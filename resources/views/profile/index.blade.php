@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header-title', 'Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="bg-white rounded-[40px] border border-[#EFEFEF] shadow-sm overflow-hidden mb-10">
            <div class="h-32 bg-[#E85A4F]"></div>
            <div class="px-10 pb-10">
                <div class="relative -mt-16 mb-8 inline-block">
                    <div class="w-32 h-32 rounded-3xl border-4 border-white bg-[#F5F4F2] overflow-hidden flex items-center justify-center text-[#8A8A8A]">
                        @if($employee && $employee->photo)
                            <img src="{{ Storage::url($employee->photo) }}" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="user" class="w-12 h-12"></i>
                        @endif
                    </div>
                    <label for="photoInput" class="absolute -bottom-2 -right-2 bg-white p-2 rounded-xl border border-[#EFEFEF] shadow-lg cursor-pointer hover:bg-gray-50">
                        <i data-lucide="camera" class="w-4 h-4 text-[#E85A4F]"></i>
                        <input type="file" id="photoInput" name="photo" class="hidden">
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#1E2432] uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" required class="w-full px-5 py-3.5 rounded-2xl border border-[#EFEFEF] bg-[#FCFBF9] text-sm outline-none focus:ring-2 focus:ring-[#E85A4F]">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#1E2432] uppercase tracking-wider">Email (Read Only)</label>
                        <input type="email" value="{{ $user->email }}" readonly class="w-full px-5 py-3.5 rounded-2xl border border-[#EFEFEF] bg-gray-50 text-[#8A8A8A] text-sm outline-none">
                    </div>
                    
                    <div class="md:col-span-2 pt-4 border-t border-[#EFEFEF] mt-4">
                        <h4 class="text-sm font-bold text-[#1E2432] mb-6">Ganti Password</h4>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#1E2432] uppercase tracking-wider">Password Baru</label>
                        <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin ganti" class="w-full px-5 py-3.5 rounded-2xl border border-[#EFEFEF] bg-[#FCFBF9] text-sm outline-none focus:ring-2 focus:ring-[#E85A4F]">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#1E2432] uppercase tracking-wider">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full px-5 py-3.5 rounded-2xl border border-[#EFEFEF] bg-[#FCFBF9] text-sm outline-none focus:ring-2 focus:ring-[#E85A4F]">
                    </div>
                </div>

                <div class="mt-10 flex justify-end">
                    <button type="submit" class="bg-[#E85A4F] text-white px-10 py-4 rounded-2xl font-bold hover:bg-[#d44d42] transition-all shadow-lg shadow-red-100">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@if(session('success'))
<script>
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#E85A4F' });
</script>
@endif
@endsection
