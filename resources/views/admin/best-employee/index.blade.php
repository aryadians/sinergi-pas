@extends('layouts.app')

@section('title', 'Pegawai Terbaik')
@section('header-title', 'Dashboard Pegawai Terbaik')

@section('content')
<div class="space-y-8 page-fade">
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

    @if(count($topEmployees) > 0)
        <!-- Top 3 Podium -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end pt-4 pb-8">
            <!-- Rank 2 -->
            @if(isset($topEmployees[1]))
            <div class="order-2 md:order-1 bg-white rounded-[32px] p-6 border border-slate-200 shadow-md card-3d relative overflow-hidden flex flex-col items-center text-center transform md:scale-95 md:-translate-y-4">
                <div class="absolute top-4 left-4 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-black text-sm border border-slate-200">#2</div>
                <div class="w-24 h-24 rounded-full bg-slate-100 mb-4 overflow-hidden border-4 border-white shadow-lg relative">
                    <img src="{{ $topEmployees[1]->employee->photo }}" alt="Profile" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($topEmployees[1]->employee->full_name) }}&background=F1F5F9&color=64748B'">
                </div>
                <h3 class="text-sm font-black text-slate-900 mb-1 leading-tight">{{ $topEmployees[1]->employee->full_name }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">{{ $topEmployees[1]->employee->work_unit->name ?? 'Staf' }}</p>
                <div class="w-full bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold">
                        <span class="text-slate-500">Skor</span>
                        <span class="text-slate-900">{{ $topEmployees[1]->score }} Poin</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-semibold text-slate-400">
                        <span>Hadir: {{ $topEmployees[1]->total_present }}x</span>
                        <span>Telat: {{ $topEmployees[1]->late_count }}x</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Rank 1 -->
            @if(isset($topEmployees[0]))
            <div class="order-1 md:order-2 bg-gradient-to-b from-amber-500 to-amber-600 rounded-[40px] p-8 border border-amber-400 shadow-2xl shadow-amber-500/20 card-3d relative overflow-hidden flex flex-col items-center text-center z-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                <div class="absolute -top-4 -left-4 text-amber-400/20">
                    <i data-lucide="award" class="w-32 h-32"></i>
                </div>
                <div class="absolute top-6 left-6 w-10 h-10 rounded-full bg-white text-amber-500 flex items-center justify-center font-black shadow-lg">
                    <i data-lucide="crown" class="w-5 h-5"></i>
                </div>
                
                <div class="w-32 h-32 rounded-full bg-white mb-5 overflow-hidden border-4 border-amber-300 shadow-xl relative z-10">
                    <img src="{{ $topEmployees[0]->employee->photo }}" alt="Profile" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($topEmployees[0]->employee->full_name) }}&background=FFFBEB&color=B45309'">
                </div>
                <h3 class="text-lg font-black text-white mb-1 leading-tight relative z-10">{{ $topEmployees[0]->employee->full_name }}</h3>
                <p class="text-[10px] font-bold text-amber-100 uppercase tracking-widest mb-6 relative z-10">{{ $topEmployees[0]->employee->work_unit->name ?? 'Staf' }}</p>
                <div class="w-full bg-black/10 rounded-3xl p-5 backdrop-blur-sm border border-white/10 space-y-3 relative z-10">
                    <div class="flex justify-between items-center text-sm font-black text-white">
                        <span>Skor Total</span>
                        <span class="text-xl">{{ $topEmployees[0]->score }} Poin</span>
                    </div>
                    <div class="h-px w-full bg-white/20"></div>
                    <div class="flex justify-between items-center text-[11px] font-bold text-amber-100 uppercase">
                        <span class="flex items-center gap-1"><i data-lucide="check-circle" class="w-3 h-3"></i> {{ $topEmployees[0]->total_present }} Hadir</span>
                        <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> {{ $topEmployees[0]->late_count }} Telat</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Rank 3 -->
            @if(isset($topEmployees[2]))
            <div class="order-3 md:order-3 bg-white rounded-[32px] p-6 border border-slate-200 shadow-md card-3d relative overflow-hidden flex flex-col items-center text-center transform md:scale-95 md:-translate-y-4">
                <div class="absolute top-4 right-4 w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-600 font-black text-sm border border-orange-100">#3</div>
                <div class="w-24 h-24 rounded-full bg-slate-100 mb-4 overflow-hidden border-4 border-white shadow-lg relative">
                    <img src="{{ $topEmployees[2]->employee->photo }}" alt="Profile" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($topEmployees[2]->employee->full_name) }}&background=FFF7ED&color=C2410C'">
                </div>
                <h3 class="text-sm font-black text-slate-900 mb-1 leading-tight">{{ $topEmployees[2]->employee->full_name }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">{{ $topEmployees[2]->employee->work_unit->name ?? 'Staf' }}</p>
                <div class="w-full bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold">
                        <span class="text-slate-500">Skor</span>
                        <span class="text-slate-900">{{ $topEmployees[2]->score }} Poin</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-semibold text-slate-400">
                        <span>Hadir: {{ $topEmployees[2]->total_present }}x</span>
                        <span>Telat: {{ $topEmployees[2]->late_count }}x</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- The Rest of the Top 10 -->
        @if(count($topEmployees) > 3)
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden card-3d">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                <i data-lucide="list-ordered" class="w-4 h-4 text-slate-500"></i>
                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em]">Peringkat 4 - {{ count($topEmployees) }}</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-16 text-center">Rank</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pegawai</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Kehadiran</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Keterlambatan</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Skor Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @for($i = 3; $i < count($topEmployees); $i++)
                        @php $emp = $topEmployees[$i]; @endphp
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-black text-slate-400">#{{ $i + 1 }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-bold border border-slate-200 shrink-0 overflow-hidden">
                                        <img src="{{ $emp->employee->photo }}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($emp->employee->full_name) }}&background=F1F5F9&color=64748B'">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 truncate">{{ $emp->employee->full_name }}</p>
                                        <p class="text-[10px] font-mono text-slate-400">NIP. {{ $emp->employee->nip }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-black border border-emerald-100">
                                    {{ $emp->total_present }}x
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 {{ $emp->late_count == 0 ? 'bg-slate-50 text-slate-400 border-slate-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} rounded-lg text-xs font-black border">
                                    {{ $emp->late_count }}x
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-black text-blue-600">{{ $emp->score }} Poin</span>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    @else
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-16 text-center card-3d">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Belum Ada Data</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto">Sistem memerlukan data absensi pada bulan ini untuk dapat melakukan perhitungan peringkat pegawai terbaik.</p>
        </div>
    @endif
</div>
@endsection