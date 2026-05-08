@extends('layouts.app')

@section('title', 'Whistleblowing System')
@section('header-title', 'Formulir Pengaduan (WBS)')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 page-fade">
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-[40px] p-8 md:p-10 text-white shadow-2xl relative overflow-hidden card-3d">
        <div class="absolute -right-8 -bottom-8 opacity-10">
            <i data-lucide="shield-alert" class="w-64 h-64"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-3xl font-black mb-3 italic tracking-tight">Whistleblowing System</h2>
            <p class="text-red-100 font-medium leading-relaxed max-w-2xl">
                Sistem Pelaporan Pelanggaran Internal Lapas Jombang. Laporkan indikasi penyalahgunaan wewenang, pungli, gratifikasi, atau pelanggaran kode etik secara aman.
            </p>
            <div class="mt-6 flex items-center gap-3 bg-white/10 w-fit px-4 py-2 rounded-full border border-white/20 backdrop-blur-sm">
                <i data-lucide="lock" class="w-4 h-4 text-amber-300"></i>
                <span class="text-xs font-bold uppercase tracking-widest text-amber-100">Kerahasiaan Dijamin (Lapor Tanpa Cemas)</span>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-2xl">
        <div class="flex items-center gap-3 mb-2">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
            <h3 class="text-sm font-bold text-red-800 uppercase tracking-widest">Terjadi Kesalahan</h3>
        </div>
        <ul class="list-disc list-inside text-sm text-red-600 font-medium space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('wbs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden card-3d">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i data-lucide="incognito" class="w-5 h-5 text-slate-500"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">Mode Anonim</h4>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_anonymous" class="sr-only peer" checked>
                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-600"></div>
                </label>
            </div>
            <div class="p-8 bg-slate-50 text-sm text-slate-600 font-medium leading-relaxed">
                Jika diaktifkan, identitas Anda (Nama & NIP) akan dirahasiakan dan <strong>TIDAK AKAN DITAMPILKAN</strong> di layar Admin. Laporan akan murni dikelola berdasarkan nomor tiket.
            </div>
        </div>

        <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm p-8 card-3d space-y-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kategori Pelanggaran *</label>
                <div class="relative group">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                    </div>
                    <select name="category" required class="w-full pl-12 pr-4 py-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none cursor-pointer">
                        <option value="" disabled selected>Pilih Kategori Pelanggaran</option>
                        <option value="Penyalahgunaan Wewenang">Penyalahgunaan Wewenang</option>
                        <option value="Pungutan Liar (Pungli)">Pungutan Liar (Pungli)</option>
                        <option value="Gratifikasi / Suap">Gratifikasi / Suap</option>
                        <option value="Pelanggaran Kode Etik">Pelanggaran Kode Etik</option>
                        <option value="Diskriminasi Layanan">Diskriminasi Layanan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Laporan *</label>
                <textarea name="description" rows="6" required placeholder="Jelaskan secara detail kejadian, waktu, lokasi, dan pihak yang terlibat..." class="w-full px-5 py-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-sm font-medium text-slate-700 outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all"></textarea>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Lampiran Bukti (Opsional)</label>
                <div class="p-8 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 text-center relative group hover:bg-blue-50 hover:border-blue-300 transition-colors">
                    <input type="file" name="evidences[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="fileInput" onchange="updateFileList()">
                    <i data-lucide="upload-cloud" class="w-12 h-12 text-slate-400 mx-auto mb-4 group-hover:text-blue-500 transition-colors"></i>
                    <p class="text-sm font-bold text-slate-700">Pilih atau letakkan file bukti di sini</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Mendukung Foto, Video, Audio, dan Dokumen. Bisa pilih banyak file sekaligus.</p>
                </div>
                <div id="fileList" class="mt-4 space-y-2 hidden">
                    <!-- File list will be populated here -->
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 pb-12">
            <button type="submit" class="px-12 py-5 rounded-[24px] bg-red-600 text-white font-black text-xs uppercase tracking-[0.2em] hover:bg-red-700 hover:shadow-2xl hover:shadow-red-500/30 transition-all active:scale-95 flex items-center gap-3 group">
                Kirim Laporan WBS <i data-lucide="send" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
            </button>
        </div>
    </form>
</div>

<script>
    function updateFileList() {
        const input = document.getElementById('fileInput');
        const list = document.getElementById('fileList');
        
        list.innerHTML = '';
        
        if (input.files.length > 0) {
            list.classList.remove('hidden');
            Array.from(input.files).forEach(file => {
                const item = document.createElement('div');
                item.className = 'flex items-center gap-3 p-3 bg-blue-50 border border-blue-100 rounded-xl text-sm font-bold text-blue-800';
                item.innerHTML = `<i data-lucide="paperclip" class="w-4 h-4 shrink-0"></i> <span class="truncate">${file.name}</span>`;
                list.appendChild(item);
            });
            lucide.createIcons();
        } else {
            list.classList.add('hidden');
        }
    }
</script>
@endsection
