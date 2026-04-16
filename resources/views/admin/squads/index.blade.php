@extends('layouts.app')

@section('title', 'Manajemen Regu')
@section('header-title', 'Kelola Regu Jaga')

@section('content')
<div class="space-y-8 page-fade">
    <!-- Header & Tools -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 transition-colors font-bold text-[10px] uppercase tracking-widest">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
            <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-xl border border-slate-200 shadow-sm">
                <input type="checkbox" id="selectAllSquads" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih Semua</span>
            </div>
            <button type="button" onclick="confirmBulkDelete()" id="btnDeleteSquads" class="hidden px-6 py-2.5 bg-red-50 text-red-600 border border-red-100 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-sm">
                Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
        </div>

        <button onclick="document.getElementById('squadModal').classList.remove('hidden')" class="px-8 py-4 rounded-2xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl btn-3d flex items-center gap-3">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Regu Baru
        </button>
    </div>

    <!-- Squad Table -->
    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden card-3d">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-5 w-12 text-center"></th>
                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Identitas Regu</th>
                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Kekuatan Personel</th>
                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Aksi Manajemen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($squads as $squad)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4 text-center">
                        <input type="checkbox" name="squad_ids[]" value="{{ $squad->id }}" class="squad-checkbox w-5 h-5 rounded-lg border-slate-200 text-blue-600 focus:ring-0 cursor-pointer transition-all">
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <span class="text-base font-black text-slate-900 uppercase">Regu {{ $squad->name }}</span>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $squad->description ?? 'Garda Pengamanan Internal' }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-3">
                            <div class="flex -space-x-2.5 mr-2">
                                @foreach($squad->employees->take(4) as $emp)
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[10px] font-black uppercase overflow-hidden shadow-xs ring-1 ring-slate-200">
                                        @if($emp->photo)
                                            <img src="{{ $emp->photo }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($emp->full_name, 0, 1) }}
                                        @endif
                                    </div>
                                @endforeach
                                @if($squad->employees_count > 4)
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-900 text-white flex items-center justify-center text-[9px] font-black shadow-lg">
                                        +{{ $squad->employees_count - 4 }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-left">
                                <span class="block text-sm font-black text-slate-900 leading-none">{{ $squad->employees_count }}</span>
                                <span class="text-[8px] text-slate-400 font-black uppercase tracking-widest">Petugas Aktif</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center items-center gap-2">
                            <button onclick='openManageMembersModal(@json($squad->load("employees")), @json($unassignedEmployees))' class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-blue-100 btn-3d">
                                Kelola Anggota
                            </button>
                            <button onclick="openEditModal({{ json_encode($squad) }})" class="w-10 h-10 rounded-xl border border-slate-200 text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all flex items-center justify-center shadow-xs">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.squads.destroy', $squad->id) }}" method="POST" class="inline no-loader">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete(this.form)" class="w-10 h-10 rounded-xl border border-slate-200 text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all flex items-center justify-center shadow-xs">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-24 text-center">
                        <i data-lucide="shield-off" class="w-12 h-12 text-slate-200 mx-auto mb-4"></i>
                        <h4 class="text-sm font-black text-slate-900 uppercase italic">Database Regu Kosong</h4>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<form id="bulkDeleteSquadForm" action="{{ route('admin.squads.bulk-destroy') }}" method="POST" class="hidden no-loader">
    @csrf @method('DELETE')
</form>

<!-- Add Modal -->
<div id="squadModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm rounded-[40px] p-10 shadow-2xl animate-in zoom-in duration-200 relative overflow-hidden">
        <h3 id="squadModalTitle" class="text-2xl font-black text-slate-900 mb-8 italic tracking-tight">Tambah Regu</h3>
        <form id="squadForm" action="{{ route('admin.squads.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="_method" id="squadMethod" value="POST">
            <input type="hidden" name="schedule_type_id" value="{{ $scheduleTypes->first()?->id ?? 1 }}">
            
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nama Regu</label>
                <input type="text" name="name" id="squad_name" required placeholder="A, B, C, D atau E" class="w-full px-6 py-4 rounded-2xl border border-slate-200 text-sm font-black focus:border-blue-500 bg-slate-50 outline-none transition-all uppercase">
            </div>
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Deskripsi</label>
                <textarea name="description" id="squad_description" rows="3" placeholder="Deskripsi opsional..." class="w-full px-6 py-4 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 bg-slate-50 outline-none transition-all"></textarea>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeSquadModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest">Batal</button>
                <button type="submit" class="flex-[2] py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl btn-3d">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm rounded-[40px] p-10 shadow-2xl animate-in zoom-in duration-200 relative overflow-hidden">
        <h3 class="text-2xl font-black text-slate-900 mb-8 italic tracking-tight relative z-10">Informasi Regu</h3>
        <form id="editForm" method="POST" class="space-y-6 relative z-10">
            @csrf @method('PUT')
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nama Regu</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-6 py-4 rounded-2xl border border-slate-200 text-sm font-black focus:border-blue-500 bg-slate-50 outline-none transition-all uppercase">
            </div>
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Deskripsi</label>
                <textarea name="description" id="edit_description" rows="3" class="w-full px-6 py-4 rounded-2xl border border-slate-200 text-sm font-bold focus:border-blue-500 bg-slate-50 outline-none transition-all"></textarea>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" class="flex-[2] py-4 bg-blue-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl btn-3d">Update Regu</button>
            </div>
        </form>
    </div>
</div>

<!-- Manage Members Modal -->
<div id="membersModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-4 sm:p-10 backdrop-blur-sm">
    <div class="bg-white w-full max-w-6xl rounded-[48px] shadow-2xl animate-in zoom-in duration-300 flex flex-col max-h-[90vh] overflow-hidden">
        <!-- Header -->
        <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
            <div class="flex items-center gap-6">
                <div id="squad_icon_header" class="w-16 h-16 rounded-[24px] bg-slate-900 text-white flex items-center justify-center text-3xl font-black italic shadow-2xl">A</div>
                <div>
                    <h3 id="members_squad_name" class="text-3xl font-black text-slate-900 italic tracking-tighter">Kelola Anggota</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1 italic">Atur penugasan petugas dalam unit regu jaga</p>
                </div>
            </div>
            <button onclick="document.getElementById('membersModal').classList.add('hidden')" class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 hover:text-red-500 transition-all">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
            <!-- Left: Current Members List -->
            <div class="flex-1 p-10 border-r border-slate-100 overflow-y-auto custom-scrollbar bg-white">
                <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-blue-600"></i> Anggota Aktif Regu
                </h4>
                <div id="members_list" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
            </div>

            <!-- Right: Add New Members -->
            <div class="lg:w-[420px] bg-slate-50 flex flex-col overflow-hidden">
                <div class="p-10 pb-6 shrink-0">
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">Tambah Anggota Baru</h4>
                    <div class="relative group">
                        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                        <input type="text" id="memberSearch" placeholder="Cari nama atau NIP..." class="w-full pl-12 pr-6 py-4 rounded-2xl border border-slate-200 bg-white text-sm font-bold focus:border-blue-500 outline-none transition-all shadow-sm">
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar px-10">
                    <form id="addMemberForm" method="POST" class="no-loader">
                        @csrf
                        <div id="unassigned_list" class="space-y-3 mb-8"></div>
                    </form>
                </div>

                <div class="p-10 pt-6 border-t border-slate-200 bg-white shrink-0">
                    <button type="button" onclick="document.getElementById('addMemberForm').submit()" class="w-full py-5 bg-blue-600 text-white rounded-[24px] font-black text-xs uppercase tracking-widest hover:bg-slate-900 transition-all shadow-2xl shadow-blue-100 btn-3d flex items-center justify-center gap-3">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Simpan Penugasan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateSelection() {
        const checked = document.querySelectorAll('.squad-checkbox:checked').length;
        document.getElementById('selectedCount').innerText = checked;
        document.getElementById('btnDeleteSquads').classList.toggle('hidden', checked === 0);
    }

    document.getElementById('selectAllSquads')?.addEventListener('change', function() {
        document.querySelectorAll('.squad-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelection();
    });

    document.querySelectorAll('.squad-checkbox').forEach(cb => cb.addEventListener('change', updateSelection));

    function confirmDelete(form) {
        Swal.fire({ title: 'Hapus Regu?', text: "Petugas di dalamnya akan otomatis kehilangan status regu.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Ya, Hapus!', customClass: { popup: 'rounded-[32px]' } }).then((result) => { if (result.isConfirmed) form.submit(); });
    }

    function openEditModal(squad) {
        document.getElementById('squadModalTitle').innerText = 'Edit Informasi Regu';
        document.getElementById('squad_name').value = squad.name;
        document.getElementById('squad_description').value = squad.description || '';
        document.getElementById('squadForm').action = '/admin/squads/' + squad.id;
        document.getElementById('squadMethod').value = 'PUT';
        document.getElementById('squadModal').classList.remove('hidden');
    }

    function closeSquadModal() {
        document.getElementById('squadModal').classList.add('hidden');
        document.getElementById('squadForm').action = "{{ route('admin.squads.store') }}";
        document.getElementById('squadMethod').value = 'POST';
        document.getElementById('squadForm').reset();
    }

    document.getElementById('memberSearch')?.addEventListener('keyup', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.available-item').forEach(item => {
            const text = item.innerText.toLowerCase();
            item.classList.toggle('hidden', !text.includes(term));
        });
    });

    function openManageMembersModal(squad, unassigned) {
        document.getElementById('members_squad_name').innerText = `Regu ${squad.name}`;
        document.getElementById('squad_icon_header').innerText = squad.name;
        document.getElementById('addMemberForm').action = `/admin/squads/${squad.id}/add-member`;
        
        // Members List (Left)
        const list = document.getElementById('members_list');
        list.innerHTML = squad.employees.length ? '' : '<div class="col-span-2 py-20 text-center text-slate-300 font-bold uppercase text-[10px] tracking-widest italic">Belum ada anggota</div>';
        squad.employees.forEach(emp => {
            const photo = emp.photo ? `<img src="${emp.photo}" class="w-full h-full object-cover">` : `<span class="text-xs font-black text-slate-400 uppercase">${emp.full_name.substring(0, 1)}</span>`;
            list.innerHTML += `
                <div class="flex items-center justify-between p-5 bg-slate-50 rounded-3xl border border-slate-100 group transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center overflow-hidden shadow-sm ring-2 ring-white">
                            ${photo}
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900">${emp.full_name}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">NIP. ${emp.nip}</p>
                        </div>
                    </div>
                    <form action="/admin/squads/${squad.id}/remove-member" method="POST" class="no-loader">
                        @csrf
                        <input type="hidden" name="employee_id" value="${emp.id}">
                        <button type="submit" class="w-10 h-10 rounded-xl bg-white text-red-400 hover:bg-red-600 hover:text-white transition-all shadow-sm flex items-center justify-center border border-slate-100">
                            <i data-lucide="user-minus" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            `;
        });

        // Available List (Right)
        const uList = document.getElementById('unassigned_list');
        uList.innerHTML = unassigned.length ? '' : '<div class="py-20 text-center text-slate-400 text-[10px] font-bold uppercase italic">Semua petugas sudah memiliki regu</div>';
        unassigned.forEach(emp => {
            const photo = emp.photo ? `<img src="${emp.photo}" class="w-full h-full object-cover">` : `<span class="text-xs font-black text-slate-400 uppercase">${emp.full_name.substring(0, 1)}</span>`;
            uList.innerHTML += `
                <label class="available-item flex items-center gap-4 p-5 bg-white rounded-3xl border border-slate-200 hover:border-blue-400 transition-all cursor-pointer group shadow-sm">
                    <input type="checkbox" name="employee_ids[]" value="${emp.id}" class="w-6 h-6 rounded-xl border-slate-300 text-blue-600 focus:ring-0">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center overflow-hidden shadow-inner">
                        ${photo}
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-900 group-hover:text-blue-600 transition-colors">${emp.full_name}</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">${emp.nip}</p>
                    </div>
                </label>
            `;
        });

        document.getElementById('membersModal').classList.remove('hidden');
        lucide.createIcons();
    }
</script>

@if(session('success'))
<script>
    window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", confirmButtonColor: '#0F172A', customClass: { popup: 'rounded-[32px]' } });
    });
</script>
@endif
@endsection
