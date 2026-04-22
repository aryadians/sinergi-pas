@extends('layouts.app')

@section('title', 'Folder ' . $employee->full_name)
@section('header-title', 'Dokumen ' . $employee->full_name)

@section('content')
<form id="bulkDocForm" action="{{ route('documents.bulk-action') }}" method="POST">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput" value="">

    <!-- Sub Header & Tabs -->
    <div class="mb-10 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-3 text-sm mb-1">
                <a href="{{ route('documents.index') }}" class="text-[#8A8A8A] hover:text-[#EAB308] transition-all font-bold uppercase tracking-widest text-[9px] flex items-center gap-1.5 group">
                    <i data-lucide="chevron-left" class="w-3 h-3 transition-transform group-hover:-translate-x-0.5"></i>
                    Pusat Dokumen
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-400 font-bold uppercase tracking-widest text-[9px]">Arsip Pegawai</span>
            </div>
            <h1 class="text-3xl font-black text-[#0F172A] tracking-tighter italic">
                {{ $employee->full_name }}
                <span class="text-[#EAB308] font-mono text-sm ml-2 not-italic opacity-60 tracking-normal">/ {{ $employee->nip }}</span>
            </h1>
        </div>
        
        <div class="flex gap-3 items-center w-full md:w-auto">
            <!-- Dynamic Bulk Actions -->
            <div id="bulkActions" class="hidden gap-3 animate-in fade-in slide-in-from-right-4 duration-300">
                <button type="button" onclick="submitBulk('unlock')" class="bg-white text-[#0F172A] px-5 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all border border-slate-200 shadow-sm flex items-center gap-2">
                    <i data-lucide="unlock" class="w-3.5 h-3.5 text-blue-600"></i> Buka Kunci
                </button>
                <button type="button" onclick="submitBulk('lock')" class="bg-[#0F172A] text-white px-5 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg flex items-center gap-2">
                    <i data-lucide="lock" class="w-3.5 h-3.5 text-amber-400"></i> Kunci
                </button>
                <button type="button" onclick="submitBulk('delete')" class="bg-red-600 text-white px-5 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-100 flex items-center gap-2">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                </button>
            </div>

            <button type="button" onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                class="bg-[#EAB308] text-white px-8 py-4 rounded-2xl font-black hover:bg-[#CA8A04] transition-all flex items-center gap-3 shadow-xl shadow-amber-100 active:scale-95 ml-auto md:ml-0">
                <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                <span class="uppercase tracking-widest text-[11px]">Unggah File</span>
            </button>
        </div>
    </div>

    <!-- Category Tabs - Polished -->
    <div class="relative mb-12">
        <div class="flex bg-slate-100/50 p-1.5 rounded-[28px] border border-slate-200/60 shadow-inner overflow-x-auto max-w-full custom-scrollbar no-scrollbar-mobile">
            <a href="{{ route('documents.employee', $employee->id) }}" 
                class="relative px-8 py-3.5 rounded-[22px] text-[10px] font-black uppercase tracking-widest transition-all shrink-0 flex items-center gap-3 {{ !request('category_id') ? 'bg-white text-[#0F172A] shadow-md border border-slate-100' : 'text-slate-500 hover:text-[#0F172A] hover:bg-slate-200/50' }}">
                <i data-lucide="layout-grid" class="w-3.5 h-3.5 {{ !request('category_id') ? 'text-[#EAB308]' : 'opacity-40' }}"></i>
                Semua File
                <span class="px-2 py-0.5 rounded-full text-[8px] {{ !request('category_id') ? 'bg-[#0F172A] text-white' : 'bg-slate-200 text-slate-600' }}">{{ $totalDocs }}</span>
            </a>
            
            <div class="w-px h-6 bg-slate-200 my-auto mx-2 shrink-0"></div>

            @foreach($categories as $cat)
            <a href="{{ route('documents.employee', ['employee' => $employee->id, 'category_id' => $cat->id]) }}" 
                class="relative px-8 py-3.5 rounded-[22px] text-[10px] font-black uppercase tracking-widest transition-all shrink-0 flex items-center gap-3 {{ request('category_id') == $cat->id ? 'bg-white text-[#0F172A] shadow-md border border-slate-100' : 'text-slate-500 hover:text-[#0F172A] hover:bg-slate-200/50' }}">
                @php
                    $icon = 'file-text';
                    if(str_contains(strtolower($cat->name), 'gaji')) $icon = 'banknote';
                    if(str_contains(strtolower($cat->name), 'sk')) $icon = 'award';
                    if(str_contains(strtolower($cat->name), 'ijazah')) $icon = 'graduation-cap';
                    if(str_contains(strtolower($cat->name), 'ktp') || str_contains(strtolower($cat->name), 'identitas')) $icon = 'contact-2';
                @endphp
                <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5 {{ request('category_id') == $cat->id ? 'text-[#EAB308]' : 'opacity-40' }}"></i>
                {{ $cat->name }}
                @if($cat->documents_count > 0)
                <span class="px-2 py-0.5 rounded-full text-[8px] {{ request('category_id') == $cat->id ? 'bg-[#EAB308] text-white' : 'bg-slate-200 text-slate-600' }}">{{ $cat->documents_count }}</span>
                @endif
            </a>
            @endforeach
        </div>
        
        <!-- Subtle mobile scroll indicator -->
        <div class="md:hidden absolute -bottom-4 left-1/2 -translate-x-1/2 flex gap-1 opacity-20">
            <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
            <div class="w-4 h-1.5 rounded-full bg-slate-400"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
        </div>
    </div>

    <!-- Files Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        @forelse($documents as $doc)
        <div class="group relative bg-white p-8 rounded-[40px] border border-slate-100 hover:border-[#EAB308] hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col justify-between h-[300px] card-3d">
            <!-- Checkbox for Bulk (Always Visible for Easy Interaction) -->
            <div class="absolute top-7 left-7 z-10">
                <input type="checkbox" name="ids[]" value="{{ $doc->id }}" class="doc-checkbox w-6 h-6 rounded-xl border-2 border-slate-200 text-[#EAB308] focus:ring-0 cursor-pointer transition-all checked:border-[#EAB308]">
            </div>

            <div class="flex justify-end items-start mb-4">
                <div class="flex gap-1.5 flex-wrap justify-end opacity-0 group-hover:opacity-100 transition-all duration-300">
                    @if($doc->status === 'pending')
                    <button type="button" onclick="verifyDoc({{ $doc->id }})" class="p-2.5 text-green-600 bg-green-50 hover:bg-green-600 hover:text-white rounded-xl transition-all shadow-sm" title="Verifikasi">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="rejectDoc({{ $doc->id }})" class="p-2.5 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white rounded-xl transition-all shadow-sm" title="Tolak">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                    </button>
                    @endif
                    
                    <button type="button" onclick="toggleLock({{ $doc->id }})" class="p-2.5 {{ $doc->is_locked ? 'text-red-600 bg-red-100' : 'text-slate-400 bg-slate-50' }} hover:bg-red-600 hover:text-white rounded-xl transition-all shadow-sm" title="Kunci/Buka">
                        <i data-lucide="{{ $doc->is_locked ? 'lock' : 'unlock' }}" class="w-4 h-4"></i>
                    </button>

                    @if(!$doc->is_locked && auth()->user()->role === 'superadmin')
                    @php
                        $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                    @endphp
                    <button type="button" onclick="showDoc('{{ route('documents.view', $doc->id) }}', '{{ $doc->title }}', {{ $isImage ? 'true' : 'false' }})" class="p-2.5 text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm" title="Lihat">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                    @endif
                    
                    @if(!$doc->is_locked)
                    <a href="{{ route('documents.download', $doc->id) }}" class="p-2.5 text-purple-600 bg-purple-50 hover:bg-purple-600 hover:text-white rounded-xl no-loader shadow-sm" title="Unduh">
                        <i data-lucide="download" class="w-4 h-4"></i>
                    </a>
                    @else
                    <div class="p-2.5 text-slate-300 bg-slate-50 rounded-xl cursor-not-allowed border border-slate-100" title="Unduhan dikunci">
                        <i data-lucide="download" class="w-4 h-4 opacity-50"></i>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col items-center justify-center flex-1 mb-4">
                <div class="w-20 h-20 bg-slate-50 rounded-[28px] flex items-center justify-center text-slate-400 group-hover:bg-[#EAB308] group-hover:text-white transition-all duration-500 shadow-sm border border-slate-100 group-hover:border-[#EAB308]">
                    @if(str_contains($doc->file_path, '.pdf'))
                        <i data-lucide="file-text" class="w-10 h-10 text-red-500 group-hover:text-white"></i>
                    @elseif(str_contains($doc->file_path, '.xls') || str_contains($doc->file_path, '.xlsx'))
                        <i data-lucide="file-spreadsheet" class="w-10 h-10 text-green-600 group-hover:text-white"></i>
                    @elseif(str_contains($doc->file_path, '.csv'))
                        <i data-lucide="file-type" class="w-10 h-10 text-blue-400 group-hover:text-white"></i>
                    @elseif(str_contains($doc->file_path, '.doc') || str_contains($doc->file_path, '.docx'))
                        <i data-lucide="file-text" class="w-10 h-10 text-blue-600 group-hover:text-white"></i>
                    @elseif(str_contains($doc->file_path, '.jpg') || str_contains($doc->file_path, '.jpeg') || str_contains($doc->file_path, '.png'))
                        <i data-lucide="image" class="w-10 h-10 text-amber-500 group-hover:text-white"></i>
                    @else
                        <i data-lucide="file" class="w-10 h-10"></i>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div class="text-center">
                    <h4 class="text-sm font-black text-[#0F172A] truncate px-2" title="{{ $doc->title }}">{{ $doc->title }}</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $doc->category->name ?? 'Tanpa Kategori' }}</p>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                    @if($doc->status === 'verified')
                        <span class="text-[8px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100 italic">Verified</span>
                    @elseif($doc->status === 'rejected')
                        <span class="text-[8px] font-black text-red-600 uppercase tracking-widest bg-red-50 px-2 py-0.5 rounded-md border border-red-100 italic cursor-help" title="Alasan: {{ $doc->rejection_reason }}">Rejected</span>
                    @else
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100 italic">Pending</span>
                    @endif
                    <span class="text-[9px] font-bold text-slate-300 italic">{{ $doc->created_at->format('d/m/y') }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 flex flex-col items-center justify-center bg-white rounded-[48px] border border-dashed border-slate-200">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                <i data-lucide="folder-search" class="w-10 h-10 text-slate-300"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight italic">Belum Ada Dokumen</h3>
            <p class="text-sm font-medium text-slate-400 mt-2">Tidak ditemukan file di kategori ini untuk pegawai tersebut.</p>
        </div>
        @endforelse
    </div>
</form>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black/80 hidden flex items-center justify-center z-[60] p-4 backdrop-blur-md">
    <div class="bg-white w-full max-w-5xl h-[90vh] rounded-[40px] flex flex-col overflow-hidden animate-in zoom-in duration-300">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center shrink-0">
            <div>
                <h3 id="viewModalTitle" class="text-xl font-black text-gray-900 truncate max-w-md">Detail Dokumen</h3>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Preview Arsip Digital</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('viewModal').classList.add('hidden')" class="bg-gray-100 p-3 rounded-2xl text-gray-400 hover:text-red-500 transition-all border border-gray-200">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <div class="flex-1 bg-gray-50 relative">
            <iframe id="viewIframe" src="" class="w-full h-full border-none"></iframe>
            <div id="imageContainer" class="hidden w-full h-full overflow-auto flex items-center justify-center p-8">
                <img id="viewImage" src="" class="max-w-full max-h-full rounded-2xl shadow-2xl">
            </div>
        </div>
    </div>
</div>

<style>
    .no-scrollbar-mobile::-webkit-scrollbar { display: none; }
    @media (min-width: 768px) { .no-scrollbar-mobile::-webkit-scrollbar { display: block; } }
</style>

<script>
    const docCheckboxes = document.querySelectorAll('.doc-checkbox');
    const bulkActionsDiv = document.getElementById('bulkActions');

    docCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.doc-checkbox:checked').length;
            if (checkedCount > 0) {
                bulkActionsDiv.classList.remove('hidden');
                bulkActionsDiv.classList.add('flex');
            } else {
                bulkActionsDiv.classList.add('hidden');
                bulkActionsDiv.classList.remove('flex');
            }
        });
    });

    function handleDownload(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function showDoc(url, title, isImage) {
        const modal = document.getElementById('viewModal');
        const iframe = document.getElementById('viewIframe');
        const imgContainer = document.getElementById('imageContainer');
        const img = document.getElementById('viewImage');
        document.getElementById('viewModalTitle').innerText = title;

        if (isImage) {
            iframe.classList.add('hidden');
            imgContainer.classList.remove('hidden');
            img.src = url;
        } else {
            imgContainer.classList.add('hidden');
            iframe.classList.remove('hidden');
            iframe.src = url;
        }

        modal.classList.remove('hidden');
        lucide.createIcons();
    }

    function submitBulk(action) {
        Swal.fire({
            title: 'Konfirmasi Aksi Massal',
            text: "Anda akan menjalankan " + action + " pada dokumen terpilih.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#EAB308',
            confirmButtonText: 'Ya, Jalankan!',
            customClass: { popup: 'rounded-[40px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulkActionInput').value = action;
                document.getElementById('bulkDocForm').submit();
            }
        });
    }

    function verifyDoc(id) {
        Swal.fire({
            title: 'Verifikasi Dokumen?',
            text: "Dokumen akan ditandai valid dan dikunci.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0F172A',
            confirmButtonText: 'Ya, Verifikasi!',
            customClass: { popup: 'rounded-[40px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/documents/${id}/verify`;
                form.innerHTML = `@csrf`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function rejectDoc(id) {
        Swal.fire({
            title: 'Tolak Dokumen?',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan',
            inputPlaceholder: 'Berikan alasan mengapa dokumen ini ditolak...',
            inputAttributes: { 'aria-label': 'Alasan Penolakan' },
            showCancelButton: true,
            confirmButtonColor: '#EAB308',
            confirmButtonText: 'Tolak Dokumen',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[40px]' },
            inputValidator: (value) => {
                if (!value) return 'Alasan harus diisi!'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/documents/${id}/reject`;
                form.innerHTML = `@csrf <input type="hidden" name="rejection_reason" value="${result.value}">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function toggleLock(id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/documents/${id}/toggle-lock`;
        form.innerHTML = `@csrf`;
        document.body.appendChild(form);
        form.submit();
    }
</script>

<!-- Upload Modal (Tetap Ada) -->
<div id="uploadModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-md">
    <div class="bg-white w-full max-w-lg rounded-[48px] p-12 shadow-2xl animate-in zoom-in duration-300">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h3 class="text-2xl font-black text-[#0F172A] tracking-tight">Unggah Arsip Baru</h3>
                <p class="text-[10px] font-bold text-[#8A8A8A] uppercase tracking-widest mt-1">Ke Akun: {{ $employee->full_name }}</p>
            </div>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="bg-[#F1F5F9] p-3 rounded-2xl text-[#8A8A8A] hover:text-red-500 transition-all border border-[#EFEFEF]">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <div class="space-y-3">
                <label class="text-[10px] font-black text-[#0F172A] uppercase tracking-[0.2em] ml-1">Jenis Dokumen</label>
                <select name="document_category_id" required class="w-full px-6 py-4 rounded-3xl border border-[#EFEFEF] bg-[#F1F5F9] text-sm font-bold outline-none focus:ring-4 focus:ring-red-500/5 appearance-none cursor-pointer">
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-3">
                <label class="text-[10px] font-black text-[#0F172A] uppercase tracking-[0.2em] ml-1">Judul Dokumen</label>
                <input type="text" name="title" required placeholder="Contoh: SK Pengangkatan 2026" class="w-full px-6 py-4 rounded-3xl border border-[#EFEFEF] bg-[#F1F5F9] text-sm font-bold outline-none focus:ring-4 focus:ring-red-500/5 transition-all">
            </div>
            <div class="space-y-3">
                <label class="text-[10px] font-black text-[#0F172A] uppercase tracking-[0.2em] ml-1">Pilih File</label>
                <div class="relative group">
                    <input type="file" name="file" required class="w-full px-6 py-10 rounded-3xl border-2 border-dashed border-[#EFEFEF] bg-[#F1F5F9] text-xs font-bold text-[#8A8A8A] file:hidden cursor-pointer hover:border-[#EAB308] transition-all text-center">
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none opacity-60 group-hover:opacity-100 transition-opacity">
                        <i data-lucide="file-up" class="w-10 h-10 text-[#EAB308] mb-3"></i>
                        <span class="text-[10px] uppercase font-black tracking-tighter">Klik atau Seret File Kesini</span>
                    </div>
                </div>
            </div>
            <button type="submit" class="w-full bg-[#EAB308] text-white py-5 rounded-[28px] font-black text-lg hover:bg-[#CA8A04] transition-all shadow-xl shadow-red-200 active:scale-95 flex items-center justify-center gap-3">
                Proses Sinkronisasi <i data-lucide="zap" class="w-5 h-5"></i>
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#EAB308', customClass: { popup: 'rounded-[40px]' } });
</script>
@endif
@endsection
