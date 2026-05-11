@extends('layouts.app')

@section('title', 'Pengaturan')
@section('header-title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-6xl mx-auto page-fade">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Navigation -->
        <div class="md:w-64 shrink-0">
            <div class="sticky top-24 space-y-1">
                <a href="#umum" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold shadow-sm hover:border-blue-300 transition-all active-nav" data-nav="umum">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span>Umum & Kop</span>
                </a>
                <a href="#siaran" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-semibold hover:bg-white hover:border-slate-200 border border-transparent transition-all" data-nav="siaran">
                    <i data-lucide="megaphone" class="w-4 h-4"></i>
                    <span>Siaran & Style</span>
                </a>
                <a href="#absensi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-semibold hover:bg-white hover:border-slate-200 border border-transparent transition-all" data-nav="absensi">
                    <i data-lucide="fingerprint" class="w-4 h-4"></i>
                    <span>Absensi & Uang Makan</span>
                </a>
                <a href="#master" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-semibold hover:bg-white hover:border-slate-200 border border-transparent transition-all" data-nav="master">
                    <i data-lucide="database" class="w-4 h-4"></i>
                    <span>Master Data</span>
                </a>
                <a href="#email" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-semibold hover:bg-white hover:border-slate-200 border border-transparent transition-all" data-nav="email">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    <span>Konfigurasi Email</span>
                </a>
                <a href="#profil" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-semibold hover:bg-white hover:border-slate-200 border border-transparent transition-all" data-nav="profil">
                    <i data-lucide="user-cog" class="w-4 h-4"></i>
                    <span>Profil Admin</span>
                </a>
                
                <div class="pt-6">
                    <a href="{{ route('settings.health') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-700 font-bold text-xs uppercase tracking-wider hover:bg-blue-100 transition-all">
                        <i data-lucide="heart-pulse" class="w-4 h-4"></i>
                        <span>System Health</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Settings Content -->
        <div class="flex-1 space-y-10 pb-20">
            <!-- Umum & Kop -->
            <section id="umum" class="space-y-6">
                <div class="flex items-center gap-3 pb-2 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest text-xs">Konfigurasi Umum & Kop</h3>
                </div>

                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <!-- Dashboard Widgets -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden card-3d">
                        <div class="p-6 border-b border-slate-50 bg-slate-50/50">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="layout" class="w-4 h-4"></i>
                                Widget Dashboard
                            </h4>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php
                                $widgets = [
                                    ['key' => 'widget_stats', 'label' => 'Statistik Utama', 'icon' => 'bar-chart-3'],
                                    ['key' => 'widget_employees', 'label' => 'Unit Kerja', 'icon' => 'users'],
                                    ['key' => 'widget_chart', 'label' => 'Grafik Berkas', 'icon' => 'pie-chart'],
                                    ['key' => 'widget_activity', 'label' => 'Audit Trail', 'icon' => 'activity'],
                                ];
                            @endphp
                            @foreach($widgets as $w)
                            <label class="flex items-center justify-between p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-blue-200 transition-all cursor-pointer group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-blue-600 transition-colors">
                                        <i data-lucide="{{ $w['icon'] }}" class="w-5 h-5"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">{{ $w['label'] }}</span>
                                </div>
                                <div class="relative inline-flex items-center">
                                    <input type="hidden" name="{{ $w['key'] }}" value="off">
                                    <input type="checkbox" name="{{ $w['key'] }}" value="on" class="peer sr-only" {{ ($settings[$w['key']] ?? 'on') === 'on' ? 'checked' : '' }}>
                                    <div class="h-6 w-11 rounded-full bg-slate-200 transition-all peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-5"></div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kop Surat -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 card-3d">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i data-lucide="building" class="w-4 h-4"></i>
                            Identitas Kop Surat
                        </h4>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Instansi Utama (Baris 1)</label>
                                    <input type="text" name="kop_line_1" id="kop_1" value="{{ $settings['kop_line_1'] ?? 'KEMENTERIAN HUKUM DAN HAM RI' }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm" onkeyup="syncKop()">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Satuan Kerja (Baris 2)</label>
                                    <input type="text" name="kop_line_2" id="kop_2" value="{{ $settings['kop_line_2'] ?? 'LAPAS KELAS IIB JOMBANG' }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm" onkeyup="syncKop()">
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-[32px] border border-slate-100 p-8 flex flex-col items-center justify-center">
                                <div class="flex items-center gap-5 border-b-2 border-slate-900 pb-4 w-full">
                                    <img src="{{ asset('logo1.png') }}" class="w-14 h-14 object-contain">
                                    <div class="text-left">
                                        <h2 id="preview_kop_1" class="text-[10px] font-bold text-slate-900 uppercase leading-tight">{{ $settings['kop_line_1'] ?? 'KEMENTERIAN HUKUM DAN HAM RI' }}</h2>
                                        <h3 id="preview_kop_2" class="text-sm font-black text-slate-900 uppercase leading-tight">{{ $settings['kop_line_2'] ?? 'LAPAS KELAS IIB JOMBANG' }}</h3>
                                    </div>
                                </div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-4">Preview Header Laporan</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 transition-all btn-3d shadow-xl">
                            Simpan Identitas
                        </button>
                    </div>
                </form>
            </section>

            <!-- Siaran & Style -->
            <section id="siaran" class="space-y-6 pt-10 border-t border-slate-200">
                <div class="flex items-center gap-3 pb-2 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest text-xs">Siaran & Visual Style</h3>
                </div>

                <!-- Running Text Style -->
                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 card-3d">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i data-lucide="palette" class="w-4 h-4"></i>
                            Konfigurasi Siaran Utama
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Mode Siaran</label>
                                <select name="broadcast_mode" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-bold focus:bg-white focus:border-blue-500 outline-none appearance-none cursor-pointer">
                                    <option value="running_text" {{ ($settings['broadcast_mode'] ?? 'running_text') === 'running_text' ? 'selected' : '' }}>Running Text (Banner Atas)</option>
                                    <option value="popup" {{ ($settings['broadcast_mode'] ?? 'running_text') === 'popup' ? 'selected' : '' }}>Pop-up Dialog (Saat Login/Refresh)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Ukuran Teks (PX)</label>
                                <div class="flex items-center gap-4">
                                    <input type="range" name="running_text_size" min="10" max="24" value="{{ $settings['running_text_size'] ?? '12' }}" class="flex-1 accent-blue-600" oninput="this.nextElementSibling.innerText = this.value + 'px'">
                                    <span class="text-sm font-black text-blue-600 w-12">{{ $settings['running_text_size'] ?? '12' }}px</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Isi Konten Pesan Siaran</label>
                            <textarea name="running_text_message" rows="2" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-bold focus:bg-white focus:border-blue-500 outline-none transition-all resize-none" placeholder="Masukkan pesan pengumuman di sini...">{{ $settings['running_text_message'] ?? '' }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kecepatan Jalan (Detik)</label>
                                <input type="number" name="running_text_speed" value="{{ $settings['running_text_speed'] ?? '20' }}" class="w-full px-5 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-bold focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Warna Background</label>
                                <div class="flex gap-2">
                                    <input type="color" name="running_text_bg" value="{{ $settings['running_text_bg'] ?? '#0F172A' }}" class="w-12 h-12 rounded-xl border border-slate-200 bg-white cursor-pointer">
                                    <input type="text" value="{{ $settings['running_text_bg'] ?? '#0F172A' }}" class="flex-1 px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono font-bold" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Warna Teks</label>
                                <div class="flex gap-2">
                                    <input type="color" name="running_text_color" value="{{ $settings['running_text_color'] ?? '#FFFFFF' }}" class="w-12 h-12 rounded-xl border border-slate-200 bg-white cursor-pointer">
                                    <input type="text" value="{{ $settings['running_text_color'] ?? '#FFFFFF' }}" class="flex-1 px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono font-bold" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 p-4 rounded-2xl border border-slate-100 bg-slate-50">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-blue-600">
                                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Watermark Dokumen</span>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Tambahkan tanda air pada setiap unduhan PDF</p>
                                    </div>
                                </div>
                                <div class="relative inline-flex items-center">
                                    <input type="hidden" name="watermark_enabled" value="off">
                                    <input type="checkbox" name="watermark_enabled" value="on" class="peer sr-only" {{ ($settings['watermark_enabled'] ?? 'on') === 'on' ? 'checked' : '' }}>
                                    <div class="h-6 w-11 rounded-full bg-slate-200 transition-all peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-5"></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-700 transition-all btn-3d shadow-xl">
                            Simpan Style Visual
                        </button>
                    </div>
                </form>
            </section>

            <!-- Absensi & Uang Makan -->
            <section id="absensi" class="space-y-6 pt-10 border-t border-slate-200">
                <div class="flex items-center gap-3 pb-2 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest text-xs">Absensi & Uang Makan</h3>
                </div>

                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm p-8 card-3d">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Threshold Terlambat (Kantor)</label>
                                <div class="relative">
                                    <i data-lucide="clock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                                    <input type="time" name="office_late_threshold" value="{{ $settings['office_late_threshold'] ?? '07:30' }}" class="w-full pl-12 pr-5 py-4 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-black text-lg text-blue-600 transition-all">
                                </div>
                                <p class="text-[10px] text-slate-400 mt-3 italic font-medium leading-relaxed">Staf yang melakukan scan setelah jam ini akan otomatis tercatat sebagai "Terlambat".</p>
                            </div>
                            <div class="bg-slate-50 rounded-3xl p-8 border border-dashed border-slate-200 flex flex-col items-center justify-center text-center">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-4">
                                    <i data-lucide="banknote" class="w-6 h-6 text-emerald-500"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 mb-2">Rate Uang Makan</h4>
                                <p class="text-[10px] text-slate-500 font-medium leading-relaxed mb-6">Manajemen besaran uang makan kini terintegrasi langsung dengan data Golongan.</p>
                                <a href="{{ route('admin.ranks.index') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg">
                                    Atur Rate di Halaman Golongan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-700 transition-all btn-3d shadow-xl">
                            Simpan Parameter Absensi
                        </button>
                    </div>
                </form>
            </section>

            <!-- Master Data -->
            <section id="master" class="space-y-6 pt-10 border-t border-slate-200">
                <div class="flex items-center gap-3 pb-2 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest text-xs">Pengelolaan Master Data</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Jabatan -->
                    <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden card-3d flex flex-col h-[500px]">
                        <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center shrink-0">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="selectAllPositions" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Daftar Jabatan</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="confirmBulkDelete('position')" id="btnDeletePositions" class="hidden px-3 py-1 bg-red-50 text-red-600 rounded-lg text-[9px] font-bold uppercase hover:bg-red-600 hover:text-white transition-all">Hapus Terpilih</button>
                                <span class="px-2 py-0.5 rounded-full bg-blue-600 text-white text-[9px] font-bold">{{ $positions->count() }}</span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col min-h-0">
                            <form action="{{ route('settings.positions.store') }}" method="POST" class="flex gap-2 mb-6 shrink-0">
                                @csrf
                                <input type="text" name="name" required class="flex-1 px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 outline-none bg-slate-50" placeholder="Tambah jabatan...">
                                <button type="submit" class="w-12 h-12 flex items-center justify-center bg-slate-900 text-white rounded-2xl hover:bg-blue-600 transition-all active:scale-95 shadow-lg">
                                    <i data-lucide="plus" class="w-6 h-6"></i>
                                </button>
                            </form>
                            <div class="overflow-y-auto custom-scrollbar space-y-2 flex-1 pr-2">
                                @foreach($positions as $p)
                                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:border-blue-200 transition-all">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="pos_ids[]" value="{{ $p->id }}" class="pos-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer">
                                        <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $p->name }}</span>
                                    </div>
                                    <form action="{{ route('settings.positions.destroy', $p->id) }}" method="POST" class="no-loader">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Unit Kerja -->
                    <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden card-3d flex flex-col h-[500px]">
                        <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center shrink-0">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="selectAllUnits" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Daftar Unit Kerja</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="confirmBulkDelete('unit')" id="btnDeleteUnits" class="hidden px-3 py-1 bg-red-50 text-red-600 rounded-lg text-[9px] font-bold uppercase hover:bg-red-600 hover:text-white transition-all">Hapus Terpilih</button>
                                <span class="px-2 py-0.5 rounded-full bg-blue-600 text-white text-[9px] font-bold">{{ $workUnits->count() }}</span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col min-h-0">
                            <form action="{{ route('settings.work-units.store') }}" method="POST" class="flex gap-2 mb-6 shrink-0">
                                @csrf
                                <input type="text" name="name" required class="flex-1 px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 outline-none bg-slate-50" placeholder="Tambah unit...">
                                <button type="submit" class="w-12 h-12 flex items-center justify-center bg-slate-900 text-white rounded-2xl hover:bg-blue-600 transition-all active:scale-95 shadow-lg">
                                    <i data-lucide="plus" class="w-6 h-6"></i>
                                </button>
                            </form>
                            <div class="overflow-y-auto custom-scrollbar space-y-2 flex-1 pr-2">
                                @foreach($workUnits as $u)
                                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:border-blue-200 transition-all">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="unit_ids[]" value="{{ $u->id }}" class="unit-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer">
                                        <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $u->name }}</span>
                                    </div>
                                    <form action="{{ route('settings.work-units.destroy', $u->id) }}" method="POST" class="no-loader">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Konfigurasi Email -->
            <section id="email" class="space-y-6 pt-10 border-t border-slate-200">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest text-xs">Konfigurasi Server Email (SMTP)</h3>
                    <div class="flex items-center gap-2 px-3 py-1 bg-amber-50 text-amber-700 rounded-lg border border-amber-100">
                        <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest">Penting untuk Lupa Password</span>
                    </div>
                </div>

                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm p-8 card-3d">
                        <div class="mb-8 p-6 bg-slate-50 rounded-3xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                            <div>
                                <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-1">Driver Email Utama</h4>
                                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-tight">Pilih 'SMTP' untuk pengiriman asli, atau 'Log' untuk simulasi (debug).</p>
                            </div>
                            <select name="mail_mailer" class="w-full px-5 py-4 rounded-2xl border-2 border-slate-200 bg-white text-sm font-black text-blue-600 outline-none appearance-none cursor-pointer">
                                <option value="smtp" {{ ($settings['mail_mailer'] ?? config('mail.default')) == 'smtp' ? 'selected' : '' }}>SERVER SMTP (REKOMENDASI)</option>
                                <option value="log" {{ ($settings['mail_mailer'] ?? config('mail.default')) == 'log' ? 'selected' : '' }}>LOG FILE (HANYA SIMULASI)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">SMTP Host</label>
                                    <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? config('mail.mailers.smtp.host') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm bg-slate-50" placeholder="smtp.gmail.com">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">SMTP Port</label>
                                        <input type="text" name="mail_port" value="{{ $settings['mail_port'] ?? config('mail.mailers.smtp.port') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm bg-slate-50" placeholder="465">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Enkripsi</label>
                                        <select name="mail_encryption" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm bg-slate-50 appearance-none">
                                            <option value="ssl" {{ ($settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="tls" {{ ($settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="null" {{ ($settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption')) == 'null' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Email Pengirim (Username)</label>
                                    <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? config('mail.mailers.smtp.username') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm bg-slate-50" placeholder="admin@lapasjombang.id">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Password / App Password</label>
                                    <div class="relative">
                                        <input type="password" name="mail_password" value="{{ $settings['mail_password'] ?? config('mail.mailers.smtp.password') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm bg-slate-50 pr-12">
                                        <button type="button" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-500 transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Display Name (Nama Pengirim)</label>
                                <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? config('mail.from.name') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm bg-slate-50" placeholder="SINERGI PAS - LAPAS JOMBANG">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Display Email Address</label>
                                <input type="text" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? config('mail.from.address') }}" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm bg-slate-50" placeholder="noreply@sdm.lapasjombang.id">
                            </div>
                        </div>

                        <div class="mt-8 p-6 bg-blue-50 rounded-3xl border border-blue-100 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                                    <i data-lucide="send" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-blue-900 uppercase tracking-widest">Uji Coba Email</h4>
                                    <p class="text-[9px] text-blue-700 font-bold uppercase tracking-tight">Kirim email tes ke alamat Anda sendiri</p>
                                </div>
                            </div>
                            <button type="button" onclick="testEmailConnection()" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg active:scale-95">
                                Kirim Email Tes
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 transition-all btn-3d shadow-xl">
                            Simpan Konfigurasi Email
                        </button>
                    </div>
                </form>
            </section>

            <!-- Profil Admin -->
            <section id="profil" class="space-y-6 pt-10 border-t border-slate-200">
                <div class="flex items-center gap-3 pb-2 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest text-xs">Informasi Profil Admin</h3>
                </div>

                <form action="{{ route('settings.admin-profile.update') }}" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm p-8 card-3d">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm bg-slate-50">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Email Login</label>
                                    <input type="email" name="email" value="{{ auth()->user()->email }}" required class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:border-blue-500 outline-none font-bold text-sm bg-slate-50">
                                    <p class="text-[9px] text-amber-600 font-bold uppercase mt-2 italic">* Pastikan email ini aktif untuk kepentingan Lupa Password.</p>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 rounded-3xl p-8 border border-blue-100 space-y-4">
                                <h4 class="text-xs font-black text-blue-900 uppercase tracking-widest flex items-center gap-2">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                    Ganti Kata Sandi (Opsional)
                                </h4>
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-2">Password Lama</label>
                                    <input type="password" name="current_password" class="w-full px-5 py-3 rounded-xl border border-blue-200 focus:border-blue-500 outline-none font-bold text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-2">Password Baru</label>
                                    <input type="password" name="new_password" class="w-full px-5 py-3 rounded-xl border border-blue-200 focus:border-blue-500 outline-none font-bold text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-2">Konfirmasi Password Baru</label>
                                    <input type="password" name="new_password_confirmation" class="w-full px-5 py-3 rounded-xl border border-blue-200 focus:border-blue-500 outline-none font-bold text-sm bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 transition-all btn-3d shadow-xl">
                            Update Profil Admin
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

<!-- Modal Add Announcement -->
<div id="addAnnouncementModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[32px] p-10 shadow-2xl animate-in zoom-in duration-200">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-bold text-slate-900 italic">Buat Siaran Baru</h3>
            <button onclick="document.getElementById('addAnnouncementModal').classList.add('hidden')" class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <form action="{{ route('announcements.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Pesan Siaran</label>
                <textarea name="message" required rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-bold focus:bg-white focus:border-blue-500 outline-none transition-all resize-none" placeholder="Tulis pengumuman di sini..."></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Tipe Tampilan</label>
                <select name="type" required class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-bold focus:bg-white focus:border-blue-500 outline-none appearance-none cursor-pointer">
                    <option value="banner">Running Text (Banner Atas)</option>
                    <option value="popup" disabled>Popup Dialog (Coming Soon)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Mulai Tayang</label>
                    <input type="datetime-local" name="starts_at" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold outline-none focus:border-blue-500">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Berakhir Pada</label>
                    <input type="datetime-local" name="expires_at" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold outline-none focus:border-blue-500">
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg btn-3d">
                Siarkan Sekarang
            </button>
        </form>
    </div>
</div>

<form id="bulkDeletePositionForm" action="{{ route('settings.positions.bulk-destroy') }}" method="POST" class="hidden no-loader">
    @csrf @method('DELETE')
</form>

<form id="bulkDeleteUnitForm" action="{{ route('settings.work-units.bulk-destroy') }}" method="POST" class="hidden no-loader">
    @csrf @method('DELETE')
</form>

@if(session('success'))
<script>
    window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", confirmButtonColor: '#0F172A', customClass: { popup: 'rounded-[32px]' } });
    });
</script>
@endif

@push('scripts')
<script>
    function syncKop() {
        const kop1 = document.getElementById('kop_1').value;
        const kop2 = document.getElementById('kop_2').value;
        document.getElementById('preview_kop_1').innerText = kop1 || 'KEMENTERIAN HUKUM DAN HAM RI';
        document.getElementById('preview_kop_2').innerText = kop2 || 'LAPAS KELAS IIB JOMBANG';
    }

    function testEmailConnection() {
        Swal.fire({
            title: 'Kirim Email Tes?',
            text: "Sistem akan mencoba mengirimkan email percobaan ke alamat email pengirim untuk memastikan konfigurasi sudah benar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim!',
            customClass: { popup: 'rounded-[32px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mencoba menghubungkan ke server email...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                fetch("{{ route('settings.email.test') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, customClass: { popup: 'rounded-[32px]' } });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message, customClass: { popup: 'rounded-[32px]' } });
                    }
                })
                .catch(error => {
                    Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan sistem saat mencoba mengirim email.', customClass: { popup: 'rounded-[32px]' } });
                });
            }
        });
    }

    // Bulk selection logic
    function handleBulkSelect(selectAllId, checkboxClass, btnDeleteId) {
        const selectAll = document.getElementById(selectAllId);
        const checkboxes = document.querySelectorAll('.' + checkboxClass);
        const btnDelete = document.getElementById(btnDeleteId);

        selectAll.addEventListener('change', () => {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBtnVisibility();
        });

        checkboxes.forEach(cb => cb.addEventListener('change', updateBtnVisibility));

        function updateBtnVisibility() {
            const checkedCount = document.querySelectorAll('.' + checkboxClass + ':checked').length;
            btnDelete.classList.toggle('hidden', checkedCount === 0);
        }
    }

    function confirmBulkDelete(type) {
        const checkboxClass = type === 'position' ? 'pos-checkbox' : 'unit-checkbox';
        const checkedBoxes = document.querySelectorAll('.' + checkboxClass + ':checked');
        const form = type === 'position' ? document.getElementById('bulkDeletePositionForm') : document.getElementById('bulkDeleteUnitForm');

        Swal.fire({
            title: 'Hapus Massal?',
            text: `${checkedBoxes.length} data terpilih akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Hapus Semua!',
            customClass: { popup: 'rounded-[32px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                form.querySelectorAll('input[name="ids[]"]').forEach(i => i.remove());
                checkedBoxes.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });
                form.submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleBulkSelect('selectAllPositions', 'pos-checkbox', 'btnDeletePositions');
        handleBulkSelect('selectAllUnits', 'unit-checkbox', 'btnDeleteUnits');

        const navLinks = document.querySelectorAll('[data-nav]');
        const sections = document.querySelectorAll('section[id]');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (window.pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('bg-white', 'border-slate-200', 'text-slate-700', 'shadow-sm');
                link.classList.add('text-slate-500', 'border-transparent');
                if (link.getAttribute('data-nav') === current) {
                    link.classList.remove('text-slate-500', 'border-transparent');
                    link.classList.add('bg-white', 'border-slate-200', 'text-slate-700', 'shadow-sm');
                }
            });
        });
    });
</script>
@endpush
@endsection
