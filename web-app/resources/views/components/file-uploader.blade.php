<form action="/" method="post" enctype="multipart/form-data">
    @csrf
    <div class="container flex mt-15 mx-auto p-4 bg-white rounded-lg backdrop-blur-md justify-self-auto gap-x-3">
        <div id="drop-container" class="w-[84%] p-5 rounded-md ">
            <label for="file" id="fileUploadLabel" class="text-black text-center pl-4 pt-2.5">Select File/Drag and
                Drop</label>
            <input id="file" type="file" name="file" class="hidden text-black text-center pl-4 pt-2.5" accept=".csv" />

        </div>
        <div class="flex w-[15%] justify-end items-center">
            <x-button type="submit">Upload File</x-button>
        </div>
        @if (session('success'))
            <div class="text-green mt-2">
                {{ session('success') }}
            </div>
        @endif
    </div>
</form>

<script>

    // Update label when file is selected
    const fileInput = document.getElementById('file');
    const fileUploadLabel = document.getElementById('fileUploadLabel');
    fileInput.addEventListener('change', () => {
        const fileName = fileInput.files[0]?.name || 'Select File/Drag and Drop';
        fileUploadLabel.textContent = fileName;
    });

    // Drag and Drop functionality
    const dropContainer = document.getElementById('drop-container');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        fileInput.addEventListener(eventName, preventDefaults, false);
        dropContainer.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(event) {
        event.preventDefault();
        event.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropContainer.addEventListener(eventName, () => {
            dropContainer.classList.add('border-2', 'border-dashed', 'border-amber-600', 'bg-amber-50');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropContainer.addEventListener(eventName, () => {
            dropContainer.classList.remove('border-2', 'border-dashed', 'border-amber-600', 'bg-amber-100');
        }, false);
    });

    dropContainer.addEventListener('drop', (event) => {
        const dt = event.dataTransfer;
        const files = dt.files;
        fileInput.files = files;

        const fileName = files[0]?.name || 'Select File/Drag and Drop';
        fileUploadLabel.textContent = fileName;
    });

    dropContainer.addEventListener('click', () => {
        fileInput.click();
    });
    fileInput.addEventListener('change', () => {
        const fileName = fileInput.files[0]?.name || 'Select File/Drag and Drop';
        fileUploadLabel.textContent = fileName;
    });

    // Upload File after button is clicked
    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('/', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                location.reload(); // Refresh to see new entry
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    }

</script>