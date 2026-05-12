@extends('layouts.app')

@section('title', 'Pegawai Terbaik')
@section('header-title', 'Dashboard Pegawai Terbaik')

@section('content')
<div class="space-y-8 page-fade" x-data="{ activeTab: localStorage.getItem('best_employee_tab') || 'pns' }">
    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm card-3d relative overflow-hidden">
        <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
        <div class="relative z-10">
            <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Daftar Pegawai Paling Disiplin</h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Berdasarkan Kehadiran & Minim Pelanggaran</p>
        </div>
        <div class="relative z-10 w-full md:w-auto">
            <form action="{{ route('admin.best-employee.index') }}" method="GET" class="flex gap-3">
                <input type="month" name="month" value="{{ $monthStr }}" class="px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:border-amber-500 outline-none transition-all" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex p-1.5 bg-slate-100 rounded-2xl w-fit border border-slate-200 shadow-inner">
        <button onclick="switchTab('pns')" id="tab-pns" class="tab-btn px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300">
            Pegawai PNS
        </button>
        <button onclick="switchTab('cpns')" id="tab-cpns" class="tab-btn px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300">
            Pegawai CPNS
        </button>
    </div>

    <!-- Info Perhitungan -->
    <div class="bg-blue-50 border border-blue-100 p-5 rounded-2xl flex gap-4 items-start">
        <i data-lucide="info" class="w-10 h-10 text-blue-500 shrink-0 mt-1"></i>
        <div>
            <h4 class="text-sm font-black text-blue-900 uppercase tracking-widest mb-1">Cara Perhitungan Poin</h4>
            <p class="text-xs font-medium text-blue-700 leading-relaxed">
                Skor dihitung berdasarkan: 
                <span class="font-bold">+10 poin</span> per kehadiran valid, 
                <span class="font-bold">-5 poin</span> per keterlambatan, dan 
                <span class="font-bold">-2 poin</span> setiap 1% potongan Tunkin.
            </p>
        </div>
    </div>

    <!-- PNS Tab Content -->
    <div id="content-pns" class="tab-content hidden">
        @include('admin.best-employee.partials.ranking', ['employees' => $pnsRanked])
    </div>

    <!-- CPNS Tab Content -->
    <div id="content-cpns" class="tab-content hidden">
        @include('admin.best-employee.partials.ranking', ['employees' => $cpnsRanked])
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        // Save to localStorage
        localStorage.setItem('best_employee_tab', tab);
        
        // Update Buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-900', 'shadow-sm', 'border-slate-100');
            btn.classList.add('text-slate-500', 'hover:text-slate-700');
        });
        
        const activeBtn = document.getElementById('tab-' + tab);
        activeBtn.classList.remove('text-slate-500', 'hover:text-slate-700');
        activeBtn.classList.add('bg-white', 'text-slate-900', 'shadow-sm', 'border', 'border-slate-100');
        
        // Update Content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById('content-' + tab).classList.remove('hidden');
    }

    // Initial state
    document.addEventListener('DOMContentLoaded', () => {
        const initialTab = localStorage.getItem('best_employee_tab') || 'pns';
        switchTab(initialTab);
    });
</script>
@endpush
@endsection
