@extends('layouts.app')

@section('title', 'Manajemen Golongan')
@section('header-title', 'Konfigurasi Golongan')

@section('content')
<div class="space-y-8 page-fade">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-xl border border-slate-200 shadow-sm">
                <input type="checkbox" id="selectAllRanks" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih Semua</span>
            </div>
            <button type="button" onclick="confirmBulkDelete()" id="btnDeleteRanks" class="hidden px-6 py-2.5 bg-red-50 text-red-600 border border-red-100 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-sm">
                Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
        </div>
        <button onclick="document.getElementById('addRankModal').classList.remove('hidden')" class="px-8 py-4 rounded-2xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl btn-3d flex items-center gap-3">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Golongan
        </button>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden card-3d">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-5 w-10"></th>
                    <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Golongan</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pangkat / Keterangan</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Uang Makan</th>
                    <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($ranks as $rank)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <input type="checkbox" name="rank_ids[]" value="{{ $rank->id }}" class="rank-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer">
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-black text-slate-900">{{ $rank->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-slate-500">{{ $rank->description ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-black border border-emerald-100">
                            Rp {{ number_format($rank->meal_allowance, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center items-center gap-2">
                            <button onclick="openEditRankModal({{ json_encode($rank) }})" class="w-9 h-9 rounded-xl border border-slate-200 text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all flex items-center justify-center">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.ranks.destroy', $rank->id) }}" method="POST" class="inline no-loader">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmSingleDelete(this.form)" class="w-9 h-9 rounded-xl border border-slate-200 text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all flex items-center justify-center">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest italic">Belum ada data golongan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<form id="bulkDeleteRankForm" action="{{ route('admin.ranks.bulk-destroy') }}" method="POST" class="hidden no-loader">
    @csrf @method('DELETE')
</form>

<!-- Add Modal -->
<div id="addRankModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm rounded-[32px] p-10 shadow-2xl animate-in zoom-in duration-200">
        <h3 class="text-xl font-bold text-slate-900 mb-6 italic">Tambah Golongan</h3>
        <form action="{{ route('admin.ranks.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Nama (Misal: II/A)</label>
                <input type="text" name="name" required class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-bold focus:border-blue-500 outline-none">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Keterangan (Pangkat)</label>
                <input type="text" name="description" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-semibold focus:border-blue-500 outline-none">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Rate Uang Makan (Per Hari)</label>
                <div class="relative">
                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                    <input type="number" name="meal_allowance" required class="w-full pl-12 pr-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-black text-emerald-600 focus:border-emerald-500 outline-none" value="0">
                </div>
            </div>
            <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl">Simpan Golongan</button>
            <button type="button" onclick="document.getElementById('addRankModal').classList.add('hidden')" class="w-full text-slate-400 font-bold text-[10px] uppercase tracking-widest mt-2">Batal</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editRankModal" class="fixed inset-0 bg-slate-900/60 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm rounded-[32px] p-10 shadow-2xl animate-in zoom-in duration-200">
        <h3 class="text-xl font-bold text-slate-900 mb-6 italic">Edit Golongan</h3>
        <form id="editRankForm" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Nama Golongan</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-bold focus:border-blue-500 outline-none">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Keterangan</label>
                <input type="text" name="description" id="edit_description" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-semibold focus:border-blue-500 outline-none">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Rate Uang Makan</label>
                <div class="relative">
                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                    <input type="number" name="meal_allowance" id="edit_meal_allowance" required class="w-full pl-12 pr-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-black text-emerald-600 focus:border-emerald-500 outline-none">
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl">Update Golongan</button>
            <button type="button" onclick="document.getElementById('editRankModal').classList.add('hidden')" class="w-full text-slate-400 font-bold text-[10px] uppercase tracking-widest mt-2">Batal</button>
        </form>
    </div>
</div>

<script>
    const selectAll = document.getElementById('selectAllRanks');
    const checkboxes = document.querySelectorAll('.rank-checkbox');
    const btnDelete = document.getElementById('btnDeleteRanks');
    const selectedCount = document.getElementById('selectedCount');

    selectAll.addEventListener('change', () => {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateUI();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateUI));

    function updateUI() {
        const checked = document.querySelectorAll('.rank-checkbox:checked');
        selectedCount.innerText = checked.length;
        btnDelete.classList.toggle('hidden', checked.length === 0);
    }

    function confirmSingleDelete(form) {
        Swal.fire({
            title: 'Hapus Golongan?',
            text: "Data ini tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Hapus!',
            customClass: { popup: 'rounded-[32px]' }
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }

    function confirmBulkDelete() {
        const checked = document.querySelectorAll('.rank-checkbox:checked');
        Swal.fire({
            title: 'Hapus Massal Golongan?',
            text: `${checked.length} golongan terpilih akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Hapus Semua!',
            customClass: { popup: 'rounded-[32px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('bulkDeleteRankForm');
                form.querySelectorAll('input[name="ids[]"]').forEach(i => i.remove());
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });
                form.submit();
            }
        });
    }

    function openEditRankModal(rank) {
        const form = document.getElementById('editRankForm');
        form.action = `/admin/ranks/${rank.id}`;
        document.getElementById('edit_name').value = rank.name;
        document.getElementById('edit_description').value = rank.description || '';
        document.getElementById('edit_meal_allowance').value = rank.meal_allowance || 0;
        document.getElementById('editRankModal').classList.remove('hidden');
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
