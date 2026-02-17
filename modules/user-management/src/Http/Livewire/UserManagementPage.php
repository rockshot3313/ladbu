<?php

namespace Ladbu\LaravelLadwireUserManagement\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

class UserManagementPage extends Component
{
    use WithPagination;

    public string $search = '';

    // Reset pagination when searching
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        Flux::toast('User deleted successfully.');
    }

    public function render()
    {
        return view('laravel-ladwire-user-management::pages.user-management.user-management', [
            'users' => User::where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->paginate(10),
        ]);
    }
}
