@extends('layouts.app')

@section('title', 'Kesehatan Sistem')
@section('header-title', 'System Health Monitor')

@section('content')
<div class="max-w-6xl mx-auto pb-24 page-fade">
    <!-- Header Summary -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-12">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-slate-900 rounded-[24px] flex items-center justify-center text-white shadow-xl">
                <i data-lucide="activity" class="w-8 h-8"></i>
            </div>
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight italic">Status Infrastruktur</h2>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mt-1">Monitoring Performa & Stabilitas Realtime</p>
            </div>
        </div>
        <div class="flex items-center gap-4 bg-white px-8 py-4 rounded-[24px] border border-slate-200 shadow-sm card-3d">
            <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
            <span class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em]">Sistem Operasional Optimal</span>
        </div>
    </div>

    <!-- Core Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Database Health -->
        <div class="bg-white p-10 rounded-[40px] border border-slate-200 shadow-sm card-3d flex flex-col justify-between h-[340px] relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <i data-lucide="database" class="w-32 h-32"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-10">Database Engine</p>
                <h4 class="text-4xl font-black text-slate-900 tracking-tighter">{{ $dbStatus }}</h4>
                <p class="text-[11px] font-bold text-slate-400 mt-2 uppercase tracking-widest">Konektivitas MySQL Stable</p>
            </div>
            <div class="pt-8 border-t border-slate-100 flex items-center justify-between relative z-10">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-slate-400 uppercase">Ukuran Data</span>
                    <span class="text-lg font-black text-slate-900">{{ number_format($dbSize, 2) }} MB</span>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 border border-green-100">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Storage Info -->
        <div class="bg-slate-900 p-10 rounded-[40px] shadow-2xl flex flex-col justify-between h-[340px] relative overflow-hidden group text-white card-3d border border-slate-800">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:rotate-12 transition-transform duration-700">
                <i data-lucide="hard-drive" class="w-32 h-32"></i>
            </div>
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-blue-600/20 blur-[80px] transition-all duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] mb-10">Total Volume Arsip</p>
                <h4 class="text-4xl font-black tracking-tighter">{{ $storageUsed }} <span class="text-lg text-slate-500">MB</span></h4>
                <p class="text-[11px] font-bold text-slate-400 mt-2 uppercase tracking-widest">Akumulasi Berkas Digital Terarsip</p>
            </div>
            <div class="pt-8 text-[9px] font-black text-slate-500 uppercase tracking-widest relative z-10 border-t border-slate-800">
                Storage Disk: Local Private
            </div>
        </div>

        <!-- App Environment -->
        <div class="bg-white p-10 rounded-[40px] border border-slate-200 shadow-sm card-3d flex flex-col h-[340px] relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <i data-lucide="cpu" class="w-32 h-32"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-8 relative z-10">Runtime Info</p>
            <div class="space-y-4 flex-1 overflow-y-auto custom-scrollbar pr-2 relative z-10">
                @foreach($envInfo as $key => $val)
                <div class="flex justify-between items-center pb-4 border-b border-slate-50 last:border-0 last:pb-0">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $key }}</span>
                    <span class="text-[10px] font-bold text-slate-700 bg-slate-50 px-3 py-1 rounded-lg border border-slate-100">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Error Monitoring Feed -->
    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden card-3d relative">
        <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-xl font-black text-slate-900 italic flex items-center gap-3">
                    <i data-lucide="terminal-square" class="w-6 h-6 text-slate-400"></i>
                    Log Kejadian Sistem
                </h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-2 ml-9">Daftar Log Error & Warning Laravel Terkini</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="bg-white px-4 py-2 rounded-2xl border border-slate-200 text-[9px] font-black uppercase tracking-widest text-slate-500 shadow-sm">Log: laravel.log</span>
            </div>
        </div>
        <div class="p-8">
            <div class="bg-slate-900 rounded-[32px] p-8 font-mono text-[11px] leading-relaxed text-blue-200 overflow-x-auto shadow-inner max-h-[500px] custom-scrollbar border border-slate-800 relative">
                <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:20px_20px] opacity-10 pointer-events-none"></div>
                <div class="relative z-10">
                    @forelse($recentLogs as $log)
                        <div class="mb-4 pb-4 border-b border-white/5 last:border-0 hover:bg-white/5 transition-colors p-2 rounded-lg -mx-2">
                            <span class="text-amber-400 font-black">[{{ now()->format('Y-m-d H:i:s') }}]</span>
                            <span class="ml-2 opacity-80 whitespace-pre-wrap">{{ $log }}</span>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-20 opacity-40">
                            <i data-lucide="shield-check" class="w-16 h-16 mb-6 text-emerald-400"></i>
                            <p class="text-sm font-black uppercase tracking-[0.4em] text-white">Zero Critical Errors Detected</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
