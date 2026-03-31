@extends('layouts.app')

@section('title', 'Dashboard')
@section('header-title', 'Overview Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <!-- Stat Cards -->
    <div class="bg-white p-6 rounded-2xl border border-[#EFEFEF] shadow-sm hover:shadow-md transition-all">
        <p class="text-sm font-medium text-[#8A8A8A] mb-4">Total Pegawai</p>
        <div class="flex items-end justify-between">
            <h3 class="text-3xl font-bold text-[#1E2432]">124</h3>
            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-lg">+12% dari bulan lalu</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-[#EFEFEF] shadow-sm hover:shadow-md transition-all">
        <p class="text-sm font-medium text-[#8A8A8A] mb-4">Slip Gaji Terbit</p>
        <div class="flex items-end justify-between">
            <h3 class="text-3xl font-bold text-[#1E2432]">1,042</h3>
            <span class="text-xs font-semibold text-[#8A8A8A] bg-[#F5F4F2] px-2 py-1 rounded-lg">Maret 2026</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-[#EFEFEF] shadow-sm hover:shadow-md transition-all">
        <p class="text-sm font-medium text-[#8A8A8A] mb-4">SKP Selesai</p>
        <div class="flex items-end justify-between">
            <h3 class="text-3xl font-bold text-[#1E2432]">98%</h3>
            <span class="text-xs font-semibold text-[#E85A4F] bg-red-50 px-2 py-1 rounded-lg">Target: 100%</span>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-2xl border border-[#EFEFEF] shadow-sm overflow-hidden">
    <div class="p-8 border-b border-[#EFEFEF] flex justify-between items-center">
        <h3 class="text-lg font-bold text-[#1E2432]">Daftar Pegawai Terbaru</h3>
        <button class="bg-[#E85A4F] text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#d44d42] transition-all flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Pegawai
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#FCFBF9]">
                    <th class="px-8 py-4 text-xs font-bold text-[#8A8A8A] uppercase tracking-wider">Nama Pegawai</th>
                    <th class="px-8 py-4 text-xs font-bold text-[#8A8A8A] uppercase tracking-wider">NIP</th>
                    <th class="px-8 py-4 text-xs font-bold text-[#8A8A8A] uppercase tracking-wider">Jabatan</th>
                    <th class="px-8 py-4 text-xs font-bold text-[#8A8A8A] uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#EFEFEF]">
                <tr class="hover:bg-[#FCFBF9] transition-all">
                    <td class="px-8 py-5 text-sm font-semibold text-[#1E2432]">Ahmad Fauzi</td>
                    <td class="px-8 py-5 text-sm text-[#8A8A8A]">19850412 201012 1 001</td>
                    <td class="px-8 py-5 text-sm text-[#8A8A8A]">Kepala Sub Bagian Umum</td>
                    <td class="px-8 py-5 text-sm text-center">
                        <div class="flex justify-center gap-3">
                            <button class="p-2 text-[#8A8A8A] hover:text-[#1E2432] transition-all"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                            <button class="p-2 text-[#E85A4F] hover:text-[#d44d42] transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </div>
                    </td>
                </tr>
                <tr class="hover:bg-[#FCFBF9] transition-all">
                    <td class="px-8 py-5 text-sm font-semibold text-[#1E2432]">Siti Aminah</td>
                    <td class="px-8 py-5 text-sm text-[#8A8A8A]">19900821 201503 2 002</td>
                    <td class="px-8 py-5 text-sm text-[#8A8A8A]">Analis Kepegawaian</td>
                    <td class="px-8 py-5 text-sm text-center">
                        <div class="flex justify-center gap-3">
                            <button class="p-2 text-[#8A8A8A] hover:text-[#1E2432] transition-all"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                            <button class="p-2 text-[#E85A4F] hover:text-[#d44d42] transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
