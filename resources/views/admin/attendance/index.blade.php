@extends('layouts.app')

@section('title', 'Manajemen Kehadiran')
@section('header-title', 'Absensi & Uang Makan')

@section('content')
<!-- Custom Loading Overlay for Import -->
<div id="importLoading" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-md">
    <div class="bg-white rounded-[32px] p-10 shadow-2xl max-w-sm w-full text-center animate-in zoom-in duration-300">
        <div class="relative w-24 h-24 mx-auto mb-6">
            <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i data-lucide="fingerprint" class="w-10 h-10 text-blue-600 animate-pulse"></i>
            </div>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Sinkronisasi Data</h3>
        <p class="text-sm text-slate-500 font-medium leading-relaxed">Mohon tunggu sebentar, sistem sedang memproses data absensi dari mesin...</p>
        <div class="mt-8 flex justify-center gap-1">
            <span class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
            <span class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
            <span class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
        </div>
    </div>
</div>

<div class="space-y-8 page-fade">
    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm card-3d flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                <i data-lucide="user-check" class="w-7 h-7"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Hadir (Bulan Ini)</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ number_format($summary['total_present']) }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm card-3d flex items-center gap-5 border-l-4 border-l-amber-500">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="clock-alert" class="w-7 h-7"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Keterlambatan</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ number_format($summary['total_late']) }} Kali</h3>
            </div>
        </div>
        <div class="bg-slate-900 rounded-3xl p-6 text-white shadow-xl card-3d flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <i data-lucide="banknote" class="w-20 h-20"></i>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-white/10 text-amber-400 flex items-center justify-center shrink-0 backdrop-blur-sm border border-white/10">
                <i data-lucide="wallet" class="w-7 h-7"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total Uang Makan</p>
                <h3 class="text-2xl font-bold text-white">Rp {{ number_format($summary['total_allowance'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Actions & Filters -->
    <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
        <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-2 w-full lg:flex-1">
            <form action="{{ route('admin.attendance.index') }}" method="GET" class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-2">
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" onchange="this.form.submit()" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-transparent bg-slate-50 text-sm font-semibold outline-none focus:bg-white focus:border-blue-500 transition-all">
                </div>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIP atau Nama..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-transparent bg-slate-50 text-sm font-semibold outline-none focus:bg-white focus:border-blue-500 transition-all">
                </div>
            </form>
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto">
            <div class="flex bg-white/50 p-1 rounded-2xl border border-slate-200 shadow-sm backdrop-blur-sm">
                <a href="{{ route('admin.attendance.export', ['month' => request('month'), 'type' => 'pdf']) }}" class="px-5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-100 transition-all flex items-center gap-2 no-loader">
                    <i data-lucide="file-text" class="w-4 h-4 text-red-500"></i> PDF
                </a>
                <a href="{{ route('admin.attendance.export', ['month' => request('month'), 'type' => 'excel']) }}" class="px-5 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-100 transition-all flex items-center gap-2 no-loader">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-green-500"></i> EXCEL
                </a>
            </div>
            <a href="{{ route('admin.shifts.index') }}" class="flex-1 lg:flex-none px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-[10px] uppercase tracking-wider hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i> Master Shift
            </a>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="flex-1 lg:flex-none px-6 py-3 rounded-xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-wider hover:bg-blue-600 transition-all shadow-lg btn-3d flex items-center justify-center gap-2">
                <i data-lucide="upload-cloud" class="w-4 h-4"></i> Import Fingerprint
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden card-3d">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pegawai</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Jam Masuk</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Jam Pulang</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Uang Makan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-bold border border-slate-200 shrink-0 overflow-hidden">
                                    @if($att->employee->photo)
                                        <img src="{{ $att->employee->photo }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($att->employee->full_name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">{{ $att->employee->full_name }}</p>
                                    <p class="text-[10px] font-mono font-bold text-slate-400">NIP. {{ $att->employee->nip }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold {{ $att->late_minutes > 0 ? 'text-red-500' : 'text-slate-900' }}">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '--:--' }}
                            </span>
                            @if($att->late_minutes > 0)
                                <p class="text-[8px] font-bold text-red-400 uppercase mt-0.5">Telat {{ $att->late_minutes }}m</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-slate-900">
                                {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '--:--' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider border {{ $att->status === 'present' ? 'bg-green-50 text-green-600 border-green-100' : ($att->status === 'late' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                                {{ $att->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <p class="text-sm font-bold text-slate-900">Rp {{ number_format($att->allowance_amount, 0, ',', '.') }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Gol. {{ $att->employee->rank_class ?? '-' }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-dashed border-slate-200">
                                <i data-lucide="fingerprint" class="w-8 h-8 text-slate-300"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest italic">Data absensi belum tersedia</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
        <div class="p-6 border-t border-slate-100 bg-slate-50/30">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[32px] p-10 shadow-2xl animate-in zoom-in duration-200 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight">Impor Data Fingerprint</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Sinkronisasi Absensi Mesin</p>
                </div>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="mb-8 p-5 bg-amber-50 rounded-2xl border border-amber-100">
                <h4 class="text-[10px] font-bold text-amber-800 uppercase tracking-widest mb-2">Petunjuk Header Excel:</h4>
                <p class="text-[10px] font-semibold text-amber-700 leading-relaxed italic">
                    Sistem akan membaca kolom ke-5 (NIP), kolom ke-2 (Tanggal), dan kolom ke-3 (Jam). Dukungan format .xls dan .xlsx dari mesin.
                </p>
            </div>

            <form id="importForm" action="{{ route('admin.attendance.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="p-8 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 text-center group hover:bg-white hover:border-blue-400 transition-all cursor-pointer relative">
                    <input type="file" name="file" required class="absolute inset-0 opacity-0 cursor-pointer" onchange="updateFileName(this)">
                    <i data-lucide="file-spreadsheet" class="w-10 h-10 text-slate-300 mx-auto mb-3 group-hover:text-blue-500 group-hover:scale-110 transition-all"></i>
                    <p id="fileName" class="text-xs font-bold text-slate-500 group-hover:text-blue-600">Klik untuk pilih file Excel</p>
                </div>
                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold text-sm uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl btn-3d">
                    Mulai Sinkronisasi
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('fileName').textContent = input.files[0].name;
            document.getElementById('fileName').classList.add('text-blue-600');
        }
    }

    document.getElementById('importForm').addEventListener('submit', function() {
        document.getElementById('importModal').classList.add('hidden');
        document.getElementById('importLoading').classList.remove('hidden');
        document.getElementById('importLoading').classList.add('flex');
    });
</script>
@endsection
