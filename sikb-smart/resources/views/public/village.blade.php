@extends('layouts.public')

@section('title', $village)

@section('content')
<div class="detail-wrap">
    <a class="back-link" href="{{ route('home', ['periode' => $period, 'seksi' => $section]) }}">
        <svg viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M14.5 9h-11m0 0 4.3-4.3M3.5 9l4.3 4.3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Kembali ke ringkasan
    </a>

    <section class="detail-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-line"></span> PROFIL WILAYAH · KAMPUNG KB</div>
            <h1>{{ $village }}</h1>
            <p>Ringkasan kegiatan dan kolaborasi mitra dalam {{ $period }} bulan terakhir.</p>
        </div>
        <span class="detail-period">{{ $period }} BULAN</span>
    </section>

    @if ($section)
        <div class="active-filter"><span>Filter seksi:</span><strong>{{ $section }}</strong></div>
    @endif

    <section class="summary-grid detail-summary">
        <article class="summary-card summary-primary">
            <div class="summary-topline"><span>Total kegiatan</span><span class="summary-symbol">01</span></div>
            <div class="summary-number">{{ number_format($totalActivities) }}</div>
            <div class="summary-note">pada periode terpilih</div>
            <div class="summary-decoration" aria-hidden="true"></div>
        </article>
        <article class="summary-card">
            <div class="summary-topline"><span>Mitra terlibat</span>
                <span class="mini-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-8a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-7.5a4 4 0 0 1 0 7.7m2 3.3a3.5 3.5 0 0 1 2 3.2V20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </span>
            </div>
            <div class="summary-number">{{ number_format($partners->count()) }}</div>
            <div class="summary-note">instansi unik dari seluruh kegiatan</div>
        </article>
        <article class="summary-card detail-dominant">
            <div class="summary-topline"><span>Seksi dominan</span><span class="mini-icon">02</span></div>
            <div class="dominant-label">{{ $dominantSection }}</div>
            <div class="summary-note">berdasarkan jumlah kegiatan</div>
        </article>
    </section>

    <div class="detail-columns">
        <section class="panel detail-panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">KOMPOSISI PROGRAM</span>
                    <h2>Rincian per seksi</h2>
                </div>
            </div>
            <div class="section-breakdown">
                @foreach ($sections as $item)
                    <div class="breakdown-row">
                        <span class="breakdown-name">{{ $item['nama'] }}</span>
                        <span class="breakdown-count">{{ number_format($item['jumlah']) }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="panel detail-panel partners-panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">KOLABORASI</span>
                    <h2>Instansi mitra</h2>
                </div>
                <span class="partner-count">{{ $partners->count() }} unik</span>
            </div>
            @if ($partners->isNotEmpty())
                <ul class="partner-list">
                    @foreach ($partners as $partner)
                        <li><span class="partner-check">✓</span>{{ $partner }}</li>
                    @endforeach
                </ul>
            @else
                <p class="empty-copy">Belum ada data instansi mitra pada periode ini.</p>
            @endif
            <p class="privacy-note">Ringkasan ini hanya menampilkan data agregat, bukan data pribadi petugas atau peserta.</p>
        </section>
    </div>
</div>
@endsection