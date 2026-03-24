<?php

use App\Models\Payroll;
use App\Models\Salary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public $payroll;

    public function mount($id): void
    {
        $this->payroll = Payroll::inCompany()
            ->with(['salaries.employee.designation.department'])
            ->whereKey($id)
            ->firstOrFail();
    }

    public function generatePayslip($id)
    {
        $salary = Salary::query()
            ->whereHas('payroll', fn ($q) => $q->inCompany())
            ->with(['employee', 'payroll'])
            ->whereKey($id)
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.payslip', ['salary' => $salary]);
        $pdf->setPaper([0, 0, 400, 1500], 'portrait');

        $filepath = storage_path('app/' . Str::slug($salary->employee->name) . '-payslip.pdf');
        $pdf->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Payroll Details
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    View salaries included in this payroll run.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('payrolls.index') }}" wire:navigate>
                    <flux:button variant="ghost">
                        Back to Payroll
                    </flux:button>
                </a>
            </div>
        </div>

        <flux:separator />

        <flux:card class="border border-zinc-200 shadow-sm">
            <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <flux:heading size="lg">{{ $payroll->month_string }}</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            Payroll #{{ $payroll->id }}
                        </flux:subheading>
                    </div>

                    <div class="rounded-xl bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-700">
                        Salaries: {{ $payroll->salaries->count() }}
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if ($payroll->salaries->count())
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Employee
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Department
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Designation
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Gross Salary
                                </th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @foreach ($payroll->salaries as $salary)
                                <tr wire:key="salary-{{ $salary->id }}">
                                    <td class="px-5 py-4 text-sm text-zinc-700">
                                        {{ $salary->employee?->name ?? '—' }}
                                    </td>

                                    <td class="px-5 py-4 text-sm text-zinc-500">
                                        {{ $salary->employee?->designation?->department?->name ?? '—' }}
                                    </td>

                                    <td class="px-5 py-4 text-sm text-zinc-500">
                                        {{ $salary->employee?->designation?->name ?? '—' }}
                                    </td>

                                    <td class="px-5 py-4 text-sm font-semibold text-zinc-900">
                                        {{ number_format($salary->gross_salary, 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <flux:button
                                                size="sm"
                                                variant="ghost"
                                                wire:click="generatePayslip({{ $salary->id }})"
                                            >
                                                Payslip
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-14 text-center">
                        <p class="text-sm font-semibold text-zinc-900">No salaries found</p>
                        <p class="mt-1 text-sm text-zinc-500">
                            This payroll does not contain any salary records yet.
                        </p>
                    </div>
                @endif
            </div>
        </flux:card>
    </div>
</div>