@extends('layouts.app')

@section('title', 'Master Shift')
@section('header-title', 'Konfigurasi Jam Kerja')

@section('content')
<div class="max-w-5xl mx-auto page-fade">
    <div class="mb-8 flex items-center justify-between">
        <a href="{{ route('admin.attendance.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 transition-colors font-bold text-[10px] uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Absensi
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Shift Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 card-3d sticky top-24">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-blue-600"></i>
                    Tambah Shift
                </h3>
                <form action="{{ route('admin.shifts.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Shift</label>
                        <input type="text" name="name" required placeholder="Contoh: Pagi, Siang, Malam" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-blue-500 bg-slate-50 transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Jam Masuk</label>
                            <input type="time" name="start_time" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-blue-500 bg-slate-50 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Jam Pulang</label>
                            <input type="time" name="end_time" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-blue-500 bg-slate-50 transition-all">
                        </div>
                    </div>
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 cursor-pointer hover:bg-white transition-all group">
                        <input type="checkbox" name="is_next_day" value="1" class="w-5 h-5 rounded-lg border-slate-200 text-blue-600 focus:ring-0">
                        <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600">Lintas Hari (Shift Malam)</span>
                    </label>
                    <button type="submit" class="w-full py-3.5 bg-slate-900 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg btn-3d">
                        Simpan Shift Baru
                    </button>
                </form>
            </div>
        </div>

        <!-- Shift List -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest px-2">Daftar Konfigurasi Shift</h3>
            <div class="grid grid-cols-1 gap-4">
                @forelse($shifts as $shift)
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between hover:border-blue-300 transition-all card-3d group">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 flex items-center justify-center border border-slate-100 transition-all">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-900 uppercase tracking-tight">{{ $shift->name }}</h4>
                            <p class="text-xs font-semibold text-slate-500 mt-0.5">
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                @if($shift->is_next_day) <span class="text-amber-600 font-bold ml-1 italic">(+1 Hari)</span> @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="openEditShiftModal({{ json_encode($shift) }})" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST" class="no-loader">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus konfigurasi shift ini?')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all shadow-sm">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-20 bg-slate-50 rounded-[40px] border border-dashed border-slate-200">
                    <i data-lucide="clock-9" class="w-10 h-10 text-slate-200 mx-auto mb-4"></i>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum ada konfigurasi jam kerja</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Edit Shift Modal -->
<div id="editShiftModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[32px] p-10 shadow-2xl animate-in zoom-in duration-200 relative overflow-hidden">
        <h3 class="text-xl font-bold text-slate-900 mb-8 flex items-center gap-2">
            <i data-lucide="edit" class="w-5 h-5 text-blue-600"></i>
            Edit Konfigurasi Shift
        </h3>
        <form id="editShiftForm" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama Shift</label>
                <input type="text" name="name" id="edit_shift_name" required class="w-full px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 bg-slate-50 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Jam Masuk</label>
                    <input type="time" name="start_time" id="edit_shift_start" required class="w-full px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Jam Pulang</label>
                    <input type="time" name="end_time" id="edit_shift_end" required class="w-full px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 bg-slate-50 outline-none">
                </div>
            </div>
            <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 cursor-pointer hover:bg-white transition-all group">
                <input type="checkbox" name="is_next_day" id="edit_shift_next_day" value="1" class="w-5 h-5 rounded-lg border-slate-200 text-blue-600 focus:ring-0">
                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600">Lintas Hari (Shift Malam)</span>
            </label>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('editShiftModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-[2] py-4 bg-blue-600 text-white rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditShiftModal(shift) {
        const modal = document.getElementById('editShiftModal');
        const form = document.getElementById('editShiftForm');
        form.action = `/admin/shifts/${shift.id}`;
        
        document.getElementById('edit_shift_name').value = shift.name;
        document.getElementById('edit_shift_start').value = shift.start_time;
        document.getElementById('edit_shift_end').value = shift.end_time;
        document.getElementById('edit_shift_next_day').checked = shift.is_next_day == 1;
        
        modal.classList.remove('hidden');
        lucide.createIcons();
    }
</script>
@endsection
