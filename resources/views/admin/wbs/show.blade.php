@extends('layouts.app')

@section('title', 'Detail Pengaduan WBS')
@section('header-title', 'Detail Laporan WBS')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 page-fade">
    
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.wbs.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-500 hover:text-red-600 hover:border-red-200 transition-colors shadow-sm">
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
    <div class="bg-emerald-50 border border-emerald-200 p-6 rounded-[24px] flex items-start gap-4 shadow-sm">
        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
            <i data-lucide="check" class="w-5 h-5"></i>
        </div>
        <div>
            <h4 class="text-sm font-black text-emerald-900">Berhasil</h4>
            <p class="text-xs font-bold text-emerald-700 mt-1">{{ session('success') }}</p>
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
                    @if($report->is_anonymous)
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest">
                            <i data-lucide="incognito" class="w-3.5 h-3.5"></i> DIRAHASIAKAN (Anonim)
                        </div>
                    @else
                        <p class="text-sm font-bold text-slate-900">{{ $report->user->employee->full_name ?? 'User' }}</p>
                        <p class="text-[10px] font-bold text-slate-400">NIP. {{ $report->user->employee->nip ?? '-' }}</p>
                    @endif
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

    <!-- Admin Response Form -->
    <div class="bg-blue-50 rounded-[32px] border border-blue-100 p-8 card-3d">
        <h3 class="text-sm font-black text-blue-900 uppercase tracking-widest mb-6 flex items-center gap-2">
            <i data-lucide="message-square" class="w-4 h-4"></i> Tindakan & Balasan Admin
        </h3>
        
        <form action="{{ route('admin.wbs.update', $report->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-blue-800 uppercase tracking-widest ml-1">Ubah Status</label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400 group-focus-within:text-blue-600 transition-colors">
                            <i data-lucide="activity" class="w-5 h-5"></i>
                        </div>
                        <select name="status" required class="w-full pl-12 pr-4 py-4 rounded-2xl border-2 border-blue-200 bg-white text-sm font-bold text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none cursor-pointer">
                            <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="investigating" {{ $report->status === 'investigating' ? 'selected' : '' }}>Sedang Diproses (Investigasi)</option>
                            <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Selesai</option>
                            <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-400 pointer-events-none">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-blue-800 uppercase tracking-widest ml-1">Pesan Balasan untuk Pelapor</label>
                <textarea name="admin_response" rows="4" placeholder="Tuliskan tindak lanjut atau balasan untuk pelapor..." class="w-full px-5 py-4 rounded-2xl border-2 border-blue-200 bg-white text-sm font-medium text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">{{ $report->admin_response }}</textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-10 py-4 rounded-2xl bg-blue-600 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-500/30 transition-all active:scale-95 flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Tindakan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
