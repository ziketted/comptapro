<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public $name = '';
    public $email = '';
    public $role = 'cashier';
    public $password = '';
    
    public $showCreateForm = false;
    public $isEditing = false;
    public $editingUserId = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->editingUserId ?? 'NULL'),
            'role' => 'required|in:manager,cashier',
            'password' => $this->isEditing ? 'nullable|min:8' : 'required|min:8',
        ];
    }

    public function createUser()
    {
        $this->validate();

        try {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'tenant_id' => auth()->user()->tenant_id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $this->resetForm();
            session()->flash('success', 'Utilisateur créé avec succès !');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function editUser($userId)
    {
        $user = User::where('id', $userId)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->firstOrFail();

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->isEditing = true;
        $this->showCreateForm = true;
        $this->password = ''; // Don't fill password
    }

    public function updateUser()
    {
        $this->validate();

        try {
            $user = User::where('id', $this->editingUserId)
                ->where('tenant_id', auth()->user()->tenant_id)
                ->firstOrFail();

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);

            $this->resetForm();
            session()->flash('success', 'Utilisateur mis à jour avec succès !');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'role', 'showCreateForm', 'isEditing', 'editingUserId']);
        $this->role = 'cashier'; // Reset to default
    }

    public function deleteUser($userId)
    {
        if ($userId === auth()->id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }

        $user = User::where('id', $userId)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->firstOrFail();

        $user->delete();
        session()->flash('success', 'Utilisateur supprimé.');
    }

    public function render()
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.settings.users', [
            'users' => $users
        ])->layout('layouts.app');
    }
}
