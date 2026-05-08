@extends('layouts.app')

@section('title', 'Log Absensi Saya')
@section('header-title', 'Monitor Kehadiran')

@section('content')
<div class="space-y-8 page-fade">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <form action="{{ route('my.attendance') }}" method="GET" class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm w-full md:w-auto">
            <i data-lucide="calendar" class="w-4 h-4 text-slate-400 ml-2"></i>
            <input type="month" name="month" value="{{ $monthStr }}" min="2026-05" onchange="this.form.submit()" class="px-4 py-2 rounded-xl bg-slate-50 border-none text-sm font-black text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
        </form>

        <div class="px-6 py-3 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center gap-3">
            <i data-lucide="info" class="w-4 h-4"></i>
            <p class="text-[10px] font-black uppercase tracking-widest">Sinkronisasi Jadwal & Mesin Fingerprint</p>
        </div>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden card-3d">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Hari / Tanggal</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Scan Masuk</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Scan Pulang</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 text-center">Status Jadwal</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 text-right">Uang Makan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/30 transition-all group">
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-slate-900 leading-none mb-1">{{ \Carbon\Carbon::parse($log['date'])->translatedFormat('d F Y') }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($log['date'])->translatedFormat('l') }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-mono text-xs font-bold border border-slate-200">
                                <i data-lucide="log-in" class="w-3 h-3 text-slate-400"></i>
                                {{ $log['check_in'] }}
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-mono text-xs font-bold border border-slate-200">
                                <i data-lucide="log-out" class="w-3 h-3 text-slate-400"></i>
                                {{ $log['check_out'] }}
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @if($log['is_scheduled'])
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest border border-blue-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    Jadwal Valid
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 text-slate-400 text-[9px] font-black uppercase tracking-widest border border-slate-100">
                                    Luar Jadwal / Libur
                                </div>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="text-sm font-black {{ $log['meal_amount'] > 0 ? 'text-emerald-600' : 'text-slate-300' }}">
                                Rp {{ number_format($log['meal_amount'], 0, ',', '.') }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <i data-lucide="calendar-off" class="w-12 h-12 text-slate-200 mx-auto mb-4"></i>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum ada rekaman absensi untuk periode ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
