@extends('layouts.app')

@section('title', 'Whistleblowing System')
@section('header-title', 'Manajemen Pengaduan (WBS)')

@section('content')
<div class="space-y-8 page-fade">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm card-3d relative overflow-hidden">
        <div class="absolute right-0 top-0 w-32 h-32 bg-red-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
        <div class="relative z-10">
            <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Kotak Masuk Pengaduan</h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Daftar laporan pelanggaran dari pegawai</p>
        </div>
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
