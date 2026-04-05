@extends('layouts.app')

@section('title', 'Manajemen Laporan')
@section('header-title', 'Helpdesk Support')

@section('content')
@php
    $currentIssues = $issues->getCollection();
    $openCount = $issueStats['open'];
    $resolvedCount = $issueStats['resolved'];
    $closedCount = $issueStats['closed'];
    $searchActive = request()->anyFilled(['search', 'status', 'date', 'work_unit_id']);
@endphp

<div class="space-y-8 page-fade">
    <!-- Stats Header -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-2 bg-slate-900 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl card-3d">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <i data-lucide="message-square" class="w-32 h-32"></i>
            </div>
            <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 mb-2">Total Laporan</h4>
            <h3 class="text-4xl font-bold tracking-tight">{{ $issueStats['total'] }}</h3>
            <p class="mt-4 text-xs font-medium text-slate-400">Antrean laporan masuk dari seluruh pegawai.</p>
        </div>
        
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover-lift border-l-4 border-l-red-500 flex flex-col justify-between">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status Terbuka</p>
            <h3 class="text-3xl font-bold text-red-600">{{ $openCount }}</h3>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover-lift border-l-4 border-l-green-500 flex flex-col justify-between">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Telah Selesai</p>
            <h3 class="text-3xl font-bold text-green-600">{{ $resolvedCount }}</h3>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col lg:flex-row justify-between items-center gap-4">
            <form action="{{ route('admin.report-issues.index') }}" method="GET" class="w-full lg:flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari laporan..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-blue-500">
                </div>
                <select name="status" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-blue-500 appearance-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                <input type="date" name="date" value="{{ request('date') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold outline-none focus:border-blue-500 bg-white">
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-slate-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all btn-3d">Filter</button>
                    @if($searchActive)
                        <a href="{{ route('admin.report-issues.index') }}" class="px-3 flex items-center justify-center bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all">
                            <i data-lucide="refresh-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
            
            <div class="h-8 w-px bg-slate-200 hidden lg:block mx-2"></div>
            
            <button type="button" onclick="confirmDeleteAll()" class="w-full lg:w-auto px-6 py-2.5 bg-red-50 text-red-600 border border-red-100 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all btn-3d flex items-center justify-center gap-2">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Hapus Semua
            </button>
        </div>

        <!-- Issue List -->
        <div class="divide-y divide-slate-100">
            @forelse($issues as $issue)
            <div class="p-6 hover:bg-slate-50/50 transition-colors group">
                <div class="flex flex-col lg:flex-row gap-6 justify-between">
                    <div class="flex gap-4 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-white group-hover:text-blue-600 transition-all border border-transparent group-hover:border-slate-200 shrink-0">
                            {{ substr($issue->user->name ?? 'S', 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="text-sm font-bold text-slate-900 truncate">{{ $issue->user->name ?? 'User Unknown' }}</h4>
                                <span class="px-2 py-0.5 rounded-lg text-[8px] font-bold uppercase tracking-wider border {{ $issue->status === 'open' ? 'bg-red-50 text-red-600 border-red-100' : ($issue->status === 'resolved' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                                    {{ $issue->status }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-slate-800 mb-2 leading-tight">{{ $issue->subject }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed mb-4">{{ $issue->message }}</p>
                            
                            <div class="flex flex-wrap gap-4">
                                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    {{ $issue->created_at->format('d M Y, H:i') }}
                                </div>
                                @if($issue->admin_note)
                                <div class="flex items-center gap-1.5 text-[10px] font-bold text-blue-600 uppercase tracking-widest">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    Telah Ditanggapi
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex lg:flex-col gap-2 shrink-0">
                        <button onclick="openDetailModal({{ json_encode($issue->load('user.employee')) }})" class="flex-1 lg:flex-none w-10 h-10 lg:w-11 lg:h-11 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:shadow-md transition-all btn-3d">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                        <form id="deleteIssue-{{ $issue->id }}" action="{{ route('admin.report-issues.destroy', $issue->id) }}" method="POST" class="flex-1 lg:flex-none no-loader">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteIssue({{ $issue->id }})" class="w-full h-10 lg:w-11 lg:h-11 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-600 hover:border-red-200 hover:shadow-md transition-all btn-3d">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-24 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-dashed border-slate-200">
                    <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                </div>
                <p class="text-sm font-bold text-slate-400 uppercase tracking-[0.2em] italic">Kotak masuk laporan kosong</p>
            </div>
            @endforelse
        </div>

        @if($issues->hasPages())
        <div class="p-6 border-t border-slate-100 bg-slate-50/30">
            {{ $issues->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-4xl rounded-[32px] overflow-hidden shadow-2xl animate-in zoom-in duration-200 flex flex-col max-h-[90vh]">
        <div class="bg-slate-900 px-8 py-6 text-white flex justify-between items-center shrink-0">
            <div>
                <h3 class="text-xl font-bold tracking-tight">Detail Penanganan Laporan</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Sinergi PAS Support System</p>
            </div>
            <button type="button" onclick="document.getElementById('detailModal').classList.add('hidden')" class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-all">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="p-8 overflow-y-auto custom-scrollbar flex-1">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Sender Info -->
                <div class="space-y-6">
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                        <div id="detail_photo_container" class="w-20 h-20 mx-auto mb-4 rounded-2xl border-2 border-white bg-white shadow-md overflow-hidden flex items-center justify-center text-slate-300">
                            <i data-lucide="user" class="w-10 h-10"></i>
                        </div>
                        <h4 id="detail_name" class="text-base font-bold text-slate-900 leading-tight"></h4>
                        <p id="detail_nip" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"></p>
                    </div>

                    <div class="space-y-4 p-2">
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email Kedinasan</p>
                            <p id="detail_email" class="text-sm font-semibold text-slate-700"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Jabatan / Unit</p>
                            <p id="detail_position" class="text-sm font-semibold text-slate-700"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tanggal Kirim</p>
                            <p id="detail_date" class="text-sm font-semibold text-slate-700"></p>
                        </div>
                    </div>
                </div>

                <!-- Right: Content & Action -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="p-6 rounded-2xl bg-blue-50 border border-blue-100">
                        <p class="text-[9px] font-bold text-blue-400 uppercase tracking-widest mb-3">Subjek & Pesan:</p>
                        <h4 id="detail_subject" class="text-lg font-bold text-blue-900 mb-4"></h4>
                        <div id="detail_message" class="text-sm text-blue-800 leading-relaxed bg-white/50 p-4 rounded-xl"></div>
                    </div>

                    <form id="updateForm" method="POST" class="space-y-6">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Status Penanganan</label>
                                <select name="status" id="detail_status" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold outline-none focus:border-blue-500">
                                    <option value="open">Open (Dalam Antrean)</option>
                                    <option value="resolved">Resolved (Sudah Selesai)</option>
                                    <option value="closed">Closed (Ditutup)</option>
                                </select>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Tanggapan / Catatan Admin</label>
                            <textarea name="admin_note" id="detail_note" rows="4" class="w-full px-5 py-4 rounded-xl border border-slate-200 bg-slate-50 text-sm font-semibold outline-none focus:border-blue-500" placeholder="Ketik tanggapan untuk pegawai..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg btn-3d">
                            Simpan Perubahan Penanganan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteAllIssuesForm" action="{{ route('admin.report-issues.destroy-all') }}" method="POST" class="hidden no-loader">@csrf @method('DELETE')</form>

<script>
    function confirmDeleteIssue(id) {
        Swal.fire({
            title: 'Hapus Laporan?',
            text: "Laporan yang dihapus tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#0F172A',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`deleteIssue-${id}`).submit();
            }
        });
    }

    function confirmDeleteAll() {
        Swal.fire({
            title: 'Bersihkan Seluruh Laporan?',
            text: "Semua data laporan akan dihapus permanen dari database.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#0F172A',
            confirmButtonText: 'Ya, Bersihkan!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteAllIssuesForm').submit();
            }
        });
    }

    function openDetailModal(data) {
        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        document.getElementById('updateForm').action = `/admin/report-issues/${data.id}`;
        document.getElementById('detail_name').innerText = data.user.name;
        document.getElementById('detail_email').innerText = data.user.email;
        document.getElementById('detail_nip').innerText = data.user.employee ? `NIP. ${data.user.employee.nip}` : '-';
        document.getElementById('detail_position').innerText = data.user.employee ? data.user.employee.position : 'Administrator';
        document.getElementById('detail_date').innerText = new Date(data.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        document.getElementById('detail_subject').innerText = data.subject;
        document.getElementById('detail_message').innerText = data.message;
        document.getElementById('detail_status').value = data.status;
        document.getElementById('detail_note').value = data.admin_note || '';

        const photoContainer = document.getElementById('detail_photo_container');
        if (data.user.employee && data.user.employee.photo) {
            photoContainer.innerHTML = `<img src="${data.user.employee.photo}" class="w-full h-full object-cover">`;
        } else {
            photoContainer.innerHTML = '<i data-lucide="user" class="w-10 h-10"></i>';
        }

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
