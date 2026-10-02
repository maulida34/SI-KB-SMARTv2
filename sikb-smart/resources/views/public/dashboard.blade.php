@extends('layouts.public')

@section('title', 'Analitik Kampung KB')

@section('content')
<div class="dashboard-wrap">
    <section class="welcome-row">
        <div class="welcome-copy">
            <div class="eyebrow"><span class="eyebrow-line"></span> PUSAT ANALITIK · BANJARMASIN</div>
            <h1>Data yang mendorong<br><span>keluarga berkualitas.</span></h1>
            <p>Ringkasan kegiatan Kampung KB lintas kelurahan, program, dan kelompok peserta.</p>
        </div>
        <div class="welcome-aside">
            <span class="live-dot"></span>
            <span>DATA TERBARU</span>
            <strong>{{ now()->translatedFormat('d F Y') }}</strong>
        </div>
    </section>

    <section class="filter-bar" aria-label="Filter analitik">
        <div class="filter-heading">
            <span class="filter-icon" aria-hidden="true">
                <svg viewBox="0 0 20 20" fill="none"><path d="M3 5h14M5.5 10h9M8 15h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <span>Periode analisis</span>
        </div>
        <form method="GET" action="{{ route('home') }}" class="filter-controls">
            <div class="period-switch" role="group" aria-label="Pilih periode">
                <button class="period-option {{ $period === 6 ? 'selected' : '' }}" type="submit" name="periode" value="6">6 bulan</button>
                <button class="period-option {{ $period === 12 ? 'selected' : '' }}" type="submit" name="periode" value="12">12 bulan</button>
            </div>
            @if ($section)
                <input type="hidden" name="seksi" value="{{ $section }}">
            @endif
            @if ($sort !== 'jumlah_desc')
                <input type="hidden" name="urut" value="{{ $sort }}">
            @endif
            <a class="reset-link" href="{{ route('home') }}">
                Reset filter
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M13 8a5 5 0 1 1-1.2-3.25M13 3v3.5H9.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </form>
    </section>

    @if ($section)
        <div class="active-filter">
            <span>Filter seksi:</span>
            <strong>{{ $section }}</strong>
            <a href="{{ route('home', ['periode' => $period]) }}" aria-label="Hapus filter seksi">×</a>
        </div>
    @endif

    <section class="summary-grid" aria-label="Ringkasan kegiatan">
        <article class="summary-card summary-primary">
            <div class="summary-topline"><span>Total kegiatan</span><span class="summary-symbol">01</span></div>
            <div class="summary-number">{{ number_format($totalActivities) }}</div>
            <div class="summary-note">tercatat dalam {{ $period }} bulan terakhir</div>
            <div class="summary-decoration" aria-hidden="true"></div>
        </article>
        <article class="summary-card">
            <div class="summary-topline"><span>Kelurahan aktif</span>
                <span class="mini-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 20.5h16M6.5 20V9.5L12 5l5.5 4.5V20M9.5 20v-5.5h5V20M9 10.5h.01M15 10.5h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>
            <div class="summary-number">{{ $activeVillages }}<span class="summary-denominator">/ 6</span></div>
            <div class="summary-note">dengan setidaknya satu kegiatan</div>
            <div class="active-track" aria-label="{{ $activeVillages }} dari 6 kelurahan aktif">
                <span style="width: {{ ($activeVillages / 6) * 100 }}%"></span>
            </div>
        </article>
        <article class="summary-note-card">
            <span class="note-kicker">CARA MEMBACA</span>
            <p>Grafik seksi dapat diklik untuk memfilter seluruh ringkasan pada halaman ini.</p>
            <span class="note-arrow" aria-hidden="true">↘</span>
        </article>
    </section>

    <section class="charts-grid" aria-label="Grafik analitik">
        <article class="panel chart-panel chart-wide">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">TREN KEGIATAN</span>
                    <h2>Aktivitas dari waktu ke waktu</h2>
                </div>
                <span class="period-label">{{ $period }} BULAN</span>
            </div>
            <div class="chart-area chart-line-area"><canvas id="monthlyChart" role="img" aria-label="Grafik garis jumlah kegiatan per bulan"></canvas></div>
        </article>
        <article class="panel chart-panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">FOKUS PROGRAM</span>
                    <h2>Kegiatan per seksi</h2>
                </div>
            </div>
            <p class="panel-hint">Pilih batang untuk menerapkan filter seksi.</p>
            <div class="chart-area chart-section-area"><canvas id="sectionChart" role="img" aria-label="Grafik batang kegiatan per seksi"></canvas></div>
        </article>
        <article class="panel chart-panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">JANGKAUAN PESERTA</span>
                    <h2>Kategori peserta</h2>
                </div>
            </div>
            <div class="chart-area chart-category-area"><canvas id="categoryChart" role="img" aria-label="Grafik kategori peserta"></canvas></div>
        </article>
    </section>

    <section class="village-section">
        <div class="section-title-row">
            <div>
                <span class="panel-kicker">SEBARAN WILAYAH</span>
                <h2>Aktivitas per kelurahan</h2>
                <p>Bandingkan capaian dan buka ringkasan tiap Kampung KB.</p>
            </div>
            <form method="GET" action="{{ route('home') }}" class="sort-control">
                <label for="urut">Urutkan</label>
                <select id="urut" name="urut" onchange="this.form.submit()">
                    <option value="jumlah_desc" {{ $sort === 'jumlah_desc' ? 'selected' : '' }}>Kegiatan terbanyak</option>
                    <option value="jumlah_asc" {{ $sort === 'jumlah_asc' ? 'selected' : '' }}>Kegiatan tersedikit</option>
                    <option value="nama_asc" {{ $sort === 'nama_asc' ? 'selected' : '' }}>Nama kelurahan</option>
                </select>
                <input type="hidden" name="periode" value="{{ $period }}">
                @if ($section)<input type="hidden" name="seksi" value="{{ $section }}">@endif
            </form>
        </div>

        <div class="village-grid">
            @foreach ($villages as $village)
                <a class="village-card" href="{{ route('public.village', ['kelurahan' => $village['nama'], 'periode' => $period, 'seksi' => $section]) }}">
                    <div class="village-card-top">
                        <span class="village-pin" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="none"><path d="M15.5 8.2c0 3.7-5.5 8.3-5.5 8.3S4.5 11.9 4.5 8.2a5.5 5.5 0 1 1 11 0Z" stroke="currentColor" stroke-width="1.4"/><circle cx="10" cy="8" r="1.7" stroke="currentColor" stroke-width="1.4"/></svg>
                        </span>
                        <span class="status-badge status-{{ strtolower($village['status']) }}">{{ $village['status'] }}</span>
                    </div>
                    <h3>{{ $village['nama'] }}</h3>
                    <div class="village-total">{{ number_format($village['jumlah']) }} <span>kegiatan</span></div>
                    <div class="village-card-bottom">
                        <span>{{ number_format($village['jumlah_mitra']) }} mitra terlibat</span>
                        <span class="card-arrow" aria-hidden="true">↗</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="status-legend">
            <span><i class="legend-dot legend-high"></i>Tinggi</span>
            <span><i class="legend-dot legend-medium"></i>Sedang</span>
            <span><i class="legend-dot legend-low"></i>Rendah</span>
            <small>{{ $section ? 'Ambang status saat filter seksi: tinggi ≥5 · sedang ≥3' : 'Ambang status: tinggi ≥20 · sedang ≥12' }}</small>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    const chartInk = getComputedStyle(document.documentElement).getPropertyValue('--chart-ink').trim();
    const chartGrid = getComputedStyle(document.documentElement).getPropertyValue('--chart-grid').trim();
    const green = '#087f67';
    const mint = '#bfe8db';
    const tooltip = {
        backgroundColor: '#102d2b',
        padding: 12,
        titleFont: { family: 'DM Sans', size: 12, weight: '600' },
        bodyFont: { family: 'DM Sans', size: 12 },
        displayColors: false,
        cornerRadius: 9
    };

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: @json($chartMonths),
            datasets: [{
                data: @json($chartMonthlyCounts),
                borderColor: green,
                backgroundColor: 'rgba(8, 127, 103, .10)',
                fill: true,
                tension: .38,
                pointRadius: 3.5,
                pointHoverRadius: 6,
                pointBackgroundColor: '#fff',
                pointBorderColor: green,
                pointBorderWidth: 2,
                borderWidth: 2.5
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip },
            scales: {
                x: { grid: { display: false }, ticks: { color: chartInk, font: { family: 'DM Sans', size: 11 } } },
                y: { beginAtZero: true, border: { display: false }, grid: { color: chartGrid }, ticks: { precision: 0, color: chartInk, font: { family: 'DM Sans', size: 11 }, padding: 10 } }
            }
        }
    });

    const sectionNames = @json($sections->pluck('nama')->all());
    new Chart(document.getElementById('sectionChart'), {
        type: 'bar',
        data: {
            labels: sectionNames.map((label) => label.replace(' dan ', ' & ')),
            datasets: [{
                data: @json($sections->pluck('jumlah')->all()),
                backgroundColor: sectionNames.map((name) => @json($section) === name ? green : mint),
                borderRadius: 5,
                barThickness: 15
            }]
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            onClick: (_, elements) => {
                if (!elements.length) return;
                const index = elements[0].index;
                const url = new URL(window.location.href);
                if (url.searchParams.get('seksi') === sectionNames[index]) {
                    url.searchParams.delete('seksi');
                } else {
                    url.searchParams.set('seksi', sectionNames[index]);
                }
                window.location.href = url.toString();
            },
            plugins: {
                legend: { display: false },
                tooltip: { ...tooltip, callbacks: { title: (items) => sectionNames[items[0].dataIndex] } }
            },
            scales: {
                x: { beginAtZero: true, border: { display: false }, grid: { color: chartGrid }, ticks: { precision: 0, color: chartInk, font: { family: 'DM Sans', size: 10 } } },
                y: { border: { display: false }, grid: { display: false }, ticks: { color: chartInk, font: { family: 'DM Sans', size: 10 }, padding: 8 } }
            }
        }
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: @json($categories->pluck('nama')->all()),
            datasets: [{
                data: @json($categories->pluck('jumlah')->all()),
                backgroundColor: ['#087f67', '#25a185', '#69bea8', '#9ad6c5', '#bbdfd4', '#d7ebe4'],
                borderRadius: 5,
                barThickness: 16
            }]
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip },
            scales: {
                x: { beginAtZero: true, border: { display: false }, grid: { color: chartGrid }, ticks: { precision: 0, color: chartInk, font: { family: 'DM Sans', size: 10 } } },
                y: { border: { display: false }, grid: { display: false }, ticks: { color: chartInk, font: { family: 'DM Sans', size: 11 } } }
            }
        }
    });
</script>
@endpush