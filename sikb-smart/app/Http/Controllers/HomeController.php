<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Support\KegiatanOptions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        [$period, $section, $start, $end, $records] = $this->filteredRecords($request);

        $months = collect(range(0, $period - 1))
            ->map(fn (int $offset) => $start->copy()->addMonths($offset));
        $monthlyCounts = $records
            ->groupBy(fn (Kegiatan $record) => $record->tanggal_kegiatan->format('Y-m'))
            ->map(fn (Collection $items) => $items->count());

        $sectionCounts = collect(KegiatanOptions::SECTIONS)
            ->map(fn (string $name) => [
                'nama' => $name,
                'jumlah' => $records->where('seksi_kegiatan', $name)->count(),
            ])
            ->values();

        $categoryCounts = $records
            ->groupBy(fn (Kegiatan $record) => trim($record->kategori_peserta) ?: 'Tidak diketahui')
            ->map(fn (Collection $items, string $name) => [
                'nama' => $name,
                'jumlah' => $items->count(),
            ])
            ->sortByDesc('jumlah')
            ->values();

        $villages = collect(KegiatanOptions::VILLAGES)
            ->map(function (string $name) use ($records, $section): array {
                $villageRecords = $records->where('kelurahan', $name);
                $perSection = collect(KegiatanOptions::SECTIONS)
                    ->mapWithKeys(fn (string $sectionName) => [
                        $sectionName => $villageRecords->where('seksi_kegiatan', $sectionName)->count(),
                    ]);
                $dominant = $perSection->sortDesc()->first() > 0
                    ? $perSection->sortDesc()->keys()->first()
                    : 'Belum ada kegiatan';

                return [
                    'nama' => $name,
                    'jumlah' => $villageRecords->count(),
                    'status' => $this->statusFor($villageRecords->count(), $section !== null),
                    'seksi_dominan' => $dominant,
                    'jumlah_mitra' => $this->uniquePartners($villageRecords)->count(),
                    'per_seksi' => $perSection->all(),
                ];
            });

        $sort = $request->string('urut')->value();
        $villages = match ($sort) {
            'jumlah_asc' => $villages->sortBy('jumlah')->values(),
            'nama_asc' => $villages->sortBy('nama')->values(),
            default => $villages->sortByDesc('jumlah')->values(),
        };

        return view('public.dashboard', [
            'period' => $period,
            'section' => $section,
            'sort' => $sort ?: 'jumlah_desc',
            'totalActivities' => $records->count(),
            'activeVillages' => $villages->where('jumlah', '>', 0)->count(),
            'villages' => $villages,
            'sections' => $sectionCounts,
            'categories' => $categoryCounts,
            'chartMonths' => $months->map(fn (Carbon $month) => $month->translatedFormat('M y'))->all(),
            'chartMonthlyCounts' => $months->map(
                fn (Carbon $month) => $monthlyCounts->get($month->format('Y-m'), 0),
            )->all(),
        ]);
    }

    public function village(Request $request, string $kelurahan)
    {
        $village = KegiatanOptions::normalizeVillage($kelurahan);

        abort_if($village === null, 404);

        [$period, $section, $start, $end, $records] = $this->filteredRecords($request, $village);

        $perSection = collect(KegiatanOptions::SECTIONS)
            ->map(fn (string $name) => [
                'nama' => $name,
                'jumlah' => $records->where('seksi_kegiatan', $name)->count(),
            ]);
        $dominant = $perSection->sortByDesc('jumlah')->first();

        return view('public.village', [
            'village' => $village,
            'period' => $period,
            'section' => $section,
            'start' => $start,
            'end' => $end,
            'totalActivities' => $records->count(),
            'partners' => $this->uniquePartners($records)->sort()->values(),
            'dominantSection' => $dominant && $dominant['jumlah'] > 0
                ? $dominant['nama']
                : 'Belum ada kegiatan',
            'sections' => $perSection->values(),
        ]);
    }

    /**
     * Public reports only load the aggregate-safe fields needed by the dashboard.
     *
     * @return array{int, ?string, Carbon, Carbon, Collection<int, Kegiatan>}
     */
    private function filteredRecords(Request $request, ?string $village = null): array
    {
        $period = (int) $request->query('periode', 12);
        $period = in_array($period, [6, 12], true) ? $period : 12;
        $section = KegiatanOptions::normalizeSection((string) $request->query('seksi', ''));
        $start = now()->startOfMonth()->subMonths($period - 1);
        $end = now()->endOfDay();

        $query = Kegiatan::query()
            ->whereDate('tanggal_kegiatan', '>=', $start->toDateString())
            ->whereDate('tanggal_kegiatan', '<=', $end->toDateString());

        if ($section !== null) {
            $query->where('seksi_kegiatan', $section);
        }

        if ($village !== null) {
            $query->where('kelurahan', $village);
        }

        return [
            $period,
            $section,
            $start,
            $end,
            $query->get([
                'id',
                'kelurahan',
                'tanggal_kegiatan',
                'seksi_kegiatan',
                'kategori_peserta',
                'opd_mitra',
            ]),
        ];
    }

    private function uniquePartners(Collection $records): Collection
    {
        return $records
            ->flatMap(fn (Kegiatan $record) => explode(',', (string) $record->opd_mitra))
            ->map(fn (string $partner) => trim($partner))
            ->filter()
            ->unique(fn (string $partner) => mb_strtolower($partner))
            ->values();
    }

    private function statusFor(int $count, bool $sectionFiltered): string
    {
        if ($sectionFiltered) {
            return $count >= 5 ? 'Tinggi' : ($count >= 3 ? 'Sedang' : 'Rendah');
        }

        return $count >= 20 ? 'Tinggi' : ($count >= 12 ? 'Sedang' : 'Rendah');
    }
}