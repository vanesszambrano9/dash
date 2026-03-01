<?php

namespace App\Livewire\AdminGeneral\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.collapsable')]
class Dashboard extends Component
{

    public function render()
    {
       return view('livewire.admin-general.dashboard.dashboard');
    }
}