<table class="table-fixed w-[89%] mt-10 mx-auto bg-white text-black rounded-md ">
    {{-- need to add: when clicked on the header, it will sort according to
    time: recent-newest, file name: alphabetically asc or desc, respectively --}}
    <thead>
        <tr>
            <th class="border px-4 py-1">Time</th>
            <th class="border px-4 py-1">File Name</th>
            <th class="border px-4 py-1">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($uploadedFiles as $file)
            <tr data-file-id="{{ $file->id }}">
                <td class="border px-4 py-2">
                    {{ $file->created_at->format('Y-m-d h:i A') }}
                    <br>
                    <span class="time-ago" data-timestamp="{{ $file->created_at->toISOString() }}">
                        ({{ $file->created_at->diffForHumans() }})
                    </span>
                </td>
                <td class="border px-4 py-2">{{ $file->file_name }}</td>
                <td class="border px-4 py-2 status-cell">{{ $file->status }}</td>
            </tr>
        @empty
            <tr>
                <td class="border px-4 py-2 text-center" colspan="3">No uploaded files found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@script
<script>
    // Wait for Echo to be initialized before setting up listeners
    function initializeEcho() {
        if (typeof window.Echo === 'undefined') {
            setTimeout(initializeEcho, 100);
            return;
        }

        // Listen for file process status updates via Laravel Echo
        window.Echo.channel('file-process-status-updates')
            .listen('FileProcessStatusUpdated', (event) => {
                console.log('Status update received:', event);

                const fileId = event.file_id;

                const row = document.querySelector(`tbody tr[data-file-id="${fileId}"]`);

                if (!row) return;

                const statusCell = row.querySelector('.status-cell');
                statusCell.textContent = event.status;

                statusCell.classList.add('bg-yellow-100');
                setTimeout(() => statusCell.classList.remove('bg-yellow-100'), 1000);
            });


    }


    function updateTimestamps() {
        // Find all elements with the 'time-ago' class
        document.querySelectorAll('.time-ago').forEach(element => {
            const rawTimestamp = element.getAttribute('data-timestamp');
            // Check if timestamp is valid
            if (rawTimestamp) {
                // Use a client-side library (built-in Date object and a simple function or a library) to format the time difference
                element.textContent = calculateDiffForHumans(new Date(rawTimestamp));
            }
        });
    }

    // A simple client-side function to calculate the difference between now and the uploaded date
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
            if (interval >= 1) {
                return `(${interval} ${unit.label}${interval > 1 ? "s" : ""} ago)`;
            }
        }

        return "(just now)";
    }

    // Initialize Echo and timestamps when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        initializeEcho();
        // Update the timestamps initially and then every 10 seconds
        updateTimestamps();
        setInterval(updateTimestamps, 10000);
    });

</script>