<table class="table-fixed w-[89%] mt-10 mx-auto bg-white text-black rounded-md ">
    {{-- need to add: when clicked on the header, it will sort according to
    time: recent-newest, file name: alphabetically asc or desc, respectively --}}
    <tr>
        <th class="border px-4 py-1">Time</th>
        <th class="border px-4 py-1">File Name</th>
        <th class="border px-4 py-1">Status</th>
    </tr>

    @forelse ($uploadedFiles as $file)
        <tr>
            <td class="border px-4 py-2">
                {{ $file->created_at->format('Y-m-d h:i A') }}
                <br>
                <span class="time-ago" data-timestamp="{{ $file->created_at->toISOString() }}">
                    ({{ $file->created_at->diffForHumans() }})
                </span>
            </td>
            <td class="border px-4 py-2">{{ $file->file_name }}</td>
            <td class="border px-4 py-2">{{ $file->status }}</td>
        </tr>
    @empty
        <tr>
            <td class="border px-4 py-2 text-center" colspan="3">No uploaded files found.</td>

        </tr>
    @endforelse


</table>

<script>
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

    // A simple client-side function to calculate the difference (can be more complex for full Carbon functionality)
    function calculateDiffForHumans(date) {
        const seconds = Math.floor((new Date() - date) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " years ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " months ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " days ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " hours ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " minutes ago";
        return Math.floor(seconds) + " seconds ago";
    }

    // Update the timestamps initially and then every 10 seconds
    updateTimestamps();
    setInterval(updateTimestamps, 10000);

</script>