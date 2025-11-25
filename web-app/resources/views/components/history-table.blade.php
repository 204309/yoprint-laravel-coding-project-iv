@php
    // Helper functions for sorting arrows and next sort direction
    function arrow($col, $sort, $direction)
    {
        if ($col !== $sort)
            return '';
        return $direction === 'asc' ? '▲' : '▼';
    }

    function nextDirection($col, $sort, $direction)
    {
        if ($col !== $sort)
            return 'asc';
        return $direction === 'asc' ? 'desc' : 'asc';
    }
@endphp

<div
    class="max-w-[calc(100vw_-_150px)] max-w-7xl mx-auto mt-10 mb-15 p-6 bg-white bg-opacity-50 backdrop-blur-md shadow-xl rounded-xl overflow-hidden">
    {{-- Main wrapper for positioning --}}
    <div class="relative">
        {{-- table headers --}}
        <table class="table-fixed w-full bg-white text-black rounded-md">
            <thead class="bg-sky-100 bg-opacity-50 sticky top-0">
                <tr class="px-6 py-3 text-left text-xs font-bold text-indigo-500 uppercase tracking-wider">
                    <th class="px-4 py-5 cursor-pointer">
                        <a href="?sort=time&direction={{ nextDirection('time', $sort, $direction) }}"
                            class="flex items-center gap-1">
                            Time
                            <span>{{ arrow('time', $sort, $direction) }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-5 cursor-pointer">
                        <a href="?sort=name&direction={{ nextDirection('name', $sort, $direction) }}"
                            class="flex items-center gap-1">
                            File Name
                            <span>{{ arrow('name', $sort, $direction) }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-5">Status</th>
                </tr>
            </thead>
        </table>

        {{-- table rows (separated into its own scrolling container for consistent behavior across browser) --}}
        <div class="overflow-y-auto max-h-[calc(100vh_-_370px)]">
            <table class="table-fixed w-full bg-opacity-70 divide-y divide-gray-200 text-black">
                <tbody class="bg-opacity-70 divide-y divide-gray-200 text-black">
                    @forelse ($uploadedFiles as $file)
                        <tr data-file-id="{{ $file->id }}" class="hover:bg-gray-100 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap w-1/3">{{-- Use widths to align with headers --}}
                                {{-- {{ $file->created_at->format('Y-m-d h:i A') }} --}}
                                <span class="local-time" data-timestamp="{{ $file->created_at->toISOString() }}">
                                    {{ $file->created_at->format('Y-m-d h:i A') }}
                                </span>


                                <br>
                                <span class="time-ago text-gray-500"
                                    data-timestamp="{{ $file->created_at->toISOString() }}">
                                    ({{ $file->created_at->diffForHumans() }})
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap w-1/3 overflow-x-auto ">{{ $file->file_name }}</td>
                            <td class="px-4 py-4 whitespace-nowrap status-cell w-1/3">
                                <x-status :status="$file->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-35 text-center" colspan="3">No uploaded files found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    // Initialize Laravel Echo for real-time status updates
    function initializeEcho() {
        if (typeof window.Echo === 'undefined') {
            setTimeout(initializeEcho, 100);
            return;
        }

        // Listen for file process status updates
        window.Echo.channel('file-process-status-updates')
            .listen('FileProcessStatusUpdated', (event) => {
                console.log('Status update received:', event);

                const row = document.querySelector(`tbody tr[data-file-id="${event.file_id}"]`);
                if (!row) return;

                const statusCell = row.querySelector('.status-cell');
                statusCell.textContent = event.status;
                // prefer the rendered component HTML, fallback to plain status 
                statusCell.innerHTML = event.status_html ?? event.status;


                // Highlight status briefly
                statusCell.classList.add('bg-yellow-100');
                setTimeout(() => statusCell.classList.remove('bg-yellow-100'), 1000);
            });
    }

    // Format as YYYY/MM/DD hh:mm AM/PM in local timezone
    function formatLocalDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        let hours = date.getHours();
        const minutes = String(date.getMinutes()).padStart(2, '0');

        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 → 12

        return `${year}/${month}/${day} ${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
    }

    // Convert all timestamps to local time
    function convertToLocalTimes() {
        document.querySelectorAll('.local-time').forEach(el => {
            const ts = el.getAttribute('data-timestamp');
            if (!ts) return;

            const date = new Date(ts);
            el.textContent = formatLocalDate(date);
        });
    }


    // Update all "time-ago" timestamps
    function updateTimestamps() {
        document.querySelectorAll('.time-ago').forEach(el => {
            const rawTimestamp = el.getAttribute('data-timestamp');
            if (rawTimestamp) el.textContent = calculateDiffForHumans(new Date(rawTimestamp));
        });
    }

    // function to format time difference
    function calculateDiffForHumans(date) {
        const seconds = Math.floor((Date.now() - new Date(date)) / 1000);
        const units = [
            { label: "year", secs: 31536000 },
            { label: "month", secs: 2592000 },
            { label: "day", secs: 86400 },
            { label: "hour", secs: 3600 },
            { label: "minute", secs: 60 },
            { label: "second", secs: 1 },
        ];

        for (const unit of units) {
            const interval = Math.floor(seconds / unit.secs);
            if (interval >= 1) return `(${interval} ${unit.label}${interval > 1 ? "s" : ""} ago)`;
        }
        return "(just now)";
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        initializeEcho();
        convertToLocalTimes();
        updateTimestamps();
        setInterval(updateTimestamps, 10000); // Update every 10s
    });
</script>