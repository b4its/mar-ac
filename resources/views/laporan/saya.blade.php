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
                    
                    {{-- Building Select (Searchable) --}}
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
                            <option value="{{ $dept->id }}" data-building="{{ $dept->building_id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                    
                    {{-- Room Select --}}
                    <select id="roomSelect" name="room_id" class="rounded border border-bauhaus-black px-3 py-2 text-sm focus:border-bauhaus-blue focus:outline-none focus:ring-1 focus:ring-bauhaus-blue">
                        <option value="">Semua Ruangan</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" data-department="{{ $room->department_id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->nama_ruangan }}
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

    {{-- Custom Searchable Select Styles & Scripts --}}
    <style>
        .search-select-wrapper {
            position: relative;
            display: block;
        }
        
        .search-select-input {
            width: 100%;
        }
        
        .search-select-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #000;
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 2px;
        }
        
        .search-select-dropdown.active {
            display: block;
        }
        
        .search-select-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        
        .search-select-item:hover {
            background: #f0f0f0;
        }
        
        .tab-btn {
            background: transparent;
            border: none;
            outline: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }
        
        .active-tab {
            background: #000;
            color: #fff;
            position: relative;
        }
        
        .active-tab::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: currentColor;
        }
        
        .inactive-tab:hover {
            background: rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        let currentTab = '{{ request('tab', 'damage') }}';
        let currentPage = 1;
        
        // Initialize searchable selects
        document.addEventListener('DOMContentLoaded', function() {
            initializeSearchableSelects();
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
                btn.classList.remove('active-tab');
                btn.classList.add('inactive-tab');
                if (btn.dataset.tab === tab) {
                    btn.classList.add('active-tab');
                    btn.classList.remove('inactive-tab');
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
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
            const { data: reports, current_page, last_page, total, from, to } = data;
            
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
                'damage': { label: 'Laporan Kerusakan', icon: 'triangle', bgColor: 'blue', textColor: 'white', badgeColor: 'bauhaus-yellow' },
                'maintenance': { label: 'Hasil Perawatan', icon: 'circle-hole', bgColor: 'yellow', textColor: '', badgeColor: 'bauhaus-yellow' },
                'repair': { label: 'Laporan Hasil Perbaikan', icon: 'square', bgColor: 'blue', textColor: 'white', badgeColor: 'bauhaus-yellow' }
            };
            
            const config = reportTypeLabels[data.type];
            
            let html = `
                <section class="border border-bauhaus-black bg-bauhaus-paper">
                    <div class="flex items-center justify-between gap-4 border-b border-bauhaus-black ${config.bgColor === 'blue' ? 'bg-bauhaus-blue p-4 text-white' : 'bg-bauhaus-yellow p-4'}">
                        <div class="flex items-center gap-4">
                            <x-bauhaus.shape type="${config.icon}" color="yellow" class="h-8 w-8" />
                            <h2 class="bauhaus-title text-lg">${config.label}</h2>
                        </div>
                        <span class="text-xs text-${config.textColor === 'white' ? 'bauhaus-yellow' : 'bauhaus-ink'}">
                            ${from} - ${to} dari ${total} laporan
                        </span>
                    </div>
                    <ul class="divide-y divide-bauhaus-black">
            `;
            
            reports.forEach(report => {
                const statusConfig = getStatusConfig(report.status);
                html += `
                    <li class="p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold">${report.nama_alat}</p>
                                <p class="text-xs font-bold uppercase tracking-widest text-bauhaus-blue">${report.nomor_laporan} · ${report.jenis_kerusakan || report.jenis_pekerjaan}</p>
                                <p class="mt-1 text-xs text-bauhaus-ink">
                                    ${report.tanggal_laporan || report.tanggal_pelaksanaan ? moment(report.tanggal_laporan || report.tanggal_pelaksanaan).format('D MMM YYYY') : '-'}
                                    · ${report.gedung !== '-' ? report.gedung : ''}${report.ruangan && report.ruangan !== '-' ? ', ' + report.ruangan : ''}
                                </p>
                            </div>
                            <span class="border border-bauhaus-black px-3 py-1 font-display text-xs uppercase tracking-widest ${statusConfig.className}">
                                ${statusConfig.label}
                            </span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="/laporan/status?nomor=${report.nomor_laporan}" class="bauhaus-btn bg-bauhaus-paper px-4 py-2 text-xs">Lihat Detail</a>
                            <a href="/laporan/pdf/${report.type || getTypeFromName(report.nomor_laporan)}/${report.id}" class="bauhaus-btn bg-bauhaus-black px-4 py-2 text-xs text-white">Preview PDF</a>
                        </div>
                    </li>
                `;
            });
            
            html += `
                    </ul>
                    ${last_page > 1 ? `
                        <div class="flex items-center justify-between border-t border-bauhaus-black p-4 bg-yellow-50">
                            <div class="text-xs text-bauhaus-ink">
                                Halaman ${current_page} dari ${last_page}
                            </div>
                            <div class="space-x-2">
                                ${current_page === 1 ? '<span class="px-3 py-1 text-xs text-gray-400">&laquo; Prev</span>' : `
                                    <button onclick="loadReports('${data.type}', ${current_page - 1})" class="bauhaus-btn bg-bauhaus-paper px-3 py-1 text-xs">Prev</button>
                                `}
                                ${current_page === last_page ? '<span class="px-3 py-1 text-xs text-gray-400">Next &raquo;</span>' : `
                                    <button onclick="loadReports('${data.type}', ${current_page + 1})" class="bauhaus-btn bg-bauhaus-paper px-3 py-1 text-xs">Next</button>
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
        
        function initializeSearchableSelects() {
            // Simple select functionality with location-based filtering
            const buildingSelect = document.getElementById('buildingSelect');
            const departmentSelect = document.getElementById('departmentSelect');
            const roomSelect = document.getElementById('roomSelect');
            
            buildingSelect?.addEventListener('change', function() {
                const buildingId = this.value;
                Array.from(departmentSelect?.options || []).forEach(opt => {
                    if (opt.dataset.building && opt.dataset.building !== buildingId) {
                        opt.style.display = 'none';
                    } else {
                        opt.style.display = 'block';
                    }
                });
                departmentSelect.value = '';
                roomSelect.value = '';
            });
            
            departmentSelect?.addEventListener('change', function() {
                const deptId = this.value;
                Array.from(roomSelect?.options || []).forEach(opt => {
                    if (opt.dataset.department && opt.dataset.department !== deptId) {
                        opt.style.display = 'none';
                    } else {
                        opt.style.display = 'block';
                    }
                });
                roomSelect.value = '';
            });
        }
    </script>
    
    {{-- Moment.js for date formatting --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
</x-bauhaus.layout>
