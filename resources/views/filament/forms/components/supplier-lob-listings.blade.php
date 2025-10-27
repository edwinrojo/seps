
<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Line of Business Listings</h2>
    <ol class="space-y-4 text-gray-500 list-decimal list-inside dark:text-gray-400">
        @foreach ($lob_listings as $lob)
            <li>{{ $lob['lobCategory']->title }}</li>
            @if (!empty($lob['lob_subcategories_list']))
                <ul class="ps-5 mt-2 space-y-1 list-disc list-inside">
                    @foreach ($lob['lob_subcategories_list'] as $sublob)
                        <li>{{ $sublob }}</li>
                    @endforeach
                </ul>
            @endif
        @endforeach
    </ol>
    @if ($reference_document)
        <a href="{{ asset($reference_document->file_path) }}" target="blank"
            class="inline-flex items-center px-3 py-2 text-sm font-medium mt-5
                text-center text-white bg-pink-600 rounded-lg hover:bg-pink-800
                dark:bg-pink-600 dark:hover:bg-pink-700 dark:focus:ring-pink-800">
            View Supporting Documents
            <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
            </svg>
        </a>
    @else
        <span class="fi-color fi-color-danger fi-text-color-700 dark:fi-text-color-400 fi-badge fi-size-m">
            <div class="flex flex-col">No supporting document has been uploaded or validated.</div>
        </span>
    @endif
</div>
