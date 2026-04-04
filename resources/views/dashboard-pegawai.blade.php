@extends('layouts.app')

@section('title', 'Pegawai Self-Service')
@section('header-title', 'Portal Mandiri Pegawai')

@section('content')
<!-- Top Hero Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mb-12">
    <!-- Welcome Card -->
    <div class="lg:col-span-2 relative overflow-hidden bg-white p-12 rounded-[56px] border border-[#EFEFEF] shadow-sm flex flex-col justify-between group">
        <!-- Background Pattern -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-50 rounded-full blur-3xl opacity-20 -mr-20 -mt-20 group-hover:scale-125 transition-transform duration-1000"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-[#1E2432] rounded-full blur-3xl opacity-5 -ml-10 -mb-10 group-hover:scale-150 transition-transform duration-1000"></div>

        <div class="relative z-10">
            <h2 class="text-4xl font-black text-[#1E2432] tracking-tight mb-3">Selamat Datang, <span class="text-[#E85A4F]">{{ auth()->user()->name }}</span></h2>
            <div class="flex items-center gap-4 mt-4">
                <div class="px-4 py-2 bg-[#FCFBF9] border border-[#EFEFEF] rounded-2xl">
                    <p class="text-[9px] font-black text-[#8A8A8A] uppercase tracking-widest">NIP Pegawai</p>
                    <p class="text-xs font-bold text-[#1E2432]">{{ $employee->nip ?? '-' }}</p>
                </div>
                <div class="px-4 py-2 bg-[#FCFBF9] border border-[#EFEFEF] rounded-2xl">
                    <p class="text-[9px] font-black text-[#8A8A8A] uppercase tracking-widest">Pangkat / Golongan</p>
                    <p class="text-xs font-bold text-[#1E2432]">{{ $employee->rank ?? '-' }}</p>
                </div>
            </div>
        </div>
        
        <div class="mt-16 relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="award" class="w-5 h-5 text-[#E85A4F]"></i>
                    <span class="text-[10px] font-black text-[#1E2432] uppercase tracking-[0.2em]">Kelengkapan Berkas Wajib</span>
                </div>
                <span class="text-lg font-black text-[#E85A4F]">{{ number_format($careerProgress, 0) }}%</span>
            </div>
            <div class="w-full h-4 bg-[#FCFBF9] rounded-full overflow-hidden border border-[#EFEFEF] p-1 shadow-inner">
                <div class="bg-gradient-to-r from-[#E85A4F] to-[#d44d42] h-full rounded-full transition-all duration-1000 shadow-lg shadow-red-100" style="width: {{ $careerProgress }}%"></div>
            </div>
        </div>
    </div>

    <!-- Quick Salary Download -->
    <div class="bg-[#1E2432] p-12 rounded-[56px] text-white shadow-2xl flex flex-col justify-between overflow-hidden relative group">
        <!-- Abstract Shapes -->
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-[#E85A4F] rounded-full blur-[80px] opacity-20"></div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white rounded-full blur-[80px] opacity-5"></div>

        <div class="relative z-10">
            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mb-6 border border-white/10">
                <i data-lucide="banknote" class="w-6 h-6 text-white"></i>
            </div>
            <h3 class="text-2xl font-black leading-tight tracking-tight">Akses Cepat<br>Slip Gaji</h3>
            <p class="text-xs font-bold opacity-50 mt-4 leading-relaxed tracking-wide">Unduh slip gaji terbaru Anda tanpa harus mencari di dalam folder.</p>
        </div>

        <div class="relative z-10 mt-10">
            @if($latestSalary)
                <a href="{{ route('documents.download', $latestSalary->id) }}" target="_blank" class="flex items-center justify-center gap-3 bg-[#E85A4F] text-white w-full py-5 rounded-[24px] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-red-900/40 hover:bg-[#d44d42] transition-all no-loader active:scale-95 group">
                    Unduh Sekarang 
                    <i data-lucide="download" class="w-4 h-4 group-hover:translate-y-1 transition-transform"></i>
                </a>
            @else
                <div class="bg-white/5 border border-white/10 p-5 rounded-3xl text-center">
                    <p class="text-[10px] font-black opacity-40 uppercase tracking-widest">Belum ada slip gaji</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Stats & Recent Documents -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- Stats Row -->
    <div class="lg:col-span-1 space-y-8">
        <h3 class="text-xs font-black text-[#ABABAB] uppercase tracking-[0.4em] ml-2 mb-4">Status Arsip</h3>
        
        <div class="bg-white p-10 rounded-[48px] border border-[#EFEFEF] shadow-sm transform hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-green-50 rounded-[24px] flex items-center justify-center text-green-600 shadow-sm">
                    <i data-lucide="verified" class="w-8 h-8"></i>
                </div>
                <div>
                    <h4 class="text-4xl font-black text-[#1E2432]">{{ $verifiedDocs }}</h4>
                    <p class="text-[10px] font-black text-[#8A8A8A] uppercase tracking-widest mt-1">Terverifikasi</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[48px] border border-[#EFEFEF] shadow-sm transform hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-orange-50 rounded-[24px] flex items-center justify-center text-orange-600 shadow-sm">
                    <i data-lucide="clock" class="w-8 h-8"></i>
                </div>
                <div>
                    <h4 class="text-4xl font-black text-[#1E2432]">{{ $myDocumentsCount - $verifiedDocs }}</h4>
                    <p class="text-[10px] font-black text-[#8A8A8A] uppercase tracking-widest mt-1">Review Admin</p>
                </div>
            </div>
        </div>

        <!-- Problem Report CTA -->
        <div class="bg-gradient-to-br from-[#1E2432] to-[#343d52] p-10 rounded-[48px] shadow-xl text-white">
            <h4 class="text-lg font-black mb-2">Punya Kendala?</h4>
            <p class="text-xs font-bold opacity-60 mb-8 leading-relaxed">Hubungi admin kepegawaian jika data Anda tidak sesuai.</p>
            <button onclick="window.location='{{ route('profile.index') }}'" class="flex items-center justify-between w-full bg-white/10 border border-white/20 p-5 rounded-[24px] hover:bg-white/20 transition-all group">
                <span class="text-[10px] font-black uppercase tracking-widest">Buka Pelaporan</span>
                <i data-lucide="message-circle" class="w-5 h-5 text-[#E85A4F] group-hover:rotate-12 transition-transform"></i>
            </button>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="lg:col-span-2">
        <div class="flex items-center justify-between mb-8 ml-2">
            <h3 class="text-xs font-black text-[#ABABAB] uppercase tracking-[0.4em]">Unggahan Terakhir</h3>
            <a href="{{ route('documents.index') }}" class="text-[10px] font-black text-[#E85A4F] uppercase tracking-widest hover:underline">Lihat Semua</a>
        </div>

        <div class="bg-white rounded-[56px] border border-[#EFEFEF] shadow-sm overflow-hidden">
            <div class="divide-y divide-[#F5F4F2]">
                @forelse($recentDocuments as $doc)
                <div class="p-8 hover:bg-[#FCFBF9] transition-all flex items-center justify-between group">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 bg-[#F5F4F2] rounded-2xl flex items-center justify-center group-hover:bg-[#E85A4F] group-hover:text-white transition-all duration-500">
                            @if(str_contains($doc->file_path, '.pdf'))
                                <i data-lucide="file-text" class="w-6 h-6"></i>
                            @else
                                <i data-lucide="image" class="w-6 h-6"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-[#1E2432] group-hover:text-[#E85A4F] transition-colors">{{ $doc->title }}</h5>
                            <p class="text-[10px] font-black text-[#8A8A8A] uppercase tracking-widest mt-1">{{ $doc->category->name ?? 'Dokumen' }} • {{ $doc->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="window.open('{{ route('documents.preview', $doc->id) }}', '_blank')" class="w-10 h-10 bg-white border border-[#EFEFEF] rounded-xl flex items-center justify-center text-[#1E2432] hover:bg-[#1E2432] hover:text-white transition-all shadow-sm">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                @empty
                <div class="p-20 text-center">
                    <div class="w-20 h-20 bg-[#FCFBF9] rounded-[32px] flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="folder-open" class="w-8 h-8 text-[#ABABAB]"></i>
                    </div>
                    <p class="text-xs font-black text-[#ABABAB] uppercase tracking-widest">Belum ada dokumen yang diunggah</p>
                </div>
                @endforelse
            </div>
            
            @if($recentDocuments->count() > 0)
            <div class="p-8 bg-[#FCFBF9]/50 text-center">
                <p class="text-[9px] font-black text-[#ABABAB] uppercase tracking-[0.3em]">Hanya menampilkan 5 dokumen terakhir</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
