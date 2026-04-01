@extends('layouts.app')

@section('title', 'Laporan Masalah')
@section('header-title', 'Manajemen Laporan Masalah')

@section('content')
<div class="bg-white rounded-[40px] border border-[#EFEFEF] shadow-sm overflow-hidden">
    <div class="p-8 border-b border-[#EFEFEF] bg-[#FCFBF9]/50">
        <h3 class="text-lg font-bold text-[#1E2432]">Daftar Laporan dari Pegawai</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#FCFBF9]">
                    <th class="px-8 py-5 text-xs font-bold text-[#8A8A8A] uppercase tracking-widest">Pegawai</th>
                    <th class="px-8 py-5 text-xs font-bold text-[#8A8A8A] uppercase tracking-widest">Subjek</th>
                    <th class="px-8 py-5 text-xs font-bold text-[#8A8A8A] uppercase tracking-widest">Status</th>
                    <th class="px-8 py-5 text-xs font-bold text-[#8A8A8A] uppercase tracking-widest">Tanggal</th>
                    <th class="px-8 py-5 text-xs font-bold text-[#8A8A8A] uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#EFEFEF]">
                @foreach($issues as $issue)
                <tr class="hover:bg-[#FCFBF9] transition-all group">
                    <td class="px-8 py-6">
                        <p class="text-sm font-bold text-[#1E2432]">{{ $issue->user->name }}</p>
                        <p class="text-[10px] text-[#8A8A8A] font-medium">{{ $issue->user->email }}</p>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-sm font-medium text-[#1E2432]">{{ $issue->subject }}</p>
                    </td>
                    <td class="px-8 py-6 text-sm">
                        @if($issue->status === 'open')
                            <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-black uppercase rounded-full border border-red-100 italic">Open</span>
                        @elseif($issue->status === 'resolved')
                            <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black uppercase rounded-full border border-green-100 italic">Resolved</span>
                        @else
                            <span class="px-3 py-1 bg-gray-50 text-gray-600 text-[10px] font-black uppercase rounded-full border border-gray-100 italic">Closed</span>
                        @endif
                    </td>
                    <td class="px-8 py-6 text-sm text-[#8A8A8A] font-medium">
                        {{ $issue->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-8 py-6 text-sm text-center">
                        <div class="flex justify-center items-center gap-2">
                            <button onclick="openDetailModal({{ $issue->toJson() }})" class="w-9 h-9 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition-all">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.report-issues.destroy', $issue->id) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 flex items-center justify-center text-[#E85A4F] hover:bg-red-50 rounded-xl transition-all">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-8 bg-[#FCFBF9]/50 border-t border-[#EFEFEF]">
        {{ $issues->links() }}
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-6 backdrop-blur-sm">
    <div class="bg-white w-full max-w-2xl rounded-[40px] p-10 shadow-2xl animate-in zoom-in duration-300">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-[#1E2432]">Detail Laporan</h3>
            <button onclick="document.getElementById('detailModal').classList.add('hidden')" class="text-[#8A8A8A] hover:text-[#1E2432]">
                <i data-lucide="x" class="w-8 h-8"></i>
            </button>
        </div>
        <form id="updateForm" method="POST">
            @csrf @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-[#8A8A8A] uppercase tracking-widest block mb-2">Pesan Pegawai</label>
                    <div class="p-5 bg-[#FCFBF9] rounded-2xl border border-[#EFEFEF] text-sm text-[#1E2432] leading-relaxed" id="detail_message"></div>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-[#8A8A8A] uppercase tracking-widest block mb-2">Update Status</label>
                        <select name="status" id="detail_status" class="w-full px-5 py-4 rounded-2xl border border-[#EFEFEF] bg-[#FCFBF9] text-sm outline-none focus:ring-2 focus:ring-[#E85A4F]">
                            <option value="open">Open</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black text-[#8A8A8A] uppercase tracking-widest block mb-2">Catatan Admin</label>
                    <textarea name="admin_note" id="detail_note" rows="3" class="w-full px-5 py-4 rounded-2xl border border-[#EFEFEF] bg-[#FCFBF9] text-sm outline-none focus:ring-2 focus:ring-[#E85A4F]" placeholder="Berikan tanggapan atau catatan..."></textarea>
                </div>

                <button type="submit" class="w-full bg-[#E85A4F] text-white py-5 rounded-[24px] font-bold hover:bg-[#d44d42] transition-all shadow-xl shadow-red-200">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDetailModal(issue) {
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('updateForm').action = `/admin/report-issues/${issue.id}`;
        document.getElementById('detail_message').innerText = issue.message;
        document.getElementById('detail_status').value = issue.status;
        document.getElementById('detail_note').value = issue.admin_note || '';
    }
</script>
@endsection
