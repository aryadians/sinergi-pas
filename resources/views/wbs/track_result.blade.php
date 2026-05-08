<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pelacakan WBS | SINERGI PAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="antialiased selection:bg-red-500 selection:text-white p-6 md:p-12">
    
    <div class="max-w-3xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('wbs.track') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-500 hover:text-red-600 hover:border-red-200 transition-colors shadow-sm">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight italic">Status Laporan</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Tiket: {{ $report->ticket_number }}</p>
                </div>
            </div>
            
            @if($report->status === 'pending')
                <span class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-black border border-slate-200 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4"></i> Menunggu
                </span>
            @elseif($report->status === 'investigating')
                <span class="px-4 py-2 bg-amber-100 text-amber-700 rounded-xl text-xs font-black border border-amber-200 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i> Sedang Diproses
                </span>
            @elseif($report->status === 'resolved')
                <span class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl text-xs font-black border border-emerald-200 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i> Selesai
                </span>
            @elseif($report->status === 'rejected')
                <span class="px-4 py-2 bg-red-100 text-red-700 rounded-xl text-xs font-black border border-red-200 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4"></i> Ditolak
                </span>
            @endif
        </div>

        @if(session('success'))
            <div class="p-6 bg-emerald-50 border border-emerald-200 rounded-[24px] flex items-start gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <i data-lucide="check" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-emerald-900">{{ session('success') }}</h4>
                    <p class="text-xs font-bold text-emerald-700 mt-1">Laporan Anda telah masuk ke dalam sistem dengan aman. Simpan nomor tiket <strong>{{ $report->ticket_number }}</strong> untuk memantau perkembangannya.</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i data-lucide="file-text" class="w-5 h-5 text-slate-500"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">Detail Laporan</h4>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $report->created_at->translatedFormat('d F Y H:i') }}</span>
            </div>
            <div class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</p>
                        <p class="text-sm font-bold text-slate-900">{{ $report->category }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Identitas Pelapor</p>
                        <p class="text-sm font-bold text-slate-900">{{ $report->is_anonymous ? 'DIRAHASIAKAN (Anonim)' : ($report->user->employee->full_name ?? 'User') }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Isi Laporan</p>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-sm font-medium text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $report->description }}</p>
                    </div>
                </div>

                @if($report->evidences->count() > 0)
                <div class="space-y-4 border-t border-slate-100 pt-8">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Lampiran Bukti ({{ $report->evidences->count() }})</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($report->evidences as $evidence)
                            <div class="flex items-center gap-3 p-4 border border-slate-200 rounded-2xl bg-white shadow-sm hover:border-red-300 transition-colors group">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100 group-hover:bg-red-50 group-hover:text-red-500 transition-colors">
                                    @if($evidence->file_type === 'image')
                                        <i data-lucide="image" class="w-5 h-5"></i>
                                    @elseif($evidence->file_type === 'video')
                                        <i data-lucide="video" class="w-5 h-5"></i>
                                    @elseif($evidence->file_type === 'audio')
                                        <i data-lucide="headphones" class="w-5 h-5"></i>
                                    @else
                                        <i data-lucide="file" class="w-5 h-5"></i>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $evidence->original_name ?? 'Bukti Terlampir' }}</p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase mt-0.5">{{ $evidence->file_type }}</p>
                                </div>
                                @if($evidence->file_type === 'image')
                                    <a href="{{ $evidence->file_path }}" download="bukti_{{ $loop->iteration }}.png" class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center hover:bg-slate-200 text-slate-500 transition-colors shrink-0" title="Download">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $evidence->file_path) }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center hover:bg-slate-200 text-slate-500 transition-colors shrink-0">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($report->admin_response)
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-[32px] border border-blue-100 shadow-sm p-8 card-3d relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full -mr-32 -mt-32"></div>
            <div class="relative z-10 space-y-4">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 border border-blue-200">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-blue-900 uppercase tracking-widest">Tanggapan Admin</h4>
                        <p class="text-[10px] font-bold text-blue-500 uppercase">{{ $report->updated_at->translatedFormat('d F Y H:i') }}</p>
                    </div>
                </div>
                <div class="p-6 bg-white/60 rounded-2xl border border-white">
                    <p class="text-sm font-medium text-slate-800 leading-relaxed whitespace-pre-wrap">{{ $report->admin_response }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="p-8 text-center border-2 border-dashed border-slate-200 rounded-[32px] bg-slate-50/50">
            <i data-lucide="clock" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
            <p class="text-sm font-bold text-slate-500">Laporan belum mendapat tanggapan dari Admin/Pemeriksa.</p>
        </div>
        @endif

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
