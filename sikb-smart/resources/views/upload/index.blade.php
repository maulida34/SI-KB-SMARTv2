@extends('layouts.public')

@section('title', 'Unggah data kegiatan')

@section('content')
<div class="upload-wrap">
    <section class="detail-heading upload-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-line"></span> RUANG KERJA PETUGAS DALDUK</div>
            <h1>Unggah data kegiatan.</h1>
            <p>Impor kegiatan dari Excel atau CSV. Data diperiksa sebelum masuk ke analitik publik.</p>
        </div>
        <a class="button button-outline" href="{{ route('upload.template') }}">
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 3v9m0 0 3.2-3.2M10 12 6.8 8.8M4 13.5v2.8h12v-2.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Unduh template Excel
        </a>
    </section>

    @if (session('success'))
        <div class="notice notice-success" role="status"><span class="notice-mark">✓</span>{{ session('success') }}</div>
    @endif
    @if (session('notice'))
        <div class="notice notice-info" role="status"><span class="notice-mark">i</span>{{ session('notice') }}</div>
    @endif
    @if ($errors->any())
        <div class="notice notice-error" role="alert">
            <span class="notice-mark">!</span>
            <div><strong>Unggahan belum dapat diproses.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        </div>
    @endif

    <div class="upload-layout">
        <section class="panel upload-panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">IMPOR FILE</span>
                    <h2>Mulai dari spreadsheet</h2>
                </div>
                <span class="step-number">01</span>
            </div>
            <form method="POST" action="{{ route('upload.preview') }}" enctype="multipart/form-data" class="upload-form">
                @csrf
                <label for="file" class="dropzone">
                    <span class="upload-symbol" aria-hidden="true">
                        <svg viewBox="0 0 28 28" fill="none"><path d="M14 18V5m0 0L9.5 9.5M14 5l4.5 4.5M5 17.5v4A1.5 1.5 0 0 0 6.5 23h15a1.5 1.5 0 0 0 1.5-1.5v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <strong id="file-name">Pilih file untuk diunggah</strong>
                    <span class="dropzone-copy">.xlsx atau .csv · ukuran maksimal 5 MB</span>
                    <span class="button button-secondary">Pilih file</span>
                    <input id="file" name="file" type="file" accept=".xlsx,.csv" required>
                </label>
                <button class="button button-primary upload-submit" type="submit">
                    Tampilkan pratinjau
                    <svg viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M3.5 9h11m0 0-4-4m4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </form>
            <div class="upload-guidance">
                <span class="guidance-icon">i</span>
                <p>Gunakan judul kolom pada template. Nama kelurahan diseragamkan otomatis; kolom seksi harus sesuai daftar resmi.</p>
            </div>
        </section>

        <aside class="panel checklist-panel">
            <span class="panel-kicker">SEBELUM MENYIMPAN</span>
            <h2>Validasi otomatis</h2>
            <ul class="check-list">
                <li><span>1</span><div><strong>Kolom wajib terisi</strong><small>Setiap baris diperiksa di server.</small></div></li>
                <li><span>2</span><div><strong>Seksi resmi DPPKB</strong><small>Lima seksi kegiatan tervalidasi.</small></div></li>
                <li><span>3</span><div><strong>Tanggal kegiatan valid</strong><small>Format Excel dan tanggal umum didukung.</small></div></li>
                <li><span>4</span><div><strong>Deteksi data duplikat</strong><small>Kelurahan, judul, dan tanggal dibandingkan.</small></div></li>
            </ul>
            <a href="{{ route('upload.template') }}" class="text-link">Lihat format kolom <span aria-hidden="true">↗</span></a>
        </aside>
    </div>

    @if ($previewName)
        <section class="panel preview-panel">
            <div class="panel-heading preview-heading">
                <div>
                    <span class="panel-kicker">LANGKAH 02 · PERIKSA DATA</span>
                    <h2>Pratinjau unggahan</h2>
                    <p class="preview-file">{{ $previewName }} <span>·</span> {{ number_format($previewCount) }} baris valid untuk ditampilkan</p>
                </div>
                @if ($previewValid)
                    <form method="POST" action="{{ route('upload.save') }}">
                        @csrf
                        <button type="submit" class="button button-primary">
                            Simpan ke database
                            <svg viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M3.5 9h11m0 0-4-4m4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </form>
                @endif
            </div>

            @if (count($previewErrors))
                <div class="validation-errors">
                    <strong>Perbaiki kesalahan ini, lalu unggah ulang file:</strong>
                    <ul>@foreach ($previewErrors as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @if (count($previewRows))
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr>
                            <th>Baris</th>
                            @foreach (array_keys($previewRows[0]) as $column)
                                <th>{{ str_replace('_', ' ', $column) }}</th>
                            @endforeach
                        </tr></thead>
                        <tbody>
                            @foreach ($previewRows as $index => $row)
                                <tr>
                                    <td class="row-number">{{ $index + 2 }}</td>
                                    @foreach ($row as $value)
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="table-footnote">Menampilkan maksimal 10 baris pertama. Semua baris sudah melewati validasi sebelum tombol simpan diaktifkan.</p>
            @endif
        </section>
    @endif

    <section class="panel history-panel">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">AKTIVITAS IMPOR</span>
                <h2>Riwayat unggahan</h2>
            </div>
            <span class="history-total">{{ $batches->count() }} batch</span>
        </div>
        @if ($batches->isNotEmpty())
            <div class="table-scroll">
                <table class="data-table history-table">
                    <thead><tr><th>Nama file</th><th>Diunggah oleh</th><th>Waktu unggah</th><th>Jumlah baris</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($batches as $batch)
                            <tr>
                                <td><span class="file-chip">XLS</span><strong>{{ $batch->nama_file }}</strong></td>
                                <td>{{ $batch->uploader?->name ?? 'Petugas tidak tersedia' }}</td>
                                <td>{{ $batch->diupload_pada->translatedFormat('d M Y, H:i') }}</td>
                                <td>{{ number_format($batch->jumlah_baris) }}</td>
                                <td class="table-action">
                                    <form method="POST" action="{{ route('upload.batches.delete', $batch) }}" onsubmit="return confirm('Hapus batch ini beserta seluruh kegiatan di dalamnya? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-button">Hapus batch ini</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <span class="empty-state-mark">—</span>
                <strong>Belum ada riwayat unggahan</strong>
                <p>Batch impor yang tersimpan akan muncul di sini.</p>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('file')?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (file) document.getElementById('file-name').textContent = file.name;
    });
</script>
@endpush