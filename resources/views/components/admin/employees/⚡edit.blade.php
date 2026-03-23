<?php

use App\Models\Designation;
use App\Models\Employee;
use Livewire\Component;

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

    public function mount($id)
    {
        $this->employee = Employee::find($this->employeeId);
        $this->department_id = $this->employee->designation->department_id;
    }

    public function save()
    {
        $this->validate();

        $this->employee->save();

        session()->flash('message', 'Employee updated successfully.');

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

<div>
    {{-- Let all your things have their places; let each part of your business have its time. - Benjamin Franklin --}}
</div>