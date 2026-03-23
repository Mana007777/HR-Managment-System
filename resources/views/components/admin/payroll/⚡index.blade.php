<?php

use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    public function rules()
    {
        return [
            'monthYear' => 'required',
        ];
    }

    public function generatePayroll()
    {
        $this->validate();

        $date = Carbon::parse($this->monthYear);

        if (Payroll::inCompany()->where('month', $date->format('Y-m'))->where('year', $date->format('Y'))->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'monthYear' => ['Payroll for this month and year already exists.']
            ]);
        } else {
            $payroll = new Payroll();
            $payroll->month = $date->format('m');
            $payroll->year = $date->format('Y');
            $payroll->company_id = session('company_id');
            $payroll->save();
            foreach(Employee::inCompany()->get() as $employee) {
                $contract = $employee->getActiveContract($date->startOfMonth()->toDateString(), $date->endOfMonth()->toDateString());
                if($contract)
                    {
                        $payroll->employees()->create([
                            'employee_id' => $employee->id,
                            'gross_salary' => $employee->getTotalEarnings($date->format('Y-m')),
                        ]);
                    }
                
              session()->flash('success', 'Payroll generated successfully.');
            }
        }
    }

    public function updatePayroll($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->salaries()->delete();
        foreach(Employee::inCompany()->get() as $employee) {
            $contract = $employee->getActiveContract($payroll->month.'-'.$payroll->year.'-01', Carbon::parse($payroll->month.'-'.$payroll->year.'-01')->endOfMonth()->toDateString());
            if($contract)
                {
                    $payroll->salaries()->create([
                        'employee_id' => $employee->id,
                        'gross_salary' => $employee->getTotalEarnings($payroll->year.'-'.$payroll->month),
                    ]);
                }
        }
        session()->flash('success', 'Payroll updated successfully.');
    }

    public function getPayrolls()
    {
        return Payroll::inCompany()->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10);
    }
};
?>

<div>
    {{-- Walk as if you are kissing the Earth with your feet. - Thich Nhat Hanh --}}
</div>