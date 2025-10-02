<?php

namespace App\Livewire;

use Livewire\Component;

class StatusView extends Component
{
    public $statuses;

    public function render()
    {
        return view('livewire.status-view');
    }

    public function mount()
    {
        // return sorted statuses by created_at desc
        $this->statuses = $this->statuses->sortByDesc('created_at');
    }
}
