@extends('layouts.app')

@section('title', 'Whistleblowing System')
@section('header-title', 'Manajemen Pengaduan (WBS)')

@section('content')
<div class="space-y-8 page-fade">
    <!-- Header Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm card-3d flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="inbox" class="w-7 h-7"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Laporan</p>
                <h3 class="text-2xl font-black text-slate-900">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm card-3d flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                <i data-lucide="clock" class="w-7 h-7"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menunggu</p>
                <h3 class="text-2xl font-black text-slate-900">{{ number_format($stats['pending']) }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm card-3d flex items-center gap-5 border-l-4 border-l-amber-500">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="search" class="w-7 h-7"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Diproses</p>
                <h3 class="text-2xl font-black text-slate-900">{{ number_format($stats['investigating']) }}</h3>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl p-6 text-white shadow-xl card-3d flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <i data-lucide="check-circle-2" class="w-24 h-24"></i>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-white/20 text-white flex items-center justify-center shrink-0 backdrop-blur-sm border border-white/20">
                <i data-lucide="shield-check" class="w-7 h-7"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest">Selesai</p>
                <h3 class="text-2xl font-black text-white">{{ number_format($stats['resolved']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-6 rounded-[32px] border border-slate-200 shadow-sm card-3d">
        <form action="{{ route('admin.wbs.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative w-full md:w-1/2">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tiket, deskripsi, atau kategori..." class="w-full pl-12 pr-4 py-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:border-red-500 transition-all">
            </div>
            
            <div class="relative w-full md:w-1/4">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i data-lucide="filter" class="w-5 h-5"></i>
                </div>
                <select name="status" class="w-full pl-12 pr-4 py-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:border-red-500 transition-all appearance-none cursor-pointer" onchange="this.form.submit()">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>

            <div class="w-full md:w-auto flex gap-2">
                <button type="submit" class="w-full md:w-auto px-8 py-4 bg-slate-900 hover:bg-red-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg active:scale-95">
                    Terapkan
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.wbs.index') }}" class="w-full md:w-auto px-5 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest transition-all flex items-center justify-center">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
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

    <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden card-3d">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tiket & Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kategori</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($reports as $report)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <p class="text-sm font-black text-slate-900">{{ $report->ticket_number }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-1">{{ $report->created_at->format('d M Y H:i') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-slate-100 text-slate-600 border border-slate-200">
                                {{ $report->category }}
                            </span>
                            @if($report->is_anonymous)
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-slate-900 text-white ml-2">Anonim</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($report->status === 'pending')
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-[9px] font-black uppercase border border-slate-200">Menunggu</span>
                            @elseif($report->status === 'investigating')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-md text-[9px] font-black uppercase border border-amber-200">Diproses</span>
                            @elseif($report->status === 'resolved')
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-md text-[9px] font-black uppercase border border-emerald-200">Selesai</span>
                            @elseif($report->status === 'rejected')
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-md text-[9px] font-black uppercase border border-red-200">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.wbs.show', $report->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum ada laporan pengaduan masuk</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="p-6 border-t border-slate-100 bg-slate-50/50">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
