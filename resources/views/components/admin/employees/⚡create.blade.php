<?php

use Livewire\Component;
use App\Models\Employee;
use App\Models\Designation;

new class extends Component
{
    public $employee;
    public $department_id;

    public function rules()
    {
        return [
            'employee.name' => 'required|string|max:255',
            'employee.email' => 'required|email|unique:employees,email',
            'employee.phone' => 'required|string|max:255',
            'employee.address' => 'required|string|max:255',
            'employee.designation_id' => 'required|exists:designations,id',
        ];
    }

    public function mount()
    {
        $this->employee = new Employee();
    }

    public function save()
    {
        $this->validate();

        $this->employee->save();

        session()->flash('message', 'Employee created successfully.');

        return $this->redirect(route('employees.index'));
    }

    public function getDesignationsProperty()
    {
        if (!$this->department_id) {
            return collect();
        }

        return Designation::inCompany()
            ->where('department_id', $this->department_id)
            ->get();
    }
};
?>
?>

<div>
    {{-- When there is no desire, all things are at peace. - Laozi --}}
</div>