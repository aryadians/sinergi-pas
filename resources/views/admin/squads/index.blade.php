@extends('layouts.app')

@section('title', 'Manajemen Regu')
@section('header-title', 'Kelola Regu Jaga')

@section('content')
<div class="space-y-8 page-fade">
    <!-- Header & Tools -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 transition-colors font-bold text-[10px] uppercase tracking-widest">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Jadwal
            </a>
        </div>

        <button onclick="document.getElementById('squadModal').classList.remove('hidden')" class="px-6 py-3 rounded-xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-wider hover:bg-blue-600 transition-all shadow-lg btn-3d flex items-center justify-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Regu Baru
        </button>
    </div>

    <!-- Squad Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($squads as $squad)
        <div class="bg-white p-8 rounded-[40px] border border-slate-200 shadow-sm hover:border-blue-300 transition-all card-3d group">
            <div class="flex justify-between items-start mb-6">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 flex items-center justify-center border border-slate-100 transition-all">
                    <i data-lucide="users" class="w-7 h-7"></i>
                </div>
                <div class="flex gap-2">
                    <button onclick="openEditModal({{ json_encode($squad) }})" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </button>
                    <form action="{{ route('admin.squads.destroy', $squad->id) }}" method="POST" class="no-loader">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus regu ini?')" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>

            <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $squad->name }}</h3>
            <p class="text-xs text-slate-500 font-medium mb-6 line-clamp-2 h-8">{{ $squad->description ?? 'Tidak ada deskripsi.' }}</p>
            
            <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total Anggota</p>
                    <p class="text-lg font-bold text-slate-900">{{ $squad->employees_count }} <span class="text-[10px] text-slate-400 font-semibold">Petugas</span></p>
                </div>
                <button onclick="openManageMembersModal({{ json_encode($squad->load('employees')) }})" class="px-5 py-2.5 rounded-xl bg-slate-50 text-slate-600 font-bold text-[10px] uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">
                    Kelola Anggota
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-24 text-center bg-white rounded-[48px] border border-dashed border-slate-200">
            <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i data-lucide="shield-alert" class="w-10 h-10 text-slate-200"></i>
            </div>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-[0.2em] italic">Belum ada regu jaga yang terdaftar</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Squad Modal (Create) -->
<div id="squadModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[32px] p-10 shadow-2xl animate-in zoom-in duration-200">
        <h3 class="text-2xl font-bold text-slate-900 mb-8">Tambah Regu Baru</h3>
        <form action="{{ route('admin.squads.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Regu</label>
                <input type="text" name="name" required placeholder="Contoh: Regu A" class="w-full px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 bg-slate-50 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Opsional..." class="w-full px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 bg-slate-50 outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('squadModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-[2] py-4 bg-slate-900 text-white rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg">Simpan Regu</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[32px] p-10 shadow-2xl animate-in zoom-in duration-200">
        <h3 class="text-2xl font-bold text-slate-900 mb-8">Edit Regu</h3>
        <form id="editForm" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Regu</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 bg-slate-50 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi</label>
                <textarea name="description" id="edit_description" rows="3" class="w-full px-5 py-3 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 bg-slate-50 outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-[2] py-4 bg-blue-600 text-white rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Manage Members Modal -->
<div id="membersModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-4xl rounded-[40px] shadow-2xl animate-in zoom-in duration-200 flex flex-col max-h-[90vh]">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center shrink-0">
            <div>
                <h3 id="members_squad_name" class="text-2xl font-bold text-slate-900">Kelola Anggota Regu</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Daftar Petugas Jaga</p>
            </div>
            <button onclick="document.getElementById('membersModal').classList.add('hidden')" class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">
            <!-- Current Members -->
            <div class="flex-1 p-8 border-r border-slate-100 overflow-y-auto custom-scrollbar">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Anggota Saat Ini</h4>
                <div id="current_members_list" class="space-y-3">
                    <!-- Member list injected here -->
                </div>
            </div>

            <!-- Add Members -->
            <div class="lg:w-96 p-8 bg-slate-50 overflow-y-auto custom-scrollbar">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Tambah Anggota</h4>
                <form id="addMemberForm" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="max-h-96 overflow-y-auto pr-2 space-y-2">
                            @forelse($unassignedEmployees as $emp)
                            <label class="flex items-center gap-3 p-3 bg-white rounded-2xl border border-slate-200 cursor-pointer hover:border-blue-400 transition-all">
                                <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="w-5 h-5 rounded-lg border-slate-200 text-blue-600 focus:ring-0">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-slate-900 truncate">{{ $emp->full_name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 truncate">NIP. {{ $emp->nip }}</p>
                                </div>
                            </label>
                            @empty
                            <p class="text-center text-[10px] font-bold text-slate-400 italic py-4">Semua petugas regu jaga sudah memiliki regu.</p>
                            @endforelse
                        </div>
                        <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all">
                            Masukkan ke Regu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditModal(squad) {
        document.getElementById('edit_name').value = squad.name;
        document.getElementById('edit_description').value = squad.description;
        document.getElementById('editForm').action = `/admin/squads/${squad.id}`;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function openManageMembersModal(squad) {
        document.getElementById('members_squad_name').innerText = `Kelola Anggota ${squad.name}`;
        document.getElementById('addMemberForm').action = `/admin/squads/${squad.id}/add-member`;
        
        const membersList = document.getElementById('current_members_list');
        membersList.innerHTML = '';
        
        if (squad.employees.length === 0) {
            membersList.innerHTML = `<p class="text-center text-[10px] font-bold text-slate-400 italic py-10">Belum ada anggota di regu ini.</p>`;
        } else {
            squad.employees.forEach(emp => {
                membersList.innerHTML += `
                    <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 font-bold text-xs uppercase">
                                ${emp.full_name.substring(0, 1)}
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-slate-900">${emp.full_name}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">NIP. ${emp.nip}</p>
                            </div>
                        </div>
                        <form action="/admin/squads/${squad.id}/remove-member" method="POST" class="no-loader">
                            @csrf
                            <input type="hidden" name="employee_id" value="${emp.id}">
                            <button type="submit" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                <i data-lucide="user-minus" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                `;
            });
        }
        
        document.getElementById('membersModal').classList.remove('hidden');
        lucide.createIcons();
    }
</script>

@if(session('success'))
<script>
    window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", confirmButtonColor: '#0F172A', customClass: { popup: 'rounded-2xl' } });
    });
</script>
@endif
@endsection
