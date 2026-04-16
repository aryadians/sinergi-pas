<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Regu Jaga</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 14px; text-transform: uppercase; }
        .header h3 { margin: 5px 0 0 0; font-size: 16px; text-transform: uppercase; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h4 { margin: 0; font-size: 12px; text-decoration: underline; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 4px 2px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8px; }
        
        .shift-name { font-weight: bold; text-align: left; padding-left: 5px; width: 80px; }
        .weekend { background-color: #ffebee; }
        .regu-box { font-weight: bold; color: #1e40af; }
        
        .footer { margin-top: 30px; }
        .footer-table { border: none; }
        .footer-table td { border: none; text-align: center; padding: 0; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $kop1 }}</h2>
        <h3>{{ $kop2 }}</h3>
    </div>

    <div class="title">
        <h4>JADWAL TUGAS REGU JAGA PERIODE {{ strtoupper($date->translatedFormat('F Y')) }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 60px;">SESI</th>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    <th class="{{ $date->copy()->day($d)->isWeekend() ? 'weekend' : '' }}">
                        {{ $d }}<br>
                        <span style="font-size: 7px;">{{ strtoupper($date->copy()->day($d)->translatedFormat('D')) }}</span>
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($shifts as $shift)
            <tr>
                <td class="shift-name">{{ $shift->name }}</td>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php 
                        $dateStr = $date->copy()->day($d)->format('Y-m-d');
                        $current = $schedules->get($dateStr . '_' . $shift->id)?->first();
                    @endphp
                    <td class="{{ $date->copy()->day($d)->isWeekend() ? 'weekend' : '' }}">
                        <span class="regu-box">{{ $current ? $current->squad->name : '-' }}</span>
                    </td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width: 60%;"></td>
                <td>
                    Jombang, {{ now()->translatedFormat('d F Y') }}<br>
                    Kepala Kesatuan Pengamanan,<br>
                    <div class="signature-space"></div>
                    <strong>( ........................................... )</strong><br>
                    NIP. ...........................................
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
