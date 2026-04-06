@extends('layouts.app')

@section('title', 'Master Shift')
@section('header-title', 'Konfigurasi Jam Kerja')

@section('content')
<div class="max-w-4xl mx-auto page-fade">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Add Shift Form -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 card-3d sticky top-24">
                <h3 class="text-lg font-bold text-slate-900 mb-6">Tambah Shift</h3>
                <form action="{{ route('admin.shifts.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Shift</label>
                        <input type="text" name="name" required placeholder="Contoh: Pagi, Siang, Malam" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-blue-500 bg-slate-50">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Jam Masuk</label>
                            <input type="time" name="start_time" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-blue-500 bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Jam Pulang</label>
                            <input type="time" name="end_time" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-blue-500 bg-slate-50">
                        </div>
                    </div>
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 cursor-pointer hover:bg-white transition-all group">
                        <input type="checkbox" name="is_next_day" value="1" class="w-5 h-5 rounded-lg border-slate-200 text-blue-600 focus:ring-0">
                        <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600">Lintas Hari (Shift Malam)</span>
                    </label>
                    <button type="submit" class="w-full py-3.5 bg-slate-900 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg btn-3d">
                        Simpan Shift
                    </button>
                </form>
            </div>
        </div>

        <!-- Shift List -->
        <div class="md:col-span-2 space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest px-2">Daftar Shift Aktif</h3>
            <div class="grid grid-cols-1 gap-4">
                @forelse($shifts as $shift)
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between hover:border-blue-300 transition-all card-3d">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-900 uppercase tracking-tight">{{ $shift->name }}</h4>
                            <p class="text-xs font-semibold text-slate-500 mt-0.5">
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                @if($shift->is_next_day) <span class="text-amber-600 font-bold ml-1">(+1 Hari)</span> @endif
                            </p>
                        </div>
                    </div>
                    <form action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST" class="no-loader">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus shift ini?')" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="text-center py-20 bg-slate-50 rounded-[40px] border border-dashed border-slate-200">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum ada data shift</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
