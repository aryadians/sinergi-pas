<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header img { width: 60px; position: absolute; left: 20px; top: 20px; }
        .header h2 { margin: 0; font-size: 14px; text-transform: uppercase; }
        .header h3 { margin: 5px 0 0 0; font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 20px; text-decoration: underline; }
        table { border-collapse: collapse; margin-bottom: 20px; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #f1f5f9; font-weight: bold; text-transform: uppercase; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .footer { width: 100%; margin-top: 50px; }
        .footer table { border: none; width: 100%; }
        .footer th, .footer td { border: none; text-align: center; }
        .signature { margin-top: 80px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo1.png') }}" alt="Logo">
        <h2>{{ \App\Models\Setting::getValue('kop_line_1', 'KEMENTERIAN HUKUM DAN HAM RI') }}</h2>
        <h3>{{ \App\Models\Setting::getValue('kop_line_2', 'LAPAS KELAS IIB JOMBANG') }}</h3>
    </div>

    <div class="title">{{ $reportTitle }}</div>

    <table>
        <thead>
            <tr>
                <th width="30">NO</th>
                <th class="text-left">NAMA PEGAWAI</th>
                <th>NIP</th>
                <th>TOTAL HADIR (HARI)</th>
                <th>TOTAL TELAT (MENIT)</th>
                <th class="text-right">TOTAL UANG MAKAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $item->employee->full_name }}</td>
                <td>{{ $item->employee->nip }}</td>
                <td>{{ $item->total_present }}</td>
                <td>{{ $item->total_late_minutes }}</td>
                <td class="text-right">Rp {{ number_format($item->total_allowance, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td width="70%"></td>
                <td>
                    <p>Jombang, {{ now()->translatedFormat('d F Y') }}</p>
                    <p>Kepala Lembaga Pemasyarakatan,</p>
                    <div class="signature">
                        <br><br><br><br>
                        (......................................................)
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
