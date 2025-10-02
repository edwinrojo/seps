<div>

    <ol class="relative border-s border-gray-200 dark:border-gray-700">
    @foreach ($statuses as $status)
        @php
            $icon_background = 'bg-' . $status->status->getColor() . '-100';
            $icon_background_dark = 'dark:bg-' . $status->status->getColor() . '-900';

            $filament_icon = $status->status->getFilamentIcon();
            $filament_icon_color = 'text-' . $status->status->getColor() . '-500';

            $avatar = $status->user->getFilamentAvatarUrl();
            $status_color = $status->status->getColor();
            $status_label = $status->status->getLabel();
        @endphp
        <li class="mb-6 ms-6">
            <span class="absolute flex items-center justify-center w-7 h-7 {{ $icon_background }} rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 {{ $icon_background_dark }}">
                  <x-dynamic-component :component="$filament_icon" class="w-4 h-4 text-custom-800 fi-color-{{ $status_color }}"/>
            </span>

            <h3 class="flex items-center mb-1 text-base font-semibold text-custom-800 fi-color-{{ $status_color }} dark:text-white">{{ $status_label }}
                {{-- display if index is 0 --}}
                @if ($loop->index === 0)
                    <span class="bg-primary-100 text-primary-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-primary-900 dark:text-primary-300 ms-3">Latest</span>
                @endif
            </h3>



            <div class="flex items-start gap-2.5 mt-3">
                <img class="w-8 h-8 rounded-full" src="{{ $avatar }}" alt="Bonnie Green image">
                <div class="flex flex-col gap-1">
                    <div class="flex flex-col w-full leading-1.5 p-4 border-gray-200 bg-gray-100 rounded-e-xl rounded-es-xl dark:bg-gray-700">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $status->user->name }}</span>
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $status->user->role->getLabel() }})</span>
                        </div>
                        <div class="flex items-start my-2.5 bg-gray-50 dark:bg-gray-600 rounded-xl p-2">
                            <div>
                                Remarks:
                            </span>
                            <span class="flex text-sm font-normal text-gray-500 dark:text-gray-400 gap-2 mt-2 ml-4">
                                {!! $status->remarks ?? 'No remarks provided.' !!}
                            </span>
                            </div>
                        </div>
                        <span class="text-sm font-normal text-primary-500 dark:text-gray-400">{{ $status->created_at->format('F j, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

        </li>

    @endforeach
    </ol>

</div>
