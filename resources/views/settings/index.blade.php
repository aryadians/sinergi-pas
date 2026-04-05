@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('header-title', 'Konfigurasi Platform')

@section('content')
<style>
    .settings-nav-link { 
        @apply flex items-center gap-3 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all border border-transparent;
    }
    .settings-nav-link.active {
        @apply bg-[#1E2432] text-white shadow-xl border-[#1E2432];
    }
    .settings-nav-link:not(.active) {
        @apply text-[#8A8A8A] hover:bg-white hover:border-[#EFEFEF] hover:text-[#1E2432];
    }
    .config-card {
        @apply bg-white rounded-[40px] border border-[#EFEFEF] shadow-sm overflow-hidden flex flex-col;
    }
    .input-field {
        @apply w-full px-6 py-4 rounded-2xl border border-[#EFEFEF] bg-[#FCFBF9] text-sm font-bold text-[#1E2432] outline-none focus:border-[#E85A4F] focus:ring-4 focus:ring-red-500/5 transition-all;
    }
    .label-caps {
        @apply text-[9px] font-black text-[#8A8A8A] uppercase tracking-[0.2em] ml-1 mb-2 block;
    }
    .master-item {
        @apply flex justify-between items-center p-4 bg-[#FCFBF9] rounded-xl border border-[#EFEFEF] group transition-all hover:bg-white hover:border-[#E85A4F] hover:shadow-md;
    }
</style>

<div class="max-w-6xl mx-auto pb-24">
    <!-- Header Nav -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-8 mb-12">
        <div class="flex bg-[#F5F4F2] p-1.5 rounded-[24px] border border-[#EFEFEF] shadow-inner">
            <a href="#general" class="settings-nav-link active">Umum</a>
            <a href="#broadcast" class="settings-nav-link">Siaran</a>
            <a href="#master" class="settings-nav-link">Master</a>
        </div>
        
        <a href="{{ route('settings.health') }}" class="bg-white border border-[#EFEFEF] px-8 py-4 rounded-[24px] text-[10px] font-black uppercase tracking-widest text-[#1E2432] hover:bg-[#1E2432] hover:text-white transition-all shadow-sm flex items-center gap-3 group">
            <i data-lucide="heart-pulse" class="w-4 h-4 text-[#E85A4F]"></i> 
            Kesehatan Sistem 
            <i data-lucide="arrow-right" class="w-3 h-3 group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>

    <!-- General Settings Form -->
    <form action="{{ route('settings.update') }}" method="POST" class="space-y-10" id="general">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Widgets -->
            <div class="lg:col-span-2 config-card">
                <div class="p-8 border-b border-[#F5F4F2] bg-[#FCFBF9]/50 flex items-center gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-[#EFEFEF] text-[#1E2432]">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-black text-[#1E2432] uppercase tracking-widest">Modul Dashboard</h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $widgetsList = [
                                ['key' => 'widget_stats', 'label' => 'Statistik Utama', 'icon' => 'bar-chart-3'],
                                ['key' => 'widget_employees', 'label' => 'Status Unit Kerja', 'icon' => 'users'],
                                ['key' => 'widget_chart' , 'label' => 'Grafik Distribusi', 'icon' => 'pie-chart'],
                                ['key' => 'widget_activity', 'label' => 'Aktivitas Terkini', 'icon' => 'activity'],
                                ['key' => 'widget_compliance', 'label' => 'Status Kepatuhan', 'icon' => 'shield-check'],
                                ['key' => 'widget_feed', 'label' => 'Antrean Berkas', 'icon' => 'zap'],
                            ];
                        @endphp
                        @foreach($widgetsList as $w)
                        <label class="flex items-center justify-between p-4 rounded-xl border border-[#EFEFEF] hover:bg-[#FCFBF9] transition-all cursor-pointer group">
                            <div class="flex items-center gap-4">
                                <i data-lucide="{{ $w['icon'] }}" class="w-4 h-4 text-[#ABABAB] group-hover:text-[#E85A4F]"></i>
                                <span class="text-[10px] font-black text-[#1E2432] uppercase tracking-tighter">{{ $w['label'] }}</span>
                            </div>
                            <div class="relative inline-flex items-center">
                                <input type="hidden" name="{{ $w['key'] }}" value="off">
                                <input type="checkbox" name="{{ $w['key'] }}" value="on" class="sr-only peer" {{ ($settings[$w['key']] ?? 'on') == 'on' ? 'checked' : '' }}>
                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#E85A4F]"></div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Watermark -->
            <div class="config-card">
                <div class="p-8 border-b border-[#F5F4F2] bg-[#FCFBF9]/50 flex items-center gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-[#EFEFEF] text-yellow-600">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-black text-[#1E2432] uppercase tracking-widest">Keamanan</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div>
                        <span class="label-caps">Watermarking</span>
                        <select name="watermark_enabled" class="input-field py-3 text-xs uppercase">
                            <option value="on" {{ ($settings['watermark_enabled'] ?? 'on') == 'on' ? 'selected' : '' }}>AKTIF</option>
                            <option value="off" {{ ($settings['watermark_enabled'] ?? 'on') == 'off' ? 'selected' : '' }}>NONAKTIF</option>
                        </select>
                    </div>
                    <div>
                        <span class="label-caps">Teks Pengaman</span>
                        <input type="text" name="watermark_text" value="{{ $settings['watermark_text'] ?? 'SINERGI PAS JOMBANG' }}" class="input-field py-3 text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- Aligned Row: Visual & Identity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="config-card">
                <div class="p-8 border-b border-[#F5F4F2] bg-[#FCFBF9]/50 flex items-center gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-[#EFEFEF] text-blue-600">
                        <i data-lucide="monitor" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-black text-[#1E2432] uppercase tracking-widest">Running Banner</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="label-caps">Latar</span>
                            <input type="color" name="running_text_bg" value="{{ $settings['running_text_bg'] ?? '#1E2432' }}" class="w-full h-10 rounded-lg border border-[#EFEFEF] bg-white p-1 cursor-pointer">
                        </div>
                        <div>
                            <span class="label-caps">Teks</span>
                            <input type="color" name="running_text_color" value="{{ $settings['running_text_color'] ?? '#FFFFFF' }}" class="w-full h-10 rounded-lg border border-[#EFEFEF] bg-white p-1 cursor-pointer">
                        </div>
                    </div>
                    <div>
                        <span class="label-caps">Kecepatan (Detik)</span>
                        <input type="number" name="running_text_speed" value="{{ $settings['running_text_speed'] ?? '20' }}" class="input-field py-3 text-xs">
                    </div>
                </div>
            </div>

            <div class="config-card">
                <div class="p-8 border-b border-[#F5F4F2] bg-[#FCFBF9]/50 flex items-center gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-[#EFEFEF] text-purple-600">
                        <i data-lucide="building" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-black text-[#1E2432] uppercase tracking-widest">Detail Instansi</h3>
                </div>
                <div class="p-8 space-y-4">
                    <div>
                        <span class="label-caps">Baris Utama Kop</span>
                        <input type="text" name="kop_line_1" value="{{ $settings['kop_line_1'] ?? 'LEMBAGA PEMASYARAKATAN JOMBANG' }}" class="input-field py-3 text-xs">
                    </div>
                    <div>
                        <span class="label-caps">Baris Kedua Kop</span>
                        <input type="text" name="kop_line_2" value="{{ $settings['kop_line_2'] ?? 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM JAWA TIMUR' }}" class="input-field py-3 text-xs">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="bg-[#1E2432] text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-[#E85A4F] transition-all shadow-xl active:scale-95 flex items-center gap-3">
            <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan Utama
        </button>
    </form>

    <!-- Broadcast Section -->
    <div class="mt-24 pt-20 border-t border-[#EFEFEF]" id="broadcast">
        <div class="flex items-center gap-4 mb-10">
            <div class="w-1.5 h-8 bg-[#E85A4F] rounded-full"></div>
            <h3 class="text-xl font-black text-[#1E2432] uppercase tracking-tight">Siaran Pengumuman</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="config-card h-fit">
                <div class="p-8">
                    <form action="{{ route('announcements.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <span class="label-caps">Pesan</span>
                            <textarea name="message" rows="4" required class="input-field py-4 text-xs" placeholder="Tulis pesan resmi..."></textarea>
                        </div>
                        <div>
                            <span class="label-caps">Tipe</span>
                            <select name="type" class="input-field py-3 text-xs appearance-none">
                                <option value="banner">RUNNING TEXT</option>
                                <option value="popup">POPUP MODAL</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="label-caps">Mulai</span>
                                <input type="datetime-local" name="starts_at" class="input-field py-3 !px-2 !text-[9px]">
                            </div>
                            <div>
                                <span class="label-caps">Selesai</span>
                                <input type="datetime-local" name="expires_at" class="input-field py-3 !px-2 !text-[9px]">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-[#E85A4F] text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-[#1E2432] transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i> Publikasikan
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                @forelse(\App\Models\Announcement::latest()->get() as $ann)
                <div class="bg-white p-6 rounded-3xl border border-[#EFEFEF] group flex justify-between items-start gap-6 hover:border-[#1E2432] transition-all">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-2.5 py-1 bg-[#F5F4F2] text-[#1E2432] text-[8px] font-black uppercase rounded-lg border border-[#EFEFEF]">{{ $ann->type }}</span>
                            @if($ann->starts_at && $ann->starts_at > now())
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-600 text-[8px] font-black uppercase rounded-lg border border-blue-100">Scheduled</span>
                            @elseif($ann->is_active && (!$ann->expires_at || $ann->expires_at > now()))
                                <span class="px-2.5 py-1 bg-green-50 text-green-600 text-[8px] font-black uppercase rounded-lg border border-green-100">Live</span>
                            @else
                                <span class="px-2.5 py-1 bg-gray-50 text-gray-400 text-[8px] font-black uppercase rounded-lg border border-gray-100">Inactive</span>
                            @endif
                        </div>
                        <p class="text-xs font-bold text-[#1E2432] leading-relaxed italic">"{{ $ann->message }}"</p>
                    </div>
                    <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all">
                        <form action="{{ route('announcements.toggle', $ann->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-9 h-9 bg-white border border-[#EFEFEF] rounded-xl flex items-center justify-center text-[#1E2432] hover:bg-[#1E2432] hover:text-white shadow-sm transition-all"><i data-lucide="{{ $ann->is_active ? 'eye-off' : 'eye' }}" class="w-4 h-4"></i></button>
                        </form>
                        <form action="{{ route('announcements.destroy', $ann->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-9 h-9 bg-white border border-[#EFEFEF] rounded-xl flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white shadow-sm transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-20 bg-[#FCFBF9] rounded-3xl border border-dashed border-[#EFEFEF] opacity-40 font-black text-[9px] uppercase tracking-widest text-[#ABABAB]">Belum ada riwayat siaran</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Master Data Section: Perfectly Balanced & Aligned -->
    <div class="mt-24 pt-20 border-t border-[#EFEFEF]" id="master">
        <div class="flex items-center gap-4 mb-10">
            <div class="w-1.5 h-8 bg-indigo-600 rounded-full"></div>
            <h3 class="text-xl font-black text-[#1E2432] uppercase tracking-tight">Manajemen Master Data</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Left: Positions -->
            <div class="config-card h-full">
                <div class="p-8 border-b border-[#F5F4F2] bg-[#FCFBF9]/50 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-[#EFEFEF] text-indigo-600 shadow-sm">
                            <i data-lucide="briefcase" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-black text-[#1E2432] uppercase tracking-widest">Master Jabatan</h4>
                    </div>
                    <span class="text-[9px] font-black bg-white px-3 py-1 rounded-full border border-[#EFEFEF] text-[#ABABAB]">{{ count($positions) }} TOTAL</span>
                </div>
                
                <div class="p-8 flex-1 flex flex-col">
                    <form action="{{ route('settings.positions.store') }}" method="POST" class="flex gap-2 mb-8">
                        @csrf
                        <input type="text" name="name" required placeholder="Tambah jabatan baru..." class="flex-1 px-5 py-3 rounded-xl border border-[#EFEFEF] bg-[#FCFBF9] text-xs font-bold text-[#1E2432] outline-none focus:border-indigo-500 transition-all">
                        <button type="submit" class="bg-indigo-600 text-white px-5 rounded-xl hover:bg-indigo-700 transition-all active:scale-90 flex items-center justify-center shadow-lg shadow-indigo-100">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </button>
                    </form>

                    <div class="space-y-2 overflow-y-auto pr-2 custom-scrollbar h-[320px]">
                        @foreach($positions as $pos)
                        <div class="flex justify-between items-center p-4 bg-[#FCFBF9] rounded-xl border border-[#EFEFEF] group hover:border-indigo-200 transition-all">
                            <span class="text-[10px] font-black text-[#1E2432] uppercase tracking-tighter">{{ $pos->name }}</span>
                            <form action="{{ route('settings.positions.destroy', $pos->id) }}" method="POST" class="no-loader">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 flex items-center justify-center text-[#ABABAB] hover:text-red-500 hover:bg-red-50 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Work Units -->
            <div class="config-card h-full">
                <div class="p-8 border-b border-[#F5F4F2] bg-[#FCFBF9]/50 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-[#EFEFEF] text-emerald-600 shadow-sm">
                            <i data-lucide="layout-grid" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-black text-[#1E2432] uppercase tracking-widest">Master Unit Kerja</h4>
                    </div>
                    <span class="text-[9px] font-black bg-white px-3 py-1 rounded-full border border-[#EFEFEF] text-[#ABABAB]">{{ count($workUnits) }} TOTAL</span>
                </div>
                
                <div class="p-8 flex-1 flex flex-col">
                    <form action="{{ route('settings.work-units.store') }}" method="POST" class="flex gap-2 mb-8">
                        @csrf
                        <input type="text" name="name" required placeholder="Tambah unit kerja baru..." class="flex-1 px-5 py-3 rounded-xl border border-[#EFEFEF] bg-[#FCFBF9] text-xs font-bold text-[#1E2432] outline-none focus:border-emerald-500 transition-all">
                        <button type="submit" class="bg-emerald-600 text-white px-5 rounded-xl hover:bg-emerald-700 transition-all active:scale-90 flex items-center justify-center shadow-lg shadow-emerald-100">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </button>
                    </form>

                    <div class="space-y-2 overflow-y-auto pr-2 custom-scrollbar h-[320px]">
                        @foreach($workUnits as $unit)
                        <div class="flex justify-between items-center p-4 bg-[#FCFBF9] rounded-xl border border-[#EFEFEF] group hover:border-emerald-200 transition-all">
                            <span class="text-[10px] font-black text-[#1E2432] uppercase tracking-tighter">{{ $unit->name }}</span>
                            <form action="{{ route('settings.work-units.destroy', $unit->id) }}" method="POST" class="no-loader">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 flex items-center justify-center text-[#ABABAB] hover:text-red-500 hover:bg-red-50 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#1E2432', customClass: { popup: 'rounded-[32px]' } });
</script>
@endif
@endsection
