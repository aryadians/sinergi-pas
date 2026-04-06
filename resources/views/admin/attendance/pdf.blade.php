<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi & Uang Makan</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        
        .kop { 
            position: relative;
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #0f172a; 
            padding-bottom: 15px; 
        }
        .kop-logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: auto;
        }
        .kop h1 { margin: 0; font-size: 13px; font-weight: bold; text-transform: uppercase; color: #0f172a; }
        .kop h2 { margin: 2px 0; font-size: 15px; font-weight: 800; text-transform: uppercase; color: #0f172a; }
        .kop p { margin: 2px 0; font-size: 8px; color: #64748b; }
        
        .title { text-align: center; font-size: 12px; font-weight: bold; margin: 25px 0; text-transform: uppercase; text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #f8fafc; font-weight: bold; color: #475569; text-transform: uppercase; font-size: 8px; text-align: center; }
        
        .footer { margin-top: 40px; }
        .footer-table { width: 100%; border: none; }
        .footer-table td { border: none; padding: 0; text-align: right; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>
    <div class="kop">
        @php
            $logoPath = public_path('logo1.png');
            $logoData = '';
            if (file_exists($logoPath)) { $logoData = base64_encode(file_get_contents($logoPath)); }
        @endphp
        @if($logoData)
            <img src="data:image/png;base64,{{ $logoData }}" class="kop-logo">
        @endif
        <div class="kop-text">
            <h1>{{ \App\Models\Setting::getValue('kop_line_1', 'KEMENTERIAN HUKUM DAN HAK ASASI MANUSIA RI') }}</h1>
            <h2>{{ \App\Models\Setting::getValue('kop_line_2', 'LEMBAGA PEMASYARAKATAN KELAS IIB JOMBANG') }}</h2>
            <p>{{ \App\Models\Setting::getValue('kop_address', 'Jl. KH. Wahid Hasyim No. 123, Jombang, Jawa Timur 61411') }}</p>
        </div>
    </div>

    <div class="title">REKAPITULASI ABSENSI & UANG MAKAN PERIODE {{ strtoupper($date->translatedFormat('F Y')) }}</div>

    <table>
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="12%">TANGGAL</th>
                <th>NAMA PEGAWAI</th>
                <th width="15%">NIP</th>
                <th width="8%">MASUK</th>
                <th width="8%">PULANG</th>
                <th width="10%">STATUS</th>
                <th width="15%">UANG MAKAN</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($attendances as $index => $a)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($a->date)->format('d/m/y') }}</td>
                <td style="font-weight: bold;">{{ $a->employee->full_name }}</td>
                <td style="font-family: monospace;">{{ $a->employee->nip }}</td>
                <td style="text-align: center;">{{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('H:i') : '-' }}</td>
                <td style="text-align: center;">{{ $a->check_out ? \Carbon\Carbon::parse($a->check_out)->format('H:i') : '-' }}</td>
                <td style="text-align: center; text-transform: uppercase; font-size: 7px;">{{ $a->status }}</td>
                <td style="text-align: right;">Rp {{ number_format($a->allowance_amount, 0, ',', '.') }}</td>
            </tr>
            @php $total += $a->allowance_amount; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="7" style="text-align: right;">TOTAL KESELURUHAN</td>
                <td style="text-align: right;">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <p style="font-size: 9px;">Jombang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p style="font-weight: bold; font-size: 9px;">ADMINISTRATOR SISTEM</p>
                    <div class="signature-space"></div>
                    <p>__________________________</p>
                    <p style="font-size: 7px; color: #94a3b8;">Sinergi PAS Automatic Report</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
