<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Dinas Pegawai</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 8px; color: #1e293b; line-height: 1.2; }
        
        .kop { 
            position: relative;
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #0f172a; 
            padding-bottom: 10px; 
        }
        .kop-logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 50px;
            height: auto;
        }
        .kop h1 { margin: 0; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #0f172a; }
        .kop h2 { margin: 2px 0; font-size: 13px; font-weight: 800; text-transform: uppercase; color: #0f172a; }
        .kop p { margin: 2px 0; font-size: 7px; color: #64748b; }
        
        .title { text-align: center; font-size: 11px; font-weight: bold; margin: 15px 0; text-transform: uppercase; text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        th, td { border: 1px solid #cbd5e1; padding: 4px 2px; text-align: center; }
        th { background-color: #f8fafc; font-weight: bold; color: #475569; font-size: 7px; }
        .emp-name { text-align: left; padding-left: 5px; font-weight: bold; width: 120px; }
        .nip-col { width: 80px; }
        .no-col { width: 20px; }
        
        .pagi { background-color: #dbeafe; color: #1e40af; }
        .siang { background-color: #fef3c7; color: #92400e; }
        .malam { background-color: #e0e7ff; color: #3730a3; }
        .kantor { background-color: #dcfce7; color: #166534; }

        .footer { margin-top: 30px; }
        .footer-table { width: 100%; border: none; }
        .footer-table td { border: none; padding: 0; text-align: right; font-size: 8px; }
        .signature-space { height: 50px; }
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

    <div class="title">JADWAL DINAS PEGAWAI PERIODE {{ strtoupper($date->translatedFormat('F Y')) }}</div>

    <table>
        <thead>
            <tr>
                <th class="no-col" rowspan="2">NO</th>
                <th class="emp-name" rowspan="2">NAMA PEGAWAI</th>
                <th class="nip-col" rowspan="2">NIP</th>
                <th colspan="{{ $daysInMonth }}">TANGGAL</th>
            </tr>
            <tr>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    <th>{{ $d }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $index => $emp)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="emp-name">{{ $emp->full_name }}</td>
                <td style="font-family: monospace;">{{ $emp->nip }}</td>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php 
                        $dateStr = $date->copy()->day($d)->format('Y-m-d');
                        $sched = $schedules->get($emp->id)?->firstWhere('date', $dateStr);
                        $shiftName = $sched?->shift?->name;
                        $class = '';
                        if($shiftName == 'Pagi') $class = 'pagi';
                        elseif($shiftName == 'Siang') $class = 'siang';
                        elseif($shiftName == 'Malam') $class = 'malam';
                        elseif($shiftName == 'Kantor') $class = 'kantor';
                    @endphp
                    <td class="{{ $class }}">
                        {{ $shiftName ? substr($shiftName, 0, 1) : '-' }}
                    </td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 15px; font-size: 7px; color: #64748b;">
        <strong>Keterangan:</strong> P = Pagi, S = Siang, M = Malam, K = Kantor, - = Libur/Lepas
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <p>Jombang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p style="font-weight: bold;">KEPALA KESATUAN PENGAMANAN</p>
                    <div class="signature-space"></div>
                    <p>__________________________</p>
                    <p style="font-size: 6px; color: #94a3b8;">Sinergi PAS System Generated</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
