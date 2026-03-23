<?php

use App\Models\Contract;
use App\Models\Department;
use App\Models\Employee;
use Livewire\Component;

new class extends Component
{
    public $contract;

    public $search = '';

    public $department_id;

    public function rules()
    {
        return [
            'contract.designation_id' => 'required',
            'contract.employee_id' => 'nullable|exists:employees,id',
            'contract.start_date' => 'required|date',
            'contract.end_date' => 'required|date|after_or_equal:contract.start_date',
            'contract.rate_type' => 'required',
            'contract.rate' => 'required|numeric',
        ];
    }
    public function mount()
    {
        $this->contract = new Contract();
    }

    public function selectEmployee($id)
    {
        $this->contract->employee_id = $id;
        $this->search = $this->contract->employee->name;
    }

    public function save()
    {
        $this->validate();

        $this->contract->save();

        session()->flash('message', 'Contract created successfully.');

        return $this->redirectIntended(route('contracts.index'));
    }
    public function searchEmployees()
    {
        $employees = Employee::inCompany()->scopeSearch($this->search)->get();
    }
    public function departments()
    {
        return Department::inCompany()->get();
    }
    public function designations()
    {
        return $this->department_id ? Department::find($this->department_id)->designations : collect();
    }
};
?>

<div>
    {{-- Happiness is not something readymade. It comes from your own actions. - Dalai Lama --}}
</div>