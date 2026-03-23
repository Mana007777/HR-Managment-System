<?php

use App\Models\Payroll;
use App\Models\Salary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public $payroll;

    public function mount($id)
    {
        $this->payroll = Payroll::inCompany()->find($id);
       
    }

    public function generatePayslip($id)
    {
        $salary = Salary::find($id);
        $pdf = Pdf::loadView('pdf.payslip', ['salary' => $salary]);
        $pdf->setPaper(array(0, 0, 400, 1500), 'portrait');
        $filepath = storage_path(Str::slug($salary->employee->name) . '-payslip.pdf');
        $pdf->save($filepath);
        return response()->download($filepath)->deleteFileAfterSend(true);
    }
};
?>

<div>
    {{-- We must ship. - Taylor Otwell --}}
</div>