@extends('layouts.app')

@section('title', 'Dokumen Saya')
@section('header-title', 'Pusat Dokumen Pribadi')

@section('content')
<div class="mb-10 text-sm">
    <p class="text-[#8A8A8A]">Halo, <span class="text-[#1E2432] font-bold">{{ auth()->user()->name }}</span>. Berikut adalah seluruh dokumen kepegawaian Anda.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-8">
    @foreach($documents as $doc)
    <div class="group bg-white p-8 rounded-[32px] border border-[#EFEFEF] hover:border-[#E85A4F] hover:shadow-2xl hover:shadow-red-100 transition-all duration-300 transform hover:-translate-y-2 flex flex-col justify-between h-[220px]">
        <div class="flex justify-between items-start">
            <div class="w-14 h-14 bg-[#F5F4F2] rounded-2xl flex items-center justify-center text-[#8A8A8A] group-hover:bg-[#E85A4F] group-hover:text-white transition-all duration-300">
                @if(str_contains($doc->file_path, '.pdf'))
                    <i data-lucide="file-text" class="w-7 h-7 text-red-500 group-hover:text-white"></i>
                @elseif(str_contains($doc->file_path, '.xls'))
                    <i data-lucide="file-spreadsheet" class="w-7 h-7 text-green-600 group-hover:text-white"></i>
                @else
                    <i data-lucide="file" class="w-7 h-7"></i>
                @endif
            </div>
            <a href="{{ route('documents.download', $doc->id) }}" class="bg-[#FCFBF9] p-3 rounded-xl text-[#E85A4F] hover:bg-[#E85A4F] hover:text-white transition-all">
                <i data-lucide="download" class="w-5 h-5"></i>
            </a>
        </div>
        <div>
            <h3 class="text-lg font-bold text-[#1E2432] truncate group-hover:text-[#E85A4F] transition-all" title="{{ $doc->title }}">{{ $doc->title }}</h3>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2 py-0.5 bg-gray-100 text-[#1E2432] text-[10px] font-bold rounded-md uppercase tracking-wider">{{ $doc->category->name }}</span>
                <span class="text-xs text-[#8A8A8A]">{{ $doc->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>
    @endforeach

    @if($documents->isEmpty())
    <div class="col-span-4 py-20 text-center bg-white rounded-[40px] border border-dashed border-[#EFEFEF]">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
            <i data-lucide="inbox" class="w-10 h-10"></i>
        </div>
        <p class="text-[#8A8A8A]">Anda belum memiliki dokumen terunggah.</p>
    </div>
    @endif
</div>
@endsection
