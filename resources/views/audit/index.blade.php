@extends('layouts.app')

@section('title', 'Keamanan & Audit')
@section('header-title', 'Security Control Center')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mb-12">
    <!-- Top Users Chart -->
    <div class="bg-white p-10 rounded-[56px] border border-[#EFEFEF] shadow-sm relative overflow-hidden transition-all hover:shadow-2xl hover:shadow-gray-100/50">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <i data-lucide="shield-check" class="w-24 h-24 text-[#1E2432]"></i>
        </div>
        <h3 class="text-lg font-black text-[#1E2432] mb-10 uppercase tracking-widest text-center italic">User Paling Aktif</h3>
        <div class="relative h-[280px]">
            <canvas id="userChart"></canvas>
        </div>
        <div class="mt-8 pt-8 border-t border-[#FCFBF9] text-center">
            <p class="text-[10px] font-black text-[#8A8A8A] uppercase tracking-[0.3em]">Total Log Bulan Ini</p>
            <h4 class="text-3xl font-black text-[#1E2432] mt-2">{{ $logs->total() }}</h4>
        </div>
    </div>

    <!-- Log Table Area -->
    <div class="lg:col-span-2 bg-white rounded-[56px] border border-[#EFEFEF] shadow-sm overflow-hidden flex flex-col transition-all hover:shadow-2xl hover:shadow-gray-100/50">
        <div class="p-10 border-b border-[#EFEFEF] bg-[#FCFBF9]/50 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h3 class="text-xl font-black text-[#1E2432] tracking-tight italic">Riwayat Akses & Aktivitas</h3>
                <p class="text-[10px] font-bold text-[#8A8A8A] uppercase tracking-widest mt-1">Seluruh log terenkripsi di server secara otomatis</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="confirmClearLogs()" class="bg-red-50 text-red-600 px-8 py-4 rounded-[20px] text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-lg shadow-red-100 border border-red-100">
                    <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i> Bersihkan Log
                </button>
                <form id="clearLogsForm" action="{{ route('audit.clear') }}" method="POST" class="hidden no-loader">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>

        <div class="p-8 border-b border-[#EFEFEF] bg-white">
            <form action="{{ route('audit.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1 group">
                    <i data-lucide="search" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#8A8A8A] group-focus-within:text-[#E85A4F] transition-all"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, Aktivitas, atau Detail..." 
                        class="w-full pl-12 pr-4 py-4 rounded-[20px] border border-[#EFEFEF] bg-[#FCFBF9] text-xs font-bold text-[#1E2432] outline-none focus:ring-4 focus:ring-red-500/5 focus:border-[#E85A4F] transition-all shadow-inner">
                </div>
                <div class="relative group">
                    <i data-lucide="calendar" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#8A8A8A] group-focus-within:text-[#E85A4F] transition-all"></i>
                    <input type="date" name="date" value="{{ request('date') }}" 
                        class="w-full pl-12 pr-4 py-4 rounded-[20px] border border-[#EFEFEF] bg-[#FCFBF9] text-xs font-bold text-[#1E2432] outline-none focus:ring-4 focus:ring-red-500/5 focus:border-[#E85A4F] transition-all shadow-inner">
                </div>
                <button type="submit" class="bg-[#1E2432] text-white px-10 py-4 rounded-[20px] text-[10px] font-black uppercase tracking-widest hover:bg-[#E85A4F] transition-all shadow-xl active:scale-95">
                    Terapkan Filter
                </button>
                @if(request()->anyFilled(['search', 'date']))
                <a href="{{ route('audit.index') }}" class="flex items-center justify-center bg-gray-100 text-gray-600 px-6 py-4 rounded-[20px] text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <div class="flex-1 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#FCFBF9]">
                        <th class="px-10 py-5 text-[10px] font-black text-[#8A8A8A] uppercase tracking-[0.2em]">Entitas User</th>
                        <th class="px-10 py-5 text-[10px] font-black text-[#8A8A8A] uppercase tracking-[0.2em]">Deskripsi Aktivitas</th>
                        <th class="px-10 py-5 text-[10px] font-black text-[#8A8A8A] uppercase tracking-[0.2em]">Waktu Log</th>
                        <th class="px-10 py-5 text-[10px] font-black text-[#8A8A8A] uppercase tracking-[0.2em]">Identitas IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFEFEF]">
                    @foreach($logs as $log)
                    <tr class="hover:bg-[#FCFBF9]/50 transition-all group">
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-100 border border-[#EFEFEF] rounded-xl flex items-center justify-center text-xs font-black text-[#1E2432] shadow-sm group-hover:bg-[#E85A4F] group-hover:text-white transition-all">
                                    {{ substr($log->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[#1E2432] group-hover:text-[#E85A4F] transition-all">{{ $log->user->name }}</p>
                                    <span class="text-[8px] font-black px-2 py-0.5 rounded-md uppercase tracking-tighter {{ $log->user->role === 'superadmin' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                        {{ $log->user->role }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            <p class="text-xs text-[#1E2432] font-bold leading-relaxed mb-1">{{ $log->details }}</p>
                            <span class="text-[9px] font-black text-[#E85A4F] uppercase tracking-widest italic opacity-60">{{ str_replace('_', ' ', $log->activity) }}</span>
                        </td>
                        <td class="px-10 py-6">
                            <p class="text-[10px] font-black text-[#1E2432] mb-0.5">{{ $log->created_at->format('d M Y') }}</p>
                            <p class="text-[9px] font-bold text-[#ABABAB] uppercase">{{ $log->created_at->format('H:i:s') }} • {{ $log->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-10 py-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#FCFBF9] border border-[#EFEFEF] rounded-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                <span class="text-[10px] font-mono font-bold text-[#8A8A8A]">{{ $log->ip_address }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if($logs->isEmpty())
                    <tr>
                        <td colspan="4" class="px-10 py-24 text-center">
                            <div class="w-24 h-24 bg-[#FCFBF9] rounded-full flex items-center justify-center mx-auto mb-6 text-gray-200 shadow-inner">
                                <i data-lucide="shield-alert" class="w-12 h-12"></i>
                            </div>
                            <p class="text-xs font-black text-[#ABABAB] uppercase tracking-[0.3em] italic">Tidak ada log aktivitas ditemukan.</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="p-10 bg-[#FCFBF9]/50 border-t border-[#EFEFEF]">
            {{ $logs->links() }}
        </div>
    </div>
</div>

<script>
    function confirmClearLogs() {
        Swal.fire({
            title: 'Bersihkan Seluruh Log?',
            text: "Seluruh riwayat aktivitas akan dihapus permanen dari server.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E85A4F',
            cancelButtonColor: '#8A8A8A',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batalkan',
            customClass: { popup: 'rounded-[32px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('clearLogsForm').submit();
            }
        });
    }

    const userCtx = document.getElementById('userChart').getContext('2d');
    new Chart(userCtx, {
        type: 'polarArea',
        data: {
            labels: {!! json_encode($topDownloaders->pluck('user.name')) !!},
            datasets: [{
                data: {!! json_encode($topDownloaders->pluck('total')) !!},
                backgroundColor: [
                    'rgba(232, 90, 79, 0.85)', 
                    'rgba(30, 36, 50, 0.85)', 
                    'rgba(138, 138, 138, 0.85)', 
                    'rgba(59, 130, 246, 0.85)',
                    'rgba(16, 185, 129, 0.85)'
                ],
                borderWidth: 4,
                borderColor: '#ffffff'
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        font: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' },
                        padding: 20,
                        usePointStyle: true
                    }
                } 
            },
            scales: { r: { grid: { display: false }, ticks: { display: false } } }
        }
    });
</script>
@endsection
