<div>
    <div class="flex flex-col items-center mb-5">
        <img src="{{ asset('project_files/SEPS Logo.png') }}" class="h-20" />
        {{-- <p class="text-center text-lg font-medium mt-2">Suppliers Eligibility and Profiling System</p> --}}
    </div>
    <x-filament-panels::page.simple>
        {{ $this->content }}
    </x-filament-panels::page.simple>
</div>
