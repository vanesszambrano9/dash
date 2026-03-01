<?php

namespace App\Livewire\Auth;

use Filament\Notifications\Notification;
use Livewire\Component;

class ChangeRoleController extends Component
{
    public $roles;

    public function mount()
    {
        $this->roles = auth()->user()->roles;
    }

    public function changeRole(?int $role)
    {
        $user = auth()->user();

        if ($user->setRole($role)) {
            $this->js('location.reload();');

            Notification::make()
                ->title(__('Role changed successfully'))
                ->success()
                ->send();

        } else {

            Notification::make()
                ->title(__('There was an error changing the role'))
                ->danger()
                ->send();

        }

    }

    public function render()
    {
        return view('livewire.auth.change-role-controller');
    }
}
