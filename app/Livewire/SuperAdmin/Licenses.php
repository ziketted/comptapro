<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LicenseKey;
use Illuminate\Support\Str;

class Licenses extends Component
{
    use WithPagination;

    public $amount = 1;

    public function generate()
    {
        $this->validate([
            'amount' => 'required|integer|min:1|max:50',
        ]);

        for ($i = 0; $i < $this->amount; $i++) {
            LicenseKey::create([
                'key' => strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)),
                'status' => 'UNUSED',
            ]);
        }

        session()->flash('success', $this->amount . ' clé(s) générée(s) avec succès !');
        $this->amount = 1;
    }

    public function delete($id)
    {
        $key = LicenseKey::find($id);
        if ($key && $key->status === 'UNUSED') {
            $key->delete();
            session()->flash('success', 'Clé supprimée.');
        }
    }

    public function render()
    {
        $keys = LicenseKey::with('tenant')->latest()->paginate(10);
        return view('livewire.super-admin.licenses', [
            'keys' => $keys
        ]);
    }
}
