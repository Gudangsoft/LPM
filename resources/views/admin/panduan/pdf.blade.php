<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Buku Panduan LPM</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
    .cover { text-align: center; margin-top: 220px; }
    .cover h1 { font-size: 26px; margin-bottom: 6px; }
    .cover p { color: #555; }
    .kategori { font-size: 16px; font-weight: bold; color: #1a3c6e; margin-top: 24px; border-bottom: 2px solid #1a3c6e; padding-bottom: 4px; page-break-before: always; }
    .kategori:first-of-type { page-break-before: auto; }
    .bab { margin-top: 16px; }
    .bab h3 { font-size: 13px; margin-bottom: 6px; color: #111; }
    .bab .isi { text-align: justify; }
    .bab .isi p { margin: 0 0 8px 0; }
    .bab .isi ul, .bab .isi ol { margin: 0 0 8px 18px; padding: 0; }
    .bab .isi table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    .bab .isi table, .bab .isi th, .bab .isi td { border: 1px solid #ccc; }
    .bab .isi th, .bab .isi td { padding: 4px 6px; text-align: left; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; color: #fff; font-size: 10px; }
    .bg-secondary { background: #6c757d; }
    .bg-info { background: #0dcaf0; color: #000; }
    .bg-success { background: #198754; }
    .bg-danger { background: #dc3545; }
    .footer { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 9px; color: #888; text-align: center; }
</style>
</head>
<body>

<div class="cover">
    <h1>Buku Panduan</h1>
    <p>Sistem Informasi Lembaga Penjaminan Mutu</p>
    <p>Dicetak pada {{ now()->translatedFormat('d F Y') }}</p>
</div>

@foreach($chapters as $kategori => $bab)
<div class="kategori">{{ $kategori }}</div>
@foreach($bab as $item)
<div class="bab">
    <h3>{{ $item->judul }}</h3>
    <div class="isi">
        {!! $item->konten !!}
    </div>
</div>
@endforeach
@endforeach

</body>
</html>
