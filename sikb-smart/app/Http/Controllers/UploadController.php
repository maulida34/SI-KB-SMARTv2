<?php

namespace App\Http\Controllers;

use App\Exports\TemplateKegiatanExport;
use App\Imports\KegiatanRowsImport;
use App\Models\Kegiatan;
use App\Models\UploadBatch;
use App\Support\KegiatanOptions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload.index', [
            'batches' => UploadBatch::query()
                ->with('uploader')
                ->orderByDesc('diupload_pada')
                ->get(),
            'previewRows' => session('upload_preview_rows', []),
            'previewCount' => session('upload_preview_count', 0),
            'previewErrors' => session('upload_preview_errors', []),
            'previewValid' => session('upload_preview_valid', false),
            'previewName' => session('upload_preview_name'),
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120', 'mimes:xlsx,csv'],
        ], [
            'file.max' => 'Ukuran file maksimal 5 MB.',
            'file.mimes' => 'Format file harus .xlsx atau .csv.',
        ]);

        $this->removePreviousPreview();

        $file = $request->file('file');
        $path = $file->storeAs('staging', Str::uuid().'.'.$file->getClientOriginalExtension(), 'local');

        session([
            'upload_preview_path' => $path,
            'upload_preview_name' => $file->getClientOriginalName(),
        ]);

        [$rows, $errors, $previewRows] = $this->readAndValidate($path);
        $valid = $errors === [] && $rows !== [];

        session([
            'upload_preview_rows' => array_slice($previewRows, 0, 10),
            'upload_preview_count' => count($previewRows),
            'upload_preview_errors' => $errors,
            'upload_preview_valid' => $valid,
        ]);

        if ($rows === [] && $errors === []) {
            session(['upload_preview_errors' => ['File tidak berisi baris kegiatan.']]);
        }

        return redirect()->route('upload.index')->with(
            $valid ? 'success' : 'notice',
            $valid
                ? 'Pratinjau siap. Periksa 10 baris pertama sebelum menyimpan.'
                : 'File belum dapat disimpan. Periksa kesalahan berikut.',
        );
    }

    public function save(Request $request)
    {
        $path = session('upload_preview_path');

        if (! is_string($path) || ! Storage::disk('local')->exists($path)) {
            return redirect()->route('upload.index')->withErrors([
                'file' => 'Pratinjau kedaluwarsa. Unggah ulang file Excel atau CSV.',
            ]);
        }

        [$rows, $errors, $previewRows] = $this->readAndValidate($path);

        if ($errors !== [] || $rows === []) {
            session([
                'upload_preview_rows' => array_slice($previewRows, 0, 10),
                'upload_preview_count' => count($previewRows),
                'upload_preview_errors' => $errors ?: ['File tidak berisi baris kegiatan.'],
                'upload_preview_valid' => false,
            ]);

            return redirect()->route('upload.index')->with('notice', 'Data berubah atau tidak lagi valid. Periksa pratinjau.');
        }

        $batch = DB::transaction(function () use ($request, $rows) {
            $batch = UploadBatch::create([
                'nama_file' => session('upload_preview_name', 'Unggahan kegiatan'),
                'diupload_oleh' => $request->user()->id,
                'diupload_pada' => now(),
                'jumlah_baris' => count($rows),
            ]);

            foreach ($rows as $row) {
                Kegiatan::create($row + ['upload_batch_id' => $batch->id]);
            }

            return $batch;
        });

        $this->removePreviousPreview();

        return redirect()->route('upload.index')->with(
            'success',
            number_format($batch->jumlah_baris).' baris berhasil disimpan dalam batch baru.',
        );
    }

    public function deleteBatch(UploadBatch $batch)
    {
        $batch->delete();

        return redirect()->route('upload.index')->with('success', 'Batch dan seluruh kegiatan di dalamnya telah dihapus.');
    }

    public function template()
    {
        return Excel::download(new TemplateKegiatanExport(), 'template-kegiatan-si-kb-smart.xlsx');
    }

    /**
     * @return array{list<array<string, mixed>>, list<string>, list<array<string, string>>}
     */
    private function readAndValidate(string $path): array
    {
        try {
            $sheet = Excel::toCollection(new KegiatanRowsImport(), Storage::disk('local')->path($path))->first();
        } catch (Throwable $exception) {
            report($exception);

            return [[], ['File tidak dapat dibaca. Pastikan format dan isinya sesuai template.'], []];
        }

        if ($sheet === null || $sheet->isEmpty()) {
            return [[], ['File tidak memiliki judul kolom atau baris data.'], []];
        }

        $headers = array_keys($sheet->first()->toArray());
        $missingHeaders = array_values(array_diff(KegiatanOptions::REQUIRED_IMPORT_HEADERS, $headers));

        if ($missingHeaders !== []) {
            return [
                [],
                ['Kolom wajib belum lengkap: '.implode(', ', $missingHeaders).'.'],
                $this->previewRows($sheet),
            ];
        }

        $existingKeys = Kegiatan::query()
            ->get(['kelurahan', 'judul_kegiatan', 'tanggal_kegiatan'])
            ->mapWithKeys(fn (Kegiatan $item) => [
                $this->duplicateKey(
                    $item->kelurahan,
                    $item->judul_kegiatan,
                    $item->tanggal_kegiatan->toDateString(),
                ) => true,
            ])
            ->all();

        $seen = [];
        $prepared = [];
        $preview = [];
        $errors = [];

        foreach ($sheet as $index => $sourceRow) {
            $rowNumber = (int) $index + 2;
            $row = collect($sourceRow->toArray())
                ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                ->all();

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $preview[] = collect($row)
                ->map(fn ($value) => $this->text($value))
                ->all();
            $fieldErrorCount = count($errors);
            foreach (KegiatanOptions::REQUIRED_IMPORT_HEADERS as $field) {
                if (! isset($row[$field]) || $this->isBlank($row[$field])) {
                    $errors[] = "Baris {$rowNumber}: kolom {$field} wajib diisi.";
                }
            }

            $villageValue = $this->text($row['kelurahan'] ?? '');
            $village = KegiatanOptions::normalizeVillage($villageValue);
            if ($village === null && $villageValue !== '') {
                $errors[] = "Baris {$rowNumber}: kelurahan harus salah satu dari ".implode(', ', KegiatanOptions::VILLAGES).'.';
            }

            $sectionValue = $this->text($row['seksi_kegiatan'] ?? '');
            $section = KegiatanOptions::normalizeSection($sectionValue);
            if ($section === null && $sectionValue !== '') {
                $errors[] = "Baris {$rowNumber}: seksi_kegiatan bukan salah satu dari lima seksi resmi.";
            }

            $activityDate = $this->normalizeDate($row['tanggal_kegiatan'] ?? null, false);
            if ($activityDate === null) {
                $errors[] = "Baris {$rowNumber}: tanggal_kegiatan tidak valid.";
            }

            $timestamp = $this->normalizeDate($row['timestamp'] ?? null, true);

            foreach (['dokumentasi_1', 'dokumentasi_2'] as $field) {
                $url = $this->text($row[$field] ?? '');
                if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL) === false) {
                    $errors[] = "Baris {$rowNumber}: {$field} harus berupa tautan yang valid.";
                }
            }

            if (count($errors) !== $fieldErrorCount) {
                continue;
            }

            $normalized = [
                'timestamp' => $timestamp,
                'nama_pengirim' => $this->text($row['nama_pengirim']),
                'jabatan_pengirim' => $this->text($row['jabatan_pengirim']),
                'nama_penyuluh_pembina' => $this->text($row['nama_penyuluh_pembina']),
                'kelurahan' => $village,
                'judul_kegiatan' => $this->text($row['judul_kegiatan']),
                'tanggal_kegiatan' => $activityDate,
                'lokasi_kegiatan' => $this->text($row['lokasi_kegiatan']),
                'deskripsi' => $this->text($row['deskripsi']),
                'seksi_kegiatan' => $section,
                'program_kegiatan' => $this->text($row['program_kegiatan']),
                'kategori_peserta' => $this->text($row['kategori_peserta']),
                'opd_mitra' => $this->text($row['opd_mitra']),
                'dokumentasi_1' => $this->text($row['dokumentasi_1'] ?? '') ?: null,
                'dokumentasi_2' => $this->text($row['dokumentasi_2'] ?? '') ?: null,
            ];

            $key = $this->duplicateKey($village, $normalized['judul_kegiatan'], $activityDate);
            if (isset($existingKeys[$key]) || isset($seen[$key])) {
                $errors[] = "Baris {$rowNumber}: duplikat kelurahan, judul kegiatan, dan tanggal.";
                continue;
            }

            $seen[$key] = true;
            $prepared[] = $normalized;
        }

        return [$prepared, $errors, array_slice($preview, 0, PHP_INT_MAX)];
    }

    private function normalizeDate(mixed $value, bool $includeTime): ?string
    {
        if ($this->isBlank($value)) {
            return $includeTime ? now()->toDateTimeString() : null;
        }

        try {
            $date = $value instanceof \DateTimeInterface
                ? Carbon::instance($value)
                : (is_numeric($value)
                ? Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))
                : Carbon::parse($value));

            return $includeTime ? $date->toDateTimeString() : $date->toDateString();
        } catch (Throwable) {
            return null;
        }
    }

    private function duplicateKey(string $village, string $title, string $date): string
    {
        return mb_strtolower(trim($village)).'|'.mb_strtolower(trim($title)).'|'.$date;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (! $this->isBlank($value)) {
                return false;
            }
        }

        return true;
    }

    private function isBlank(mixed $value): bool
    {
        return $value === null
            || (is_string($value) && trim($value) === '')
            || (is_numeric($value) && (string) $value === '');
    }

    private function text(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function removePreviousPreview(): void
    {
        $path = session('upload_preview_path');

        if (is_string($path)) {
            Storage::disk('local')->delete($path);
        }

        session()->forget([
            'upload_preview_path',
            'upload_preview_name',
            'upload_preview_rows',
            'upload_preview_count',
            'upload_preview_errors',
            'upload_preview_valid',
        ]);
    }

    private function previewRows(\Illuminate\Support\Collection $sheet): array
    {
        return $sheet
            ->filter(fn ($row) => ! $this->isEmptyRow($row->toArray()))
            ->take(10)
            ->map(fn ($row) => collect($row)->map(fn ($value) => $this->text($value))->all())
            ->values()
            ->all();
    }
}