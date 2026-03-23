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

    public function mount($id)
    {
        $this->department = Department::find($id);
    }   

    public function save()
    {
        $this->validate();
        $this->department->save();
        session()->flash('success', 'Department updated successfully.');
        return $this->redirectIntended('departments.index');
    }
};
?>

<div>
    {{-- People find pleasure in different ways. I find it in keeping my mind clear. - Marcus Aurelius --}}
</div>