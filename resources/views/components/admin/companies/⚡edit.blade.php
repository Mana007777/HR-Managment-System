<?php

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
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
    public function mount($id){
        $this->company =Company::find($id);
    }
    public function save(){
        $this->validate();
        if($this->logo){
            if($this->company->logo){
                Storage::disk('public')->delete($this->company->logo);
            }
            $this->company->logo = $this->logo->store('logos', 'public');
        }
        $this->company->save();
        session()->flash('success', 'Company updated successfully.');
        return $this->redirectIntended('companies.index');
    }
};
?>

<div>
    {{-- He who is contented is rich. - Laozi --}}
</div>