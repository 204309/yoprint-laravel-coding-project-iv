<form id="fileUploadForm" action="/" method="post" enctype="multipart/form-data">
    @csrf
    <div
        class="container flex max-w-[calc(100vw_-_350px)] mt-15 mx-auto p-4 bg-white rounded-lg backdrop-blur-md justify-self-auto gap-x-3">
        <div id="drop-container" class="w-[84%] p-5 rounded-md ">
            <label for="file_input" id="fileUploadLabel" class="text-black text-center pl-4 pt-2.5">
                Select File/Drag and Drop
            </label>
            <input id="file_input" type="file" name="file" class="hidden" accept=".csv" />
        </div>
        <div class="flex w-[15%] justify-end items-center">
            <x-button type="submit" id="uploadButton">Upload File</x-button>
        </div>
    </div>
</form>

<!-- Notification bubble -->
<div id="uploadMessage"
    class="fixed bottom-6 left-1/2 px-6 py-3 rounded-lg shadow-lg text-white pointer-events-none transition-all duration-500 z-50"
    style="transform: translateX(-50%) translateY(50px); opacity: 0;">
</div>



<script>
    const fileInput = document.getElementById('file_input');
    const fileUploadLabel = document.getElementById('fileUploadLabel');
    const dropContainer = document.getElementById('drop-container');
    const fileUploadForm = document.getElementById('fileUploadForm');
    const uploadButton = document.getElementById('uploadButton');
    // const uploadMessage = document.getElementById('uploadMessage');

    function showUploadMessage(message, success = true) {
        const msgEl = document.getElementById('uploadMessage');
        msgEl.textContent = message;
        msgEl.classList.remove('bg-green-500', 'bg-red-500');
        msgEl.classList.add(success ? 'bg-emerald-500' : 'bg-red-500');

        // Show message (slide up + fade in)
        msgEl.style.opacity = '1';
        msgEl.style.transform = 'translateX(-50%) translateY(-60px)';

        // Hide after 5 seconds (slide down + fade out)
        setTimeout(() => {
            msgEl.style.opacity = '0';
            msgEl.style.transform = 'translateX(-50%) translateY(50px)'; // slide down
        }, 5000);
    }



    // Prevent default behavior for drag and drop events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
        fileInput.addEventListener(event, preventDefaults, false);
        dropContainer.addEventListener(event, preventDefaults, false);
    });

    function preventDefaults(event) {
        event.preventDefault();
        event.stopPropagation();
    }

    // Highlight drop area on drag events
    ['dragenter', 'dragover'].forEach(event => {
        dropContainer.addEventListener(event, () => {
            dropContainer.classList.add('border-2', 'border-dashed', 'border-amber-600', 'bg-amber-50');
        }, false);
    });

    // Remove highlight on drag leave
    dropContainer.addEventListener('dragleave', () => {
        dropContainer.classList.remove('border-2', 'border-dashed', 'border-amber-600', 'bg-amber-50');
    }, false);

    // Handle file drop and update label
    // Only the light highlight stays on drop
    dropContainer.addEventListener('drop', (event) => {
        const dt = event.dataTransfer;
        const files = dt.files;
        fileInput.files = files;

        const fileName = files[0]?.name || 'Select File/Drag and Drop';
        fileUploadLabel.textContent = fileName;
        dropContainer.classList.remove('border-2', 'border-dashed', 'border-amber-600', 'bg-amber-100');
    });

    // Remove highlight on click
    ['click'].forEach(event => {
        dropContainer.addEventListener(event, () => {
            dropContainer.classList.add('border-2', 'border-dashed', 'border-amber-600',);
            dropContainer.classList.remove(
                'bg-amber-50');
        }, false);
    });

    // On change, add light highlight (especially change after click event)
    ['change'].forEach(event => {
        dropContainer.addEventListener(event, () => {
            dropContainer.classList.add(
                'bg-amber-50');
            dropContainer.classList.remove(
                'border-2', 'border-dashed', 'border-amber-600');
        }, false);
    });

    // Handle click to open file dialog
    dropContainer.addEventListener('click', () => {
        fileInput.click();
    });
    // Update label when file is selected after click event
    fileInput.addEventListener('change', () => {
        const fileName = fileInput.files[0]?.name || 'Select File/Drag and Drop';
        fileUploadLabel.textContent = fileName;
    });

    // Handle form submission with AJAX to use transformer response
    fileUploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const file = fileInput.files[0];
        if (!file) {
            alert('Please select a file first');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        // Disable button during upload
        uploadButton.disabled = true;
        uploadButton.textContent = 'Uploading...';
        // uploadMessage.classList.add('hidden');

        try {
            // Perform the AJAX request
            const response = await fetch('/', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json', // return in JSON (transformer)
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                showUploadMessage('Upload failed. Please try again.', false);

                throw new Error('Upload failed. Please try again.');
            }

            // Get the transformer data
            const responseData = await response.json();

            // The transformer returns: { id, file_name, status, created_at }
            const fileData = responseData.data || responseData;

            // Console the transformer data 
            // console.log('Uploaded File Data from Transformer:', fileData);
            // console.log('File ID:', fileData.id);
            // console.log('File Name:', fileData.file_name);
            // console.log('Status:', fileData.status);
            // console.log('Created At:', fileData.created_at);

            // Show success message using transformer data
            showUploadMessage(`File "${fileData.file_name}" uploaded successfully! Status: ${fileData.status}`, true);
            fileUploadLabel.textContent = `File "${fileData.file_name}" uploaded successfully! Status: ${fileData.status}`;


            // Reset form
            fileInput.value = '';
            fileUploadLabel.textContent = 'Select File/Drag and Drop';

            // Reload page after 1 second to see new entry in table
            setTimeout(() => {
                window.location.href = '/';
            }, 1000);

        } catch (error) {
            showUploadMessage('Upload failed. Please try again.', false);

        } finally {
            uploadButton.disabled = false;
            uploadButton.textContent = 'Upload File';
        }
    });
</script>