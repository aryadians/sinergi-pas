@extends('layouts.app')

@section('title', 'Dokumen Saya')
@section('header-title', 'Pusat Dokumen Pribadi')

@section('content')
<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
    <div class="text-sm">
        <p class="text-[#8A8A8A]">Halo, <span class="text-[#1E2432] font-black">{{ auth()->user()->name }}</span>. Berikut adalah dokumen Anda.</p>
    </div>
    
    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
        class="bg-[#E85A4F] text-white px-8 py-3.5 rounded-2xl font-black hover:bg-[#d44d42] transition-all flex items-center gap-2 shadow-lg shadow-red-100 active:scale-95">
        <i data-lucide="upload-cloud" class="w-5 h-5 text-white"></i>
        Unggah Dokumen Baru
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-8">
    @foreach($documents as $doc)
    <div class="group bg-white p-8 rounded-[40px] border border-[#EFEFEF] hover:border-[#E85A4F] hover:shadow-2xl hover:shadow-red-100/50 transition-all duration-500 transform hover:-translate-y-2 flex flex-col justify-between h-[280px]">
        <div class="flex justify-between items-start">
            <div class="w-14 h-14 bg-[#F5F4F2] rounded-2xl flex items-center justify-center text-[#8A8A8A] group-hover:bg-[#E85A4F] group-hover:text-white transition-all duration-500">
                @if(str_contains($doc->file_path, '.pdf'))
                    <i data-lucide="file-text" class="w-7 h-7 text-red-500 group-hover:text-white"></i>
                @elseif(str_contains($doc->file_path, '.xls') || str_contains($doc->file_path, '.xlsx'))
                    <i data-lucide="file-spreadsheet" class="w-7 h-7 text-green-600 group-hover:text-white"></i>
                @elseif(str_contains($doc->file_path, '.csv'))
                    <i data-lucide="file-type" class="w-7 h-7 text-blue-400 group-hover:text-white"></i>
                @elseif(str_contains($doc->file_path, '.doc') || str_contains($doc->file_path, '.docx'))
                    <i data-lucide="file-text" class="w-7 h-7 text-blue-600 group-hover:text-white"></i>
                @elseif(str_contains($doc->file_path, '.jpg') || str_contains($doc->file_path, '.jpeg') || str_contains($doc->file_path, '.png'))
                    <i data-lucide="image" class="w-7 h-7 text-blue-500 group-hover:text-white"></i>
                @else
                    <i data-lucide="file" class="w-7 h-7"></i>
                @endif
            </div>
            <div class="flex gap-2">
                <button onclick="openPreview('{{ route('documents.preview', $doc->id) }}', '{{ $doc->title }}', '{{ $doc->file_path }}', {{ json_encode($doc->versions) }})" class="bg-green-50 p-3 rounded-xl text-green-600 hover:bg-green-600 hover:text-white transition-all">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                </button>
                <button onclick="openRevisionModal({{ $doc->id }}, '{{ $doc->title }}')" class="bg-blue-50 p-3 rounded-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all" title="Unggah Revisi">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
                @if(!$doc->is_locked)
                <a href="{{ route('documents.download', $doc->id) }}" target="_blank" class="bg-[#FCFBF9] p-3 rounded-xl text-[#E85A4F] hover:bg-[#E85A4F] hover:text-white transition-all no-loader">
                    <i data-lucide="download" class="w-4 h-4"></i>
                </a>
                @else
                <div class="bg-gray-50 p-3 rounded-xl text-gray-300 cursor-not-allowed border border-gray-100" title="Unduhan dikunci Admin">
                    <i data-lucide="download" class="w-4 h-4 opacity-50"></i>
                </div>
                @endif
                @if(!$doc->is_locked)
                <form id="deleteDoc-{{ $doc->id }}" action="{{ route('documents.destroy', $doc->id) }}" method="POST" class="no-loader">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDocDelete({{ $doc->id }})" class="bg-red-50 p-3 rounded-xl text-red-500 hover:bg-red-500 hover:text-white transition-all">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
                @else
                <div class="bg-gray-100 p-3 rounded-xl text-gray-400 cursor-not-allowed" title="Dokumen dikunci Admin">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                </div>
                @endif
            </div>
        </div>
...
    function openPreview(url, title, filePath, versions = []) {
        ...
    }

    function confirmDocDelete(id) {
        Swal.fire({
            title: 'Hapus Dokumen?',
            text: "Berkas ini akan dihapus permanen dari sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E85A4F',
            cancelButtonColor: '#1E2432',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[48px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteDoc-' + id).submit();
            }
        });
    }
</script>
...
