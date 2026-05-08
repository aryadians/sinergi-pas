<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pengaduan WBS | SINERGI PAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0F172A; }
    </style>
</head>
<body class="antialiased selection:bg-red-500 selection:text-white flex items-center justify-center min-h-screen p-6 relative overflow-hidden">
    
    <div class="absolute -top-24 -left-24 w-64 h-64 bg-red-600/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-amber-500/20 rounded-full blur-3xl"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <a href="{{ route('login') }}" class="inline-block bg-white/10 p-4 rounded-3xl backdrop-blur-sm border border-white/10 mb-6 hover:bg-white/20 transition-all">
                <i data-lucide="shield-alert" class="w-10 h-10 text-red-500"></i>
            </a>
            <h2 class="text-3xl font-black text-white tracking-tight italic">Lacak Pengaduan</h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">Whistleblowing System Internal</p>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/50 rounded-2xl text-center">
                <p class="text-xs font-bold text-red-400 uppercase tracking-widest">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-[32px] p-8 shadow-2xl">
            <form action="{{ route('wbs.track.show', '') }}" method="GET" id="trackForm" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nomor Tiket Pengaduan</label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-red-500 transition-colors">
                            <i data-lucide="ticket" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="ticket_number" id="ticket_number" required
                            class="w-full pl-12 pr-6 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:bg-white focus:border-red-500 outline-none transition-all font-bold text-sm text-slate-700 uppercase"
                            placeholder="LP-JBG-2026-XXXXXX">
                    </div>
                </div>

                <button type="button" onclick="submitForm()"
                    class="w-full py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-red-500/30 transition-all transform hover:-translate-y-1 active:scale-95">
                    Cari Laporan
                </button>
            </form>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-[10px] font-bold text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-3 h-3"></i> Kembali ke Login
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();
        function submitForm() {
            const ticket = document.getElementById('ticket_number').value.trim();
            if (ticket) {
                window.location.href = "{{ route('wbs.track.show', '') }}/" + ticket;
            }
        }
        
        document.getElementById('trackForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm();
        });
    </script>
</body>
</html>
