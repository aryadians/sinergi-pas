@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('header-title', 'Konfigurasi Platform')

@section('content')
<style>
    .settings-card { transition: all 0.4s ease; border: 1px solid #EFEFEF; }
    .settings-card:hover { border-color: #E85A4F; box-shadow: 0 20px 40px -12px rgba(232, 90, 79, 0.1); }
    .input-premium { background: #FCFBF9; border: 1px solid #EFEFEF; transition: all 0.3s ease; }
    .input-premium:focus { border-color: #E85A4F; box-shadow: 0 0 0 4px rgba(232, 90, 79, 0.05); background: white; }
</style>

<div class="max-w-6xl mx-auto pb-24">
    <form action="{{ route('settings.update') }}" method="POST" class="space-y-12">
        @csrf
        
        <!-- Section 1: Visual & Experience -->
        <div class="bg-white rounded-[64px] border border-[#EFEFEF] shadow-sm overflow-hidden settings-card">
            <div class="bg-[#1E2432] p-12 text-white relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                <div class="relative flex items-center gap-8">
                    <div class="w-20 h-20 bg-white/10 rounded-[32px] flex items-center justify-center border border-white/20 shadow-inner">
                        <i data-lucide="layout-template" class="w-10 h-10 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-black italic tracking-tight">Kustomisasi Antarmuka</h3>
                        <p class="text-[10px] font-black opacity-60 uppercase tracking-[0.4em] mt-2">Konfigurasi Visual & Pengalaman Pengguna</p>
                    </div>
                </div>
            </div>
            
            <div class="p-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @php
                        $widgetsList = [
                            ['key' => 'widget_stats', 'label' => 'Analitik Statistik', 'icon' => 'bar-chart-3'],
                            ['key' => 'widget_employees', 'label' => 'Status Performa Unit', 'icon' => 'users'],
                            ['key' => 'widget_chart' , 'label' => 'Grafik Distribusi', 'icon' => 'pie-chart'],
                            ['key' => 'widget_activity', 'label' => 'Log Aktivitas Realtime', 'icon' => 'activity'],
                            ['key' => 'widget_compliance', 'label' => 'Pelacakan Kepatuhan', 'icon' => 'shield-check'],
                            ['key' => 'widget_feed', 'label' => 'Antrean Verifikasi', 'icon' => 'zap'],
                        ];
                    @endphp

                    @foreach($widgetsList as $w)
                    <div class="flex items-center justify-between p-8 bg-[#FCFBF9] rounded-[40px] border border-[#EFEFEF] group transition-all hover:bg-white hover:border-[#E85A4F]">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#8A8A8A] group-hover:text-[#E85A4F] transition-all shadow-sm border border-[#EFEFEF]">
                                <i data-lucide="{{ $w['icon'] }}" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-black text-[#1E2432] uppercase tracking-tighter">{{ $w['label'] }}</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="{{ $w['key'] }}" value="off">
                            <input type="checkbox" name="{{ $w['key'] }}" value="on" class="sr-only peer" {{ ($settings[$w['key']] ?? 'on') == 'on' ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#E85A4F]"></div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Section 2: Security & Watermark -->
            <div class="bg-white rounded-[64px] p-12 flex flex-col settings-card">
                <div class="flex items-center gap-6 mb-12">
                    <div class="w-16 h-16 bg-yellow-50 rounded-[28px] flex items-center justify-center text-yellow-600 shadow-sm border border-yellow-100">
                        <i data-lucide="shield-check" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-[#1E2432] italic">Proteksi Dokumen</h3>
                        <p class="text-[10px] font-bold text-[#8A8A8A] uppercase tracking-widest mt-1">Sistem Watermarking Otomatis</p>
                    </div>
                </div>
                
                <div class="space-y-10 flex-1">
                    <div class="flex items-center justify-between p-8 bg-[#FCFBF9] rounded-[40px] border border-[#EFEFEF]">
                        <span class="text-xs font-black text-[#1E2432] uppercase tracking-widest">Status Proteksi</span>
                        <select name="watermark_enabled" class="bg-white border border-[#EFEFEF] rounded-2xl px-6 py-3 text-[10px] font-black uppercase outline-none focus:ring-4 focus:ring-yellow-500/5 cursor-pointer">
                            <option value="on" {{ ($settings['watermark_enabled'] ?? 'on') == 'on' ? 'selected' : '' }}>Aktif (On)</option>
                            <option value="off" {{ ($settings['watermark_enabled'] ?? 'on') == 'off' ? 'selected' : '' }}>Nonaktif (Off)</option>
                        </select>
                    </div>
                    <div class="space-y-4 px-2">
                        <label class="text-[10px] font-black text-[#1E2432] uppercase tracking-[0.3em] ml-2">Label Watermark Pratinjau</label>
                        <input type="text" name="watermark_text" value="{{ $settings['watermark_text'] ?? 'SINERGI PAS JOMBANG' }}" class="w-full px-8 py-5 rounded-[28px] input-premium text-sm font-black text-[#1E2432] outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 3: Broadcast Banner -->
            <div class="bg-white rounded-[64px] p-12 flex flex-col settings-card">
                <div class="flex items-center gap-6 mb-12">
                    <div class="w-16 h-16 bg-blue-50 rounded-[28px] flex items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                        <i data-lucide="monitor" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-[#1E2432] italic">Running Banner</h3>
                        <p class="text-[10px] font-bold text-[#8A8A8A] uppercase tracking-widest mt-1">Konfigurasi Pengumuman Berjalan</p>
                    </div>
                </div>
                
                <div class="space-y-8 flex-1">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="text-[9px] font-black text-[#8A8A8A] uppercase tracking-[0.3em] ml-2">Durasi Animasi (Detik)</label>
                            <input type="number" name="running_text_speed" value="{{ $settings['running_text_speed'] ?? '20' }}" class="w-full px-6 py-4 rounded-2xl input-premium text-xs font-black">
                        </div>
                        <div class="flex flex-col justify-end pb-2">
                            <p class="text-[9px] text-[#ABABAB] font-bold uppercase italic leading-tight">Makin kecil nilai, makin cepat gerakan teks.</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="text-[9px] font-black text-[#8A8A8A] uppercase tracking-[0.3em] ml-2">Warna Latar</label>
                                <div class="flex gap-3">
                                    <input type="color" name="running_text_bg" id="color_bg" value="{{ $settings['running_text_bg'] ?? '#1E2432' }}" class="w-12 h-12 rounded-xl border border-[#EFEFEF] bg-white p-1 cursor-pointer">
                                    <input type="text" id="color_bg_text" value="{{ $settings['running_text_bg'] ?? '#1E2432' }}" readonly class="flex-1 px-4 py-3 rounded-xl border border-[#EFEFEF] bg-gray-50 text-[10px] font-mono font-bold uppercase flex items-center justify-center">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[9px] font-black text-[#8A8A8A] uppercase tracking-[0.3em] ml-2">Warna Teks</label>
                                <div class="flex gap-3">
                                    <input type="color" name="running_text_color" id="color_text" value="{{ $settings['running_text_color'] ?? '#FFFFFF' }}" class="w-12 h-12 rounded-xl border border-[#EFEFEF] bg-white p-1 cursor-pointer">
                                    <input type="text" id="color_text_text" value="{{ $settings['running_text_color'] ?? '#FFFFFF' }}" readonly class="flex-1 px-4 py-3 rounded-xl border border-[#EFEFEF] bg-gray-50 text-[10px] font-mono font-bold uppercase flex items-center justify-center">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Institutional Identity -->
        <div class="bg-white rounded-[64px] p-12 border border-[#EFEFEF] shadow-sm settings-card">
            <div class="flex items-center gap-6 mb-12">
                <div class="w-16 h-16 bg-purple-50 rounded-[28px] flex items-center justify-center text-purple-600 shadow-sm border border-purple-100">
                    <i data-lucide="building" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-[#1E2432] italic">Identitas Resmi Instansi</h3>
                    <p class="text-[10px] font-bold text-[#8A8A8A] uppercase tracking-widest mt-1">Konfigurasi Kop Dokumen & Export</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-[#1E2432] uppercase tracking-[0.3em] ml-2">Nama Instansi (Baris Utama)</label>
                        <input type="text" name="kop_line_1" value="{{ $settings['kop_line_1'] ?? 'LEMBAGA PEMASYARAKATAN JOMBANG' }}" class="w-full px-8 py-5 rounded-[28px] input-premium text-sm font-black text-[#1E2432] outline-none">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-[#1E2432] uppercase tracking-[0.3em] ml-2">Sub-Instansi / Wilayah</label>
                        <input type="text" name="kop_line_2" value="{{ $settings['kop_line_2'] ?? 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM JAWA TIMUR' }}" class="w-full px-8 py-5 rounded-[28px] input-premium text-sm font-black text-[#1E2432] outline-none">
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-[#1E2432] uppercase tracking-[0.3em] ml-2">Alamat Lengkap & Kontak</label>
                    <textarea name="kop_address" rows="5" class="w-full px-8 py-6 rounded-[32px] input-premium text-sm font-black text-[#1E2432] outline-none leading-relaxed">{{ $settings['kop_address'] ?? 'Jl. KH. Wahid Hasyim No. 123, Jombang' }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-center">
            <button type="submit" class="bg-[#1E2432] text-white px-20 py-6 rounded-[32px] font-black text-lg hover:bg-[#E85A4F] transition-all shadow-2xl active:scale-[0.98] flex items-center gap-5 group">
                Simpan Konfigurasi Global
                <i data-lucide="check-circle" class="w-7 h-7 group-hover:scale-110 transition-transform"></i>
            </button>
        </div>
    </form>

    <div class="h-px bg-gradient-to-r from-transparent via-[#EFEFEF] to-transparent my-20"></div>

    <!-- Section 5: Dynamic Broadcast Hub -->
    <div class="bg-white rounded-[64px] border border-[#EFEFEF] shadow-sm p-12 settings-card overflow-hidden relative">
        <div class="absolute top-0 right-0 p-12 opacity-[0.03]">
            <i data-lucide="megaphone" class="w-64 h-64 text-[#E85A4F]"></i>
        </div>
        <div class="relative">
            <div class="flex items-center gap-6 mb-14">
                <div class="w-16 h-16 bg-red-50 rounded-[28px] flex items-center justify-center text-red-600 shadow-sm border border-red-100">
                    <i data-lucide="broadcast" class="w-8 h-8 animate-pulse"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-[#1E2432] italic">Pusat Siaran Informasi</h3>
                    <p class="text-[10px] font-bold text-[#8A8A8A] uppercase tracking-widest mt-1">Publikasi Pengumuman Sistem</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                <!-- Create Form -->
                <div class="lg:col-span-1 bg-[#FCFBF9] p-10 rounded-[48px] border border-[#EFEFEF]">
                    <form action="{{ route('announcements.store') }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-[#8A8A8A] uppercase tracking-[0.3em] ml-2">Pesan Siaran</label>
                            <textarea name="message" rows="5" required class="w-full px-8 py-6 rounded-[32px] border border-[#EFEFEF] bg-white text-sm font-bold text-[#1E2432] outline-none focus:border-[#E85A4F] transition-all" placeholder="Tulis instruksi atau info resmi..."></textarea>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-[#8A8A8A] uppercase tracking-[0.3em] ml-2">Tipe Tampilan</label>
                            <div class="relative">
                                <select name="type" class="w-full px-8 py-5 rounded-[24px] border border-[#EFEFEF] bg-white text-[10px] font-black uppercase tracking-widest text-[#1E2432] outline-none appearance-none cursor-pointer">
                                    <option value="banner">Running Text Banner</option>
                                    <option value="popup">Important Alert Pop-up</option>
                                </select>
                                <i data-lucide="chevron-down" class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-4 text-[#8A8A8A]"></i>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-[#E85A4F] text-white py-6 rounded-[28px] font-black text-xs uppercase tracking-[0.3em] hover:bg-[#d44d42] transition-all shadow-xl shadow-red-100 flex items-center justify-center gap-3">
                            Siarkan Sekarang <i data-lucide="send" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>

                <!-- List History -->
                <div class="lg:col-span-2">
                    <h4 class="text-[10px] font-black text-[#ABABAB] uppercase tracking-[0.4em] mb-8 px-4 flex items-center gap-3">
                        <i data-lucide="history" class="w-4 h-4"></i> Riwayat Publikasi Terakhir
                    </h4>
                    <div class="space-y-6 max-h-[540px] overflow-y-auto pr-4 custom-scrollbar">
                        @forelse($announcements as $ann)
                        <div class="p-8 bg-white rounded-[40px] border border-[#EFEFEF] group transition-all hover:bg-[#FCFBF9] hover:border-[#E85A4F] flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-4">
                                    <span class="px-4 py-1.5 {{ $ann->type == 'popup' ? 'bg-purple-50 text-purple-600 border-purple-100' : 'bg-blue-50 text-blue-600 border-blue-100' }} text-[9px] font-black uppercase rounded-xl border italic">{{ $ann->type }}</span>
                                    @if($ann->is_active)
                                        <span class="flex items-center gap-2 px-4 py-1.5 bg-green-50 text-green-600 text-[9px] font-black uppercase rounded-xl border border-green-100 shadow-sm">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Aktif Di Sistem
                                        </span>
                                    @else
                                        <span class="px-4 py-1.5 bg-gray-100 text-gray-500 text-[9px] font-black uppercase rounded-xl border border-gray-200">Nonaktif</span>
                                    @endif
                                </div>
                                <p class="text-sm font-bold text-[#1E2432] leading-relaxed italic border-l-4 border-gray-100 pl-6 group-hover:border-[#E85A4F] transition-all">"{{ $ann->message }}"</p>
                                <div class="flex items-center gap-4 mt-6">
                                    <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center text-[8px] font-black text-[#8A8A8A]">{{ substr($ann->user->name, 0, 1) }}</div>
                                    <p class="text-[9px] text-[#ABABAB] font-black uppercase tracking-tighter">Publikasi oleh {{ $ann->user->name }} • {{ $ann->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-3 ml-8 opacity-0 group-hover:opacity-100 transition-all">
                                <form action="{{ route('announcements.toggle', $ann->id) }}" method="POST" class="no-loader">
                                    @csrf
                                    <button type="submit" class="w-12 h-12 bg-white rounded-2xl border border-[#EFEFEF] text-[#1E2432] hover:bg-[#1E2432] hover:text-white transition-all shadow-sm flex items-center justify-center" title="Toggle Aktif/Nonaktif">
                                        <i data-lucide="{{ $ann->is_active ? 'eye-off' : 'eye' }}" class="w-5 h-5"></i>
                                    </button>
                                </form>
                                <form id="deleteAnn-{{ $ann->id }}" action="{{ route('announcements.destroy', $ann->id) }}" method="POST" class="no-loader">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmGenericDelete('deleteAnn-{{ $ann->id }}', 'Hapus pengumuman ini?')" class="w-12 h-12 bg-white rounded-2xl border border-[#EFEFEF] text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Hapus Permanen">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="flex flex-col items-center justify-center py-32 bg-[#FCFBF9] rounded-[48px] border-2 border-dashed border-[#EFEFEF] opacity-40">
                            <i data-lucide="megaphone-off" class="w-20 h-20 mb-6"></i>
                            <p class="text-xs font-black uppercase tracking-[0.4em]">Belum Ada Riwayat Siaran</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="h-px bg-gradient-to-r from-transparent via-[#EFEFEF] to-transparent my-20"></div>

    <!-- Section 6: Master Data Governance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Position Governance -->
        <div class="bg-white rounded-[64px] border border-[#EFEFEF] shadow-sm p-12 settings-card">
            <div class="flex items-center gap-6 mb-12">
                <div class="w-16 h-16 bg-indigo-50 rounded-[28px] flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100">
                    <i data-lucide="briefcase" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-[#1E2432] italic">Master Jabatan</h3>
                    <p class="text-[10px] font-bold text-[#8A8A8A] uppercase tracking-widest mt-1">Tata Kelola Hierarki Organisasi</p>
                </div>
            </div>

            <form action="{{ route('settings.positions.store') }}" method="POST" class="flex gap-4 mb-12 no-loader group">
                @csrf
                <div class="relative flex-1">
                    <i data-lucide="plus-circle" class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-[#8A8A8A] group-focus-within:text-indigo-600 transition-all"></i>
                    <input type="text" name="name" required placeholder="Tambah Jabatan Baru..." class="w-full pl-16 pr-8 py-5 rounded-[24px] border border-[#EFEFEF] bg-[#FCFBF9] text-sm font-bold text-[#1E2432] outline-none focus:border-indigo-500 transition-all">
                </div>
                <button type="submit" class="w-16 h-16 bg-indigo-600 text-white rounded-[24px] flex items-center justify-center hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-90">
                    <i data-lucide="zap" class="w-6 h-6"></i>
                </button>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar">
                @forelse($positions as $pos)
                <div class="flex justify-between items-center p-6 bg-[#FCFBF9] rounded-3xl border border-[#EFEFEF] group transition-all hover:bg-white hover:border-indigo-200">
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-[#1E2432] uppercase tracking-tight">{{ $pos->name }}</span>
                        <span class="text-[8px] font-bold text-[#ABABAB] uppercase mt-1">{{ $pos->slug }}</span>
                    </div>
                    <form id="deletePos-{{ $pos->id }}" action="{{ route('settings.positions.destroy', $pos->id) }}" method="POST" class="no-loader">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmGenericDelete('deletePos-{{ $pos->id }}', 'Hapus jabatan ini?')" class="w-10 h-10 bg-white rounded-xl border border-[#EFEFEF] text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center shadow-sm">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="col-span-2 py-20 text-center opacity-30 italic">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em]">Belum Ada Data Jabatan</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Work Unit Governance -->
        <div class="bg-white rounded-[64px] border border-[#EFEFEF] shadow-sm p-12 settings-card">
            <div class="flex items-center gap-6 mb-12">
                <div class="w-16 h-16 bg-emerald-50 rounded-[28px] flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100">
                    <i data-lucide="layout-grid" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-[#1E2432] italic">Master Unit Kerja</h3>
                    <p class="text-[10px] font-bold text-[#8A8A8A] uppercase tracking-widest mt-1">Manajemen Struktur Penempatan</p>
                </div>
            </div>

            <form action="{{ route('settings.work-units.store') }}" method="POST" class="flex gap-4 mb-12 no-loader group">
                @csrf
                <div class="relative flex-1">
                    <i data-lucide="folder-plus" class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-[#8A8A8A] group-focus-within:text-emerald-600 transition-all"></i>
                    <input type="text" name="name" required placeholder="Tambah Unit Kerja Baru..." class="w-full pl-16 pr-8 py-5 rounded-[24px] border border-[#EFEFEF] bg-[#FCFBF9] text-sm font-bold text-[#1E2432] outline-none focus:border-emerald-500 transition-all">
                </div>
                <button type="submit" class="w-16 h-16 bg-emerald-600 text-white rounded-[24px] flex items-center justify-center hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100 active:scale-90">
                    <i data-lucide="zap" class="w-6 h-6"></i>
                </button>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar">
                @forelse($workUnits as $unit)
                <div class="flex justify-between items-center p-6 bg-[#FCFBF9] rounded-3xl border border-[#EFEFEF] group transition-all hover:bg-white hover:border-emerald-200">
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-[#1E2432] uppercase tracking-tight">{{ $unit->name }}</span>
                        <span class="text-[8px] font-bold text-[#ABABAB] uppercase mt-1">{{ $unit->slug }}</span>
                    </div>
                    <form id="deleteUnit-{{ $unit->id }}" action="{{ route('settings.work-units.destroy', $unit->id) }}" method="POST" class="no-loader">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmGenericDelete('deleteUnit-{{ $unit->id }}', 'Hapus unit kerja ini?')" class="w-10 h-10 bg-white rounded-xl border border-[#EFEFEF] text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center shadow-sm">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="col-span-2 py-20 text-center opacity-30 italic">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em]">Belum Ada Data Unit Kerja</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    // Realtime Color Text Preview
    const bgInp = document.getElementById('color_bg');
    const bgTxt = document.getElementById('color_bg_text');
    const txInp = document.getElementById('color_text');
    const txTxt = document.getElementById('color_text_text');

    if(bgInp) {
        bgInp.addEventListener('input', (e) => bgTxt.value = e.target.value.toUpperCase());
        txInp.addEventListener('input', (e) => txTxt.value = e.target.value.toUpperCase());
    }

    function confirmGenericDelete(formId, text) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: text || "Data ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E85A4F',
            cancelButtonColor: '#1E2432',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[48px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>

@if(session('success'))
<script>
    Swal.fire({ 
        icon: 'success', 
        title: 'Konfigurasi Terenkripsi', 
        text: "{{ session('success') }}", 
        confirmButtonColor: '#1E2432',
        customClass: { popup: 'rounded-[48px]' }
    });
</script>
@endif
@endsection
