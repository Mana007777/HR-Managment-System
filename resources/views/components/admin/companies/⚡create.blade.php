<?php

use App\Models\Company;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;
    public $company;

    public $logo;
    public function rules(){
        return [
            'company.name' => 'required|string|max:255',
            'company.email' => 'required|email|max:255',
            'company.website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,svg', // 1MB Max
        ];
    }
    public function mount(){
        $this->company = new Company();
    }
    public function save(){
        $this->validate();
        if($this->logo){
            $this->company->logo = $this->logo->store('logos', 'public');
        }
        $this->company->save();
        session()->flash('success', 'Company created successfully.');
        return $this->redirectIntended('companies.index');
    }
};
?>

<div>
    {{-- Breathing in, I calm body and mind. Breathing out, I smile. - Thich Nhat Hanh --}}
</div>