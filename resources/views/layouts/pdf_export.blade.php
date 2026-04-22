<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laporan Sinergi PAS')</title>
    <style>
        @page { margin: 1cm 1.5cm; }
        body { 
            font-family: sans-serif; 
            font-size: 10px; 
            color: #1e293b; 
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        
        .header-container {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .logo-center {
            margin: 0 auto 5px auto;
            width: 60px;
        }
        
        .kop-text h1 { 
            margin: 0; 
            font-size: 11px; 
            font-weight: bold; 
            text-transform: uppercase;
            color: #0f172a; 
        }
        
        .kop-text h2 { 
            margin: 2px 0; 
            font-size: 14px; 
            font-weight: bold; 
            text-transform: uppercase;
            color: #1e40af;
        }
        
        .kop-text p { 
            margin: 1px 0; 
            font-size: 8px; 
            color: #475569; 
        }

        .report-title-box {
            text-align: center;
            margin: 15px 0;
            background-color: #f1f5f9;
            padding: 8px;
        }

        .report-title-box h3 {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .report-meta {
            font-size: 8px;
            color: #64748b;
            margin-top: 3px;
        }

        table.main-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        table.main-table th { 
            background-color: #1e293b; 
            color: #ffffff; 
            padding: 6px; 
            text-align: left; 
            text-transform: uppercase;
            font-size: 8px;
            border: 1px solid #0f172a;
        }
        table.main-table td { 
            border: 1px solid #cbd5e1; 
            padding: 5px; 
            vertical-align: middle;
            font-size: 8px;
        }
        table.main-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-warning { background: #fef9c3; color: #a16207; }

        .footer { 
            margin-top: 30px;
            width: 100%;
        }
        .signature-table { width: 100%; }
        .signature-table td { width: 50%; vertical-align: top; }
        .signature-space { height: 50px; }
        
        .generated-at {
            font-size: 7px;
            color: #94a3b8;
            text-align: center;
            margin-top: 20px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header-container">
        @php
            try {
                $logoPath = public_path('logo1.png');
                if (file_exists($logoPath)) {
                    echo '<img src="data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) . '" class="logo-center">';
                }
            } catch (\Exception $e) {}
        @endphp
        <div class="kop-text">
            <h1>{{ \App\Models\Setting::getValue('kop_line_1', 'KEMENTERIAN HUKUM DAN HAK ASASI MANUSIA RI') }}</h1>
            <h2>{{ \App\Models\Setting::getValue('kop_line_2', 'LEMBAGA PEMASYARAKATAN KELAS IIB JOMBANG') }}</h2>
            <p>{{ \App\Models\Setting::getValue('kop_address', 'Jl. KH. Wahid Hasyim No. 151, Jombang, Jawa Timur 61411') }}</p>
            <p>Telepon: (0321) 861054 | Email: lpjombang@gmail.com</p>
        </div>
    </div>

    <div class="report-title-box">
        <h3>@yield('report_title')</h3>
        <div class="report-meta">@yield('report_meta')</div>
    </div>

    @yield('content')

    <div class="footer">
        <table class="signature-table">
            <tr>
                <td>@yield('footer_left')</td>
                <td class="text-right">
                    <p>Jombang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p><strong>@yield('signer_title', 'Kepala Lembaga Pemasyarakatan')</strong></p>
                    <div class="signature-space"></div>
                    <p><strong>@yield('signer_name', '__________________________')</strong></p>
                    <p>NIP. @yield('signer_nip', '..........................')</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="generated-at">
        Sinergi PAS Platform - {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
