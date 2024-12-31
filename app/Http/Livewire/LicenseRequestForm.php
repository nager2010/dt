<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LicenseRequest;

class LicenseRequestForm extends Component
{
    public $form = [];

    public function submit()
    {
        $this->validate([
            'form.fullName' => 'required|string|max:120',
            'form.nationalID' => 'required|string|max:20',
            'form.phoneNumber' => 'required|string|max:15',
            'form.projectName' => 'required|string|max:120',
            'form.license_type_id' => 'required',
            'form.municipality_id' => 'required',
            'form.region_id' => 'required',
        ]);

        LicenseRequest::create($this->form);

        session()->flash('message', 'تم تقديم طلبك بنجاح.');
        $this->reset('form');
    }

    public function render()
    {
        return view('livewire.license-request-form');
    }
}
