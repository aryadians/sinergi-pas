@extends('layouts.app')

@section('title', 'Whistleblowing System')
@section('header-title', 'Formulir Pengaduan (WBS)')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 page-fade">
    <!-- Premium Header -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-red-900 rounded-[40px] p-8 md:p-12 text-white shadow-2xl relative overflow-hidden card-3d border border-slate-700">
        <!-- Decorative elements -->
        <div class="absolute -right-16 -bottom-16 opacity-5 mix-blend-overlay">
            <i data-lucide="fingerprint" class="w-96 h-96"></i>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center md:items-start">
            <div class="w-24 h-24 rounded-[24px] bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shrink-0 shadow-lg shadow-red-500/30 border border-red-400/50">
                <i data-lucide="shield-alert" class="w-12 h-12 text-white"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <h2 class="text-3xl md:text-4xl font-black tracking-tight italic">Whistleblowing System</h2>
                    <span class="px-3 py-1 bg-red-500/20 text-red-300 text-[10px] font-black uppercase tracking-widest rounded-full border border-red-500/30 backdrop-blur-md">Internal WBS</span>
                </div>
                <p class="text-slate-300 font-medium leading-relaxed max-w-2xl text-sm md:text-base">
                    Saluran resmi pelaporan pelanggaran di lingkungan Lapas Jombang. Kami menjamin kerahasiaan identitas Anda demi menciptakan lingkungan kerja yang bersih, berintegritas, dan bebas dari praktik KKN.
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2 bg-white/5 px-4 py-2 rounded-xl border border-white/10 backdrop-blur-sm">
                        <i data-lucide="lock" class="w-4 h-4 text-emerald-400"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-200">Kerahasiaan Identitas</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/5 px-4 py-2 rounded-xl border border-white/10 backdrop-blur-sm">
                        <i data-lucide="eye-off" class="w-4 h-4 text-amber-400"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-200">Lapor Secara Anonim</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kebijakan Perlindungan -->
    <div class="bg-blue-50 rounded-[32px] border border-blue-100 p-6 md:p-8 card-3d">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                <i data-lucide="info" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-blue-900 uppercase tracking-widest mb-2">Panduan & Perlindungan Pelapor</h3>
                <p class="text-xs text-blue-800 font-medium leading-relaxed mb-4">
                    Sistem ini dilindungi oleh enkripsi. Jika Anda memilih mode anonim, sistem secara otomatis akan menghapus jejak NIP dan nama Anda dari antarmuka pemeriksa (Admin). Anda hanya perlu menyimpan <strong>Nomor Tiket</strong> yang diberikan setelah melapor untuk melacak status laporan Anda.
                </p>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px] font-bold text-blue-700">
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-blue-500"></i> Pungutan Liar (Pungli)</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-blue-500"></i> Gratifikasi / Suap</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-blue-500"></i> Penyalahgunaan Wewenang</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-blue-500"></i> Pelanggaran Kode Etik Pegawai</li>
                </ul>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-2xl animate-in slide-in-from-top-2">
        <div class="flex items-center gap-3 mb-2">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
            <h3 class="text-sm font-bold text-red-800 uppercase tracking-widest">Terjadi Kesalahan Form</h3>
        </div>
        <ul class="list-disc list-inside text-sm text-red-600 font-medium space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('wbs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="wbsForm">
        @csrf

        <!-- Mode Anonim Toggle -->
        <div class="bg-white rounded-[32px] border-2 border-red-100 shadow-sm overflow-hidden card-3d relative group hover:border-red-300 transition-colors">
            <div class="absolute inset-0 bg-red-50/50 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
            <div class="px-8 py-6 border-b border-red-100 bg-red-50/30 flex items-center justify-between relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-red-600 shadow-sm">
                        <i data-lucide="user-minus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest">Sembunyikan Identitas (Anonim)</h4>
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5">Admin tidak akan tahu siapa yang mengirim ini</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_anonymous" class="sr-only peer" checked id="anonToggle">
                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-600 shadow-inner"></div>
                </label>
            </div>
        </div>

        <!-- Form Utama -->
        <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm p-8 md:p-10 card-3d space-y-8">
            <div>
                <label class="block text-xs font-black text-slate-800 uppercase tracking-widest mb-3 ml-1">Kategori Pelanggaran <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <i data-lucide="bookmark" class="w-5 h-5"></i>
                    </div>
                    <select name="category" required class="w-full pl-14 pr-6 py-5 rounded-2xl border-2 border-slate-100 bg-slate-50 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none cursor-pointer">
                        <option value="" disabled selected>-- Pilih Jenis Pelanggaran --</option>
                        <option value="Penyalahgunaan Wewenang">Penyalahgunaan Wewenang</option>
                        <option value="Pungutan Liar (Pungli)">Pungutan Liar (Pungli)</option>
                        <option value="Gratifikasi / Suap">Gratifikasi / Suap</option>
                        <option value="Pelanggaran Kode Etik">Pelanggaran Kode Etik Pegawai</option>
                        <option value="Diskriminasi Layanan">Diskriminasi Layanan</option>
                        <option value="Lainnya">Pelanggaran Lainnya</option>
                    </select>
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-800 uppercase tracking-widest mb-3 ml-1 flex justify-between items-center">
                    <span>Deskripsi Kejadian <span class="text-red-500">*</span></span>
                    <span class="text-[9px] font-bold text-slate-400 normal-case bg-slate-100 px-2 py-1 rounded-md">Jelaskan 5W + 1H</span>
                </label>
                <div class="relative group">
                    <textarea name="description" rows="8" required placeholder="Jelaskan secara rinci:&#10;1. Apa yang terjadi?&#10;2. Siapa yang terlibat?&#10;3. Kapan kejadiannya (waktu & tanggal)?&#10;4. Di mana lokasinya?&#10;5. Bagaimana kronologisnya?" class="w-full px-6 py-5 rounded-2xl border-2 border-slate-100 bg-slate-50 text-sm font-medium text-slate-700 outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all resize-y leading-relaxed"></textarea>
                </div>
            </div>

            <!-- Upload Area -->
            <div>
                <label class="block text-xs font-black text-slate-800 uppercase tracking-widest mb-3 ml-1 flex justify-between items-center">
                    <span>Lampiran Bukti Pendukung</span>
                    <span class="text-[9px] font-bold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1 rounded-md normal-case">Sangat Dianjurkan</span>
                </label>
                
                <div id="dropZone" class="p-10 border-2 border-dashed border-slate-300 rounded-[24px] bg-slate-50 text-center relative group hover:bg-blue-50 hover:border-blue-400 transition-all cursor-pointer">
                    <input type="file" name="evidences[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="fileInput" onchange="handleFiles(this.files)" accept="image/*,video/*,audio/*,.pdf,.doc,.docx">
                    <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:shadow-md transition-all duration-300 border border-slate-100">
                        <i data-lucide="cloud-upload" class="w-8 h-8 text-blue-500"></i>
                    </div>
                    <p class="text-base font-black text-slate-700 mb-1">Klik atau Tarik File ke Sini</p>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Format Didukung: JPG, PNG, MP4, MP3, PDF (Multi-file)</p>
                </div>

                <!-- Preview Area -->
                <div id="filePreviewContainer" class="mt-6 hidden">
                    <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">File Terpilih:</h5>
                    <div id="fileList" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <!-- Preview cards injected here by JS -->
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-4 pb-12">
            <p class="text-[10px] font-bold text-slate-400 text-center sm:text-left max-w-xs">
                <i data-lucide="shield-check" class="inline w-3 h-3 text-emerald-500"></i> Dengan mengirimkan laporan ini, saya menyatakan bahwa informasi yang diberikan adalah benar.
            </p>
            <button type="submit" id="submitBtn" class="w-full sm:w-auto px-12 py-5 rounded-full bg-slate-900 text-white font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-600 hover:shadow-2xl hover:shadow-blue-500/30 transition-all active:scale-95 flex items-center justify-center gap-3 group relative overflow-hidden">
                <span class="relative z-10 flex items-center gap-2">
                    Kirim Laporan <i data-lucide="send" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </span>
                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out"></div>
            </button>
        </div>
    </form>
</div>

<script>
    // Handle Drag and Drop
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files; // Assign files to the input
        handleFiles(files);
    }

    // Handle File Preview
    function handleFiles(files) {
        const previewContainer = document.getElementById('filePreviewContainer');
        const list = document.getElementById('fileList');
        
        list.innerHTML = '';
        
        if (files.length > 0) {
            previewContainer.classList.remove('hidden');
            
            Array.from(files).forEach(file => {
                const item = document.createElement('div');
                item.className = 'relative flex flex-col items-center gap-2 p-4 bg-white border border-slate-200 rounded-[20px] shadow-sm overflow-hidden group hover:border-blue-300 transition-colors';
                
                // Determine icon or image preview
                let previewContent = '';
                if (file.type.startsWith('image/')) {
                    const objUrl = URL.createObjectURL(file);
                    previewContent = `<img src="${objUrl}" class="w-full h-24 object-cover rounded-xl border border-slate-100 group-hover:scale-105 transition-transform duration-500" alt="Preview">`;
                } else if (file.type.startsWith('video/')) {
                    previewContent = `<div class="w-full h-24 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100"><i data-lucide="video" class="w-8 h-8 text-indigo-400"></i></div>`;
                } else if (file.type.startsWith('audio/')) {
                    previewContent = `<div class="w-full h-24 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100"><i data-lucide="headphones" class="w-8 h-8 text-amber-400"></i></div>`;
                } else {
                    previewContent = `<div class="w-full h-24 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100"><i data-lucide="file-text" class="w-8 h-8 text-slate-400"></i></div>`;
                }

                // File size formatter
                const size = (file.size / (1024*1024)).toFixed(2);
                
                item.innerHTML = `
                    ${previewContent}
                    <div class="w-full text-center mt-1">
                        <p class="text-xs font-bold text-slate-800 truncate px-2" title="${file.name}">${file.name}</p>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">${size} MB</p>
                    </div>
                    <div class="absolute top-2 right-2 w-6 h-6 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-emerald-500 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        <i data-lucide="check" class="w-3 h-3"></i>
                    </div>
                `;
                list.appendChild(item);
            });
            lucide.createIcons();
        } else {
            previewContainer.classList.add('hidden');
        }
    }

    // Submit Loading State
    document.getElementById('wbsForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = `<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Mengenkripsi & Mengirim...`;
        btn.classList.add('opacity-80', 'pointer-events-none');
        lucide.createIcons();
    });
</script>
@endsection