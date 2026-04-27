<label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors employee-item" 
    data-name="{{ strtolower($emp->full_name) }}" 
    data-group="{{ $group }}">
    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" 
        class="w-4 h-4 rounded border-white/20 bg-transparent text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900 employee-checkbox"
        data-group="{{ $group }}">
    <div class="min-w-0">
        <p class="text-[11px] font-bold text-white truncate uppercase">{{ $emp->full_name }}</p>
        <p class="text-[8px] font-medium text-slate-500 truncate">NIP. {{ $emp->nip }}</p>
    </div>
</label>
