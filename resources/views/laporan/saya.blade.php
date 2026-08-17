<x-bauhaus.layout title="Laporan Saya">
    <div class="w-full max-w-5xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="square" color="yellow" class="h-12 w-12" />
            <div class="min-w-0">
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Laporan Saya</h1>
                <p class="mt-1 break-words text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">{{ auth()->user()->name }}</p>
            </div>
        </div>

        {{-- Filters Bar --}}
        <div class="mb-6 border border-bauhaus-black bg-bauhaus-paper p-4">
            <form id="searchForm">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-3">
                    {{-- Search Input --}}
                    <input 
                        type="text" 
                        id="searchInput"
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari laporan..."
                        class="rounded border border-bauhaus-black px-3 py-2 text-sm focus:border-bauhaus-blue focus:outline-none focus:ring-1 focus:ring-bauhaus-blue"
                    >
                    
                    {{-- Building Select --}}
                    <select id="buildingSelect" name="building_id" class="rounded border border-bauhaus-black px-3 py-2 text-sm focus:border-bauhaus-blue focus:outline-none focus:ring-1 focus:ring-bauhaus-blue">
                        <option value="">Semua Gedung</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                {{ $building->nama_gedung }}
                            </option>
                        @endforeach
                    </select>
                    
                    {{-- Department Select --}}
                    <select id="departmentSelect" name="department_id" class="rounded border border-bauhaus-black px-3 py-2 text-sm focus:border-bauhaus-blue focus:outline-none focus:ring-1 focus:ring-bauhaus-blue">
                        <option value="">Semua Jurusan</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nama_jurusan }} - {{ $dept->building?->nama_gedung ?? 'Tidak diketahui' }}
                            </option>
                        @endforeach
                    </select>
                    
                    {{-- Room Select --}}
                    <select id="roomSelect" name="room_id" class="rounded border border-bauhaus-black px-3 py-2 text-sm focus:border-bauhaus-blue focus:outline-none focus:ring-1 focus:ring-bauhaus-blue">
                        <option value="">Semua Ruangan</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->nama_ruangan }} - {{ $room->department?->nama_jurusan ?? 'Tidak diketahui' }}
                            </option>
                        @endforeach
                    </select>
                    
                    {{-- Per Page Select --}}
                    <select id="perPageSelect" class="rounded border border-bauhaus-black px-3 py-2 text-sm focus:border-bauhaus-blue focus:outline-none focus:ring-1 focus:ring-bauhaus-blue">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per halaman</option>
                        <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20 per halaman</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 per halaman</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="bauhaus-btn bg-bauhaus-black px-4 py-2 text-xs text-white">
                        Cari
                    </button>
                    @if(request()->hasAny(['search', 'building_id', 'department_id', 'room_id']))
                        <a href="{{ route('laporan.saya') }}" class="bauhaus-btn bg-bauhaus-paper px-4 py-2 text-xs">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tab Navigation --}}
        <div class="border border-bauhaus-black bg-bauhaus-paper mb-6 overflow-hidden">
            <div class="flex border-b border-bauhaus-black">
                <button data-tab="damage" class="tab-btn flex-1 px-6 py-3 text-sm font-bold uppercase tracking-wider transition-all active-tab" onclick="switchTab('damage')">
                    Laporan Kerusakan
                </button>
                <button data-tab="maintenance" class="tab-btn flex-1 px-6 py-3 text-sm font-bold uppercase tracking-wider" onclick="switchTab('maintenance')">
                    Hasil Perawatan
                </button>
                <button data-tab="repair" class="tab-btn flex-1 px-6 py-3 text-sm font-bold uppercase tracking-wider" onclick="switchTab('repair')">
                    Hasil Perbaikan
                </button>
            </div>
        </div>

        {{-- Reports Container --}}
        <div id="reportsContainer" class="mb-6">
            {{-- Content will be loaded via AJAX --}}
            <div id="loadingState" class="flex justify-center p-8">
                <span>Loading...</span>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Kembali</a>
        </div>
    </div>

    {{-- Styles & Scripts --}}
    <style>
        .tab-btn {
            background: transparent;
            border: none;
            outline: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
            cursor: pointer;
        }
        
        .active-tab {
            background: #000;
            color: #fff;
            position: relative;
        }
        
        .inactive-tab:hover {
            background: rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        let currentTab = '{{ request('tab', 'damage') }}';
        let currentPage = 1;
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeFilters();
            loadReports(currentTab, 1);
            
            // Form submit handler
            document.getElementById('searchForm').addEventListener('submit', function(e) {
                e.preventDefault();
                currentTab = document.querySelector('.active-tab').dataset.tab;
                currentPage = 1;
                loadReports(currentTab, 1);
            });
            
            // Tab switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    switchTab(this.dataset.tab, true);
                });
            });
            
            // Per page change
            document.getElementById('perPageSelect').addEventListener('change', function() {
                currentTab = document.querySelector('.active-tab').dataset.tab;
                currentPage = 1;
                loadReports(currentTab, 1);
            });
        });
        
        function switchTab(tab, updateHistory = false) {
            if (tab === currentTab) return;
            
            currentTab = tab;
            currentPage = 1;
            
            // Update tabs UI
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (btn.dataset.tab === tab) {
                    btn.classList.add('active-tab');
                    btn.classList.remove('inactive-tab');
                } else {
                    btn.classList.add('inactive-tab');
                    btn.classList.remove('active-tab');
                }
            });
            
            // Load reports
            loadReports(tab, 1);
            
            // Update URL without reload
            if (updateHistory) {
                const url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.pushState({}, '', url);
            }
        }
        
        async function loadReports(type, page) {
            const formData = {
                _token: '{{ csrf_token() }}',
                type: type,
                page: page,
                per_page: document.getElementById('perPageSelect').value,
                search: document.getElementById('searchInput').value,
                building_id: document.getElementById('buildingSelect').value || null,
                department_id: document.getElementById('departmentSelect').value || null,
                room_id: document.getElementById('roomSelect').value || null,
            };
            
            try {
                const response = await fetch('{{ route("laporan.saya.reports") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                renderReports(data);
                
                // Update URL without reload
                const url = new URL(window.location.href);
                url.searchParams.set('type', type);
                if (formData.building_id) url.searchParams.set('building_id', formData.building_id);
                if (formData.department_id) url.searchParams.set('department_id', formData.department_id);
                if (formData.room_id) url.searchParams.set('room_id', formData.room_id);
                if (formData.search) url.searchParams.set('search', formData.search);
                url.searchParams.set('per_page', formData.per_page);
                window.history.pushState({}, '', url);
                
            } catch (error) {
                console.error('Error loading reports:', error);
                document.getElementById('reportsContainer').innerHTML = `
                    <div class="text-center p-8 text-red-600">
                        Error loading reports. Please try again.
                    </div>
                `;
            }
        }
        
        function renderReports(data) {
            const { data: reports, current_page, last_page, total, from, to, type } = data;
            
            if (!reports || reports.length === 0) {
                document.getElementById('reportsContainer').innerHTML = `
                    <div class="flex items-center gap-4 border border-bauhaus-black bg-bauhaus-paper p-8">
                        <x-bauhaus.shape type="circle-hole" color="red" class="h-12 w-12" />
                        <p class="font-display text-sm uppercase tracking-widest">Tidak ada laporan yang ditemukan.</p>
                    </div>
                `;
                return;
            }
            
            const reportTypeLabels = {
                'damage': { label: 'Laporan Kerusakan', icon: 'triangle', bgColor: 'blue', textColor: '#fff' },
                'maintenance': { label: 'Hasil Perawatan', icon: 'circle-hole', bgColor: '#FFD700', textColor: '#000' },
                'repair': { label: 'Laporan Hasil Perbaikan', icon: 'square', bgColor: 'blue', textColor: '#fff' }
            };
            
            const config = reportTypeLabels[type];
            const badgeConfig = getStatusConfig(data.statuses?.[0]?.status);
            
            let html = `
                <section class="border border-bauhaus-black bg-bauhaus-paper">
                    <div class="flex items-center justify-between gap-4 border-b border-bauhaus-black" style="background-color: ${config.bgColor}; color: ${config.textColor}; padding: 1rem;">
                        <div class="flex items-center gap-4" style="display: flex; align-items: center; gap: 1rem;">
                            <x-bauhaus.shape type="${config.icon}" color="yellow" class="h-8 w-8" />
                            <h2 class="bauhaus-title text-lg" style="margin: 0;">${config.label}</h2>
                        </div>
                        <span class="text-xs" style="color: ${config.textColor === '#fff' ? '#FFD700' : '#000'};">
                            ${from} - ${to} dari ${total} laporan
                        </span>
                    </div>
                    <ul class="divide-y divide-bauhaus-black" style="border-collapse: collapse;">
            `;
            
            reports.forEach(report => {
                const status = report.status;
                const isDisetujui = status === 'disetujui';
                const isDitolak = status === 'ditolak';
                const isPending = status === 'pending' || !status;
                const badgeClass = isDisetujui ? 'bg-bauhaus-yellow' : (isDitolak ? 'bg-bauhaus-paper text-red-600' : 'bg-bauhaus-paper');
                const badgeLabel = isDisetujui ? 'Disetujui' : (isDitolak ? 'Ditolak' : (status === 'revisi' ? 'Revisi' : 'Pending'));
                
                html += `
                    <li style="padding: 1.25rem;">
                        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
                            <div style="min-width: 0;">
                                <p style="margin: 0 0 0.25rem; font-size: 0.875rem; font-weight: 600;">${report.nama_alat}</p>
                                <p style="margin: 0; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; color: #2196F3;">${report.nomor_laporan} · ${report.jenis_kerusakan || report.jenis_pekerjaan}</p>
                                <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #333;">
                                    ${report.tanggal_laporan || report.tanggal_pelaksanaan ? new Date(report.tanggal_laporan || report.tanggal_pelaksanaan).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'}
                                    ${report.gedung !== '-' ? (' · ' + report.gedung) : ''}${report.ruangan && report.ruangan !== '-' ? (' · ' + report.ruangan) : ''}${report.jurusan && report.jurusan !== '-' ? (' · ' + report.jurusan) : ''}
                                </p>
                            </div>
                            <span style="border: 1px solid #000; padding: 0.25rem 0.75rem; font-family: monospace; font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase; ${badgeClass === 'bg-bauhaus-yellow' ? 'background-color: #FFD700;' : (badgeClass.includes('text-red-600') ? 'color: #dc2626;' : '')}">${badgeLabel}</span>
                        </div>
                        <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.75rem;">
                            <a href="/laporan/status?nomor=${report.nomor_laporan}" style="background-color: #fff; padding: 0.5rem 1rem; font-size: 0.75rem; border: 1px solid #000; text-decoration: none; color: #000;" class="bauhaus-btn">Lihat Detail</a>
                            <a href="/laporan/pdf/${report.type || getTypeFromName(report.nomor_laporan)}/${report.id}" style="background-color: #000; padding: 0.5rem 1rem; font-size: 0.75rem; color: #fff; border: 1px solid #000; text-decoration: none;" class="bauhaus-btn">Preview PDF</a>
                        </div>
                    </li>
                `;
            });
            
            html += `
                    </ul>
                    ${last_page > 1 ? `
                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #000; padding: 1rem; background-color: #FFFEF0;">
                            <div class="text-xs text-bauhaus-ink" style="margin: 0; font-size: 0.875rem;">
                                Halaman ${current_page} dari ${last_page}
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                ${current_page === 1 ? '<span style="padding: 0.25rem 0.75rem; font-size: 0.75rem; color: #ccc;">&laquo; Prev</span>' : `
                                    <button onclick="loadReports('${data.type}', ${current_page - 1})" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; border: 1px solid #000; background: #fff; cursor: pointer;" class="bauhaus-btn">Prev</button>
                                `}
                                ${current_page === last_page ? '<span style="padding: 0.25rem 0.75rem; font-size: 0.75rem; color: #ccc;">Next &raquo;</span>' : `
                                    <button onclick="loadReports('${data.type}', ${current_page + 1})" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; border: 1px solid #000; background: #fff; cursor: pointer;" class="bauhaus-btn">Next</button>
                                `}
                            </div>
                        </div>
                    ` : ''}
                </section>
            `;
            
            document.getElementById('reportsContainer').innerHTML = html;
        }
        
        function getStatusConfig(status) {
            const statusMap = {
                'disetujui': { label: 'Disetujui', className: 'bg-bauhaus-yellow' },
                'ditolak': { label: 'Ditolak', className: 'bg-bauhaus-paper text-red-600' },
                'pending': { label: 'Pending', className: 'bg-bauhaus-paper' },
                'revisi': { label: 'Revisi', className: 'bg-bauhaus-paper' }
            };
            return statusMap[status] || { label: status, className: 'bg-bauhaus-paper' };
        }
        
        function getTypeFromName(nomorLaporan) {
            if (nomorLaporan.includes('KRS')) return 'kerusakan';
            if (nomorLaporan.includes('PRW')) return 'perawatan';
            if (nomorLaporan.includes('PBP')) return 'perbaikan';
            return 'kerusakan';
        }
        
        function initializeFilters() {
            const buildingSelect = document.getElementById('buildingSelect');
            const departmentSelect = document.getElementById('departmentSelect');
            const roomSelect = document.getElementById('roomSelect');
            
            // Note: Building filter is currently a simple dropdown without cascade filtering
            // due to database schema constraints. All options are always visible.
        }
    </script>
</x-bauhaus.layout>
