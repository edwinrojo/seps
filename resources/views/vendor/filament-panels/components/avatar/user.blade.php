@props([
    'user' => filament()->auth()->user(),
])

@php
    $src = filament()->getUserAvatarUrl($user);
    $alt = __('filament-panels::layout.avatar.alt', ['name' => filament()->getUserName($user)]);
@endphp

<div class="flex items-center justify-end w-full">
    <!-- Text (right-aligned) -->
    <div class="flex flex-col items-end text-right leading-tight mr-2" style="text-align: right;">
        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ $user->name }}
        </span>
        <span class="text-xs text-gray-500 dark:text-gray-400">
            {{ $user->role->getLabel() ?? 'No Role' }}
        </span>
    </div>

    <!-- Avatar -->
    <x-filament::avatar
        :src="$src"
        :alt="$alt"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->class(['fi-user-avatar w-10 h-10 rounded-full flex-shrink-0'])
        "
    />
</div>
