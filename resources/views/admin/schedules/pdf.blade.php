<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Dinas Pegawai</title>
    <style>
        @page { margin: 0.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 7px; color: #1e293b; line-height: 1.1; }
        
        .kop { 
            position: relative;
            text-align: center; 
            margin-bottom: 10px; 
            border-bottom: 1.5px solid #0f172a; 
            padding-bottom: 8px; 
        }
        .kop-logo {
            position: absolute;
            left: 10px;
            top: 0;
            width: 45px;
            height: auto;
        }
        .kop h1 { margin: 0; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #0f172a; }
        .kop h2 { margin: 1px 0; font-size: 12px; font-weight: 800; text-transform: uppercase; color: #0f172a; }
        .kop p { margin: 1px 0; font-size: 7px; color: #64748b; }
        
        .title { text-align: center; font-size: 10px; font-weight: bold; margin: 10px 0; text-transform: uppercase; text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 0.5px solid #94a3b8; padding: 2px 1px; text-align: center; overflow: hidden; }
        th { background-color: #f1f5f9; font-weight: bold; color: #1e293b; font-size: 6px; }
        
        .no-col { width: 15px; }
        .emp-name { text-align: left; padding-left: 3px; font-weight: bold; width: 100px; font-size: 6.5px; }
        .nip-col { width: 65px; font-size: 6px; }
        
        .pagi { background-color: #ecfdf5; color: #065f46; font-weight: bold; }
        .siang { background-color: #fffbeb; color: #92400e; font-weight: bold; }
        .malam { background-color: #f8fafc; color: #1e293b; font-weight: bold; border: 1px solid #1e293b !important; }
        .kantor { background-color: #eff6ff; color: #1e40af; font-weight: bold; }

        .footer { margin-top: 20px; width: 100%; }
        .footer-table { width: 100%; border: none; }
        .footer-table td { border: none; padding: 0; text-align: right; font-size: 8px; }
        .signature-space { height: 40px; }
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
            <h1>{{ \App\Models\Setting::getValue('kop_line_1', 'KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN RI') }}</h1>
            <h2>{{ \App\Models\Setting::getValue('kop_line_2', 'KANTOR WILAYAH KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN') }}</h2>
            <p>{{ \App\Models\Setting::getValue('kop_address', 'LEMBAGA PEMASYARAKATAN KELAS IIB JOMBANG') }}</p>
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
                <td class="no-col">{{ $index + 1 }}</td>
                <td class="emp-name">{{ $emp->full_name }}</td>
                <td class="nip-col">{{ $emp->nip }}</td>
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

    <div style="margin-top: 10px; font-size: 6px; color: #475569;">
        <strong>Keterangan:</strong> P = Pagi, S = Siang, M = Malam (Border Hitam), K = Kantor, - = Libur/Lepas
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <p>Jombang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p style="font-weight: bold; margin-top: 5px;">KEPALA KESATUAN PENGAMANAN</p>
                    <div class="signature-space"></div>
                    <p>( __________________________ )</p>
                    <p style="font-size: 5px; color: #94a3b8; margin-top: 5px;">Sinergi PAS System Digital Archive</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
