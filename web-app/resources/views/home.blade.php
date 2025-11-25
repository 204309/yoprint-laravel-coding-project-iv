<x-layout>
    <x-file-uploader />
    <x-history-table :uploadedFiles="$uploadedFiles" :sort="$sort" :direction="$direction" />

</x-layout>