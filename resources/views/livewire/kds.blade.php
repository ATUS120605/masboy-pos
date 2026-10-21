<div
    wire:poll.5s="refreshPesanan"
    class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-full"
    x-data="{ now: Date.now() }"
    x-init="setInterval(() => now = Date.now(), 1000)"
    x-on:toast.window="toast($event.detail.icon, $event.detail.title)">

    <div class="flex flex-col h-full">
        <div class="flex items-center gap-2 mb-5">
            <span class="w-2.5 h-2.5 rounded-full bg-orange-400 animate-pulse"></span>
            <h2 class="text-base font-bold text-gray-800 uppercase tracking-wide">Dimasak</h2>
            <span class="ml-auto text-xs font-bold bg-orange-100 text-orange-700 px-2.5 py-1 rounded-lg">{{ count($pesananDimasak) }}</span>
        </div>
        <div class="flex-1 space-y-3 overflow-y-auto pr-0.5">
            @forelse($pesananDimasak as $p)
                @php
                    $created = \Carbon\Carbon::parse($p['created_at']);
                    $elapsedMinutes = (int) $created->diffInMinutes(now());
                    $isUrgent = $elapsedMinutes >= 10;
                    $progress = min($elapsedMinutes / 10 * 100, 100);
                @endphp
                <div class="relative overflow-hidden rounded-xl {{ $isUrgent ? 'bg-red-50 ring-1 ring-red-300' : 'bg-white ring-1 ring-gray-200' }}">
                    <div class="absolute top-0 left-0 h-1 {{ $isUrgent ? 'bg-red-400' : 'bg-emerald-400' }}" style="width: {{ $progress }}%; transition: width 1s;"></div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="text-2xl font-black tracking-tight {{ $isUrgent ? 'text-red-700' : 'text-emerald-800' }}">#{{ $p['nomor_pesanan'] }}</span>
                                <div class="flex flex-wrap gap-1">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $p['tipe_pesanan'] === 'bungkus' ? 'bg-yellow-100 text-yellow-800' : 'bg-emerald-100 text-emerald-900' }}">{{ $p['tipe_pesanan'] === 'bungkus' ? 'BUNGKUS' : 'DINE IN' }}</span>
                                    @if($p['nomor_meja'])
                                        <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md">MEJA {{ $p['nomor_meja'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0 text-right" x-data="{ created: {{ $created->timestamp * 1000 }} }">
                                <div class="text-lg font-mono font-bold tabular-nums {{ $isUrgent ? 'text-red-600' : 'text-emerald-700' }}"
                                     x-text="(() => {
                                        let s = Math.floor((now - created) / 1000);
                                        let m = Math.floor(s / 60);
                                        let h = Math.floor(m / 60);
                                        s = s % 60; m = m % 60;
                                        return h > 0
                                            ? `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`
                                            : `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
                                     })()">
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5 font-mono">{{ $created->format('H:i') }}</div>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach($p['detail_pesanans'] as $item)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-sm font-medium rounded-lg {{ $isUrgent ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $item['menu']['nama_menu'] ?? '?' }}
                                    <span class="font-bold {{ $isUrgent ? 'text-red-600' : 'text-gray-900' }}">x{{ $item['qty'] }}</span>
                                </span>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between mt-3">
                            <span class="text-[11px] text-gray-400">{{ $p['transaksi']['kasir']['name'] ?? 'Kasir' }}</span>
                            <button
                                x-on:click.prevent="Swal.fire({
                                    icon: 'question',
                                    title: 'Selesaikan #{{ $p['nomor_pesanan'] }}?',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya, Selesai',
                                    cancelButtonText: 'Batal',
                                    confirmButtonColor: '#064E3B',
                                    cancelButtonColor: '#9CA3AF',
                                    iconColor: '#064E3B',
                                    buttonsStyling: false,
                                    customClass: { confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' },
                                    didOpen: () => {
                                        document.querySelectorAll('.swal2-confirm, .swal2-cancel').forEach(b => {
                                            b.style.borderRadius = '12px';
                                            b.style.fontWeight = '600';
                                            b.style.fontSize = '0.875rem';
                                            b.style.padding = '10px 28px';
                                            b.style.border = 'none';
                                        });
                                    },
                                }).then(r => { if (r.isConfirmed) $wire.selesaikan({{ $p['id'] }}); })"
                                class="px-4 py-1.5 text-xs font-bold rounded-lg transition {{ $isUrgent ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-emerald-900 text-white hover:bg-emerald-800' }}">
                                {{ $isUrgent ? 'SELESAIKAN' : 'SELESAI' }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-gray-300 bg-white rounded-xl ring-1 ring-dashed ring-gray-200">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <p class="text-sm font-medium">Kosong</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="flex flex-col h-full">
        <div class="flex items-center gap-2 mb-5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
            <h2 class="text-base font-bold text-gray-800 uppercase tracking-wide">Selesai</h2>
            <span class="ml-auto text-xs font-bold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-lg">{{ count($pesananSelesai) }}</span>
        </div>
        <div class="flex-1 space-y-2 overflow-y-auto pr-0.5">
            @forelse($pesananSelesai as $p)
                <div class="rounded-xl bg-emerald-50 ring-1 ring-emerald-200">
                    <div class="p-3.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-black text-emerald-700">#{{ $p['nomor_pesanan'] }}</span>
                                <div class="flex flex-wrap gap-1">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $p['tipe_pesanan'] === 'bungkus' ? 'bg-yellow-100 text-yellow-800' : 'bg-emerald-100 text-emerald-900' }}">{{ $p['tipe_pesanan'] === 'bungkus' ? 'BUNGKUS' : 'DINE IN' }}</span>
                                    @if($p['nomor_meja'])
                                        <span class="text-[10px] font-bold bg-white text-gray-500 px-2 py-0.5 rounded-md">MEJA {{ $p['nomor_meja'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-[10px] font-mono text-gray-400 shrink-0">{{ \Carbon\Carbon::parse($p['updated_at'])->format('H:i') }}</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($p['detail_pesanans'] as $item)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-emerald-800 bg-emerald-100/70 rounded-md">
                                    {{ $item['menu']['nama_menu'] ?? '?' }}
                                    <span class="font-bold">x{{ $item['qty'] }}</span>
                                </span>
                            @endforeach
                        </div>
                        <div class="text-[10px] text-gray-400 mt-2 font-mono">{{ \Carbon\Carbon::parse($p['created_at'])->format('H:i') }} &middot; {{ $p['transaksi']['kasir']['name'] ?? 'Kasir' }}</div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-gray-300 bg-white rounded-xl ring-1 ring-dashed ring-gray-200">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium">Kosong</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
