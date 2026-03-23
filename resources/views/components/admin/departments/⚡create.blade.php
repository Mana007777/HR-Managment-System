<?php

use App\Models\Department;
use Livewire\Component;

new class extends Component
{
    public $department;
    public function rules()
    {
        return [
            'department.name' => 'required|string|max:255',
        ];
    }

    public function mount()
    {
        $this->department = new Department();
    }

    public function save()
    {
        $this->validate();
        $this->department->company_id = session('company_id');
        $this->department->save();
        session()->flash('success', 'Department created successfully.');
        return $this->redirectIntended('departments.index');
    }
};
?>

<div>
    {{-- It always seems impossible until it is done. - Nelson Mandela --}}
</div>