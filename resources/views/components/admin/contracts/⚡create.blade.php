<?php

use App\Models\Contract;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Livewire\Component;

new class extends Component
{
    public ?int $department_id = null;
    public ?int $designation_id = null;
    public ?int $employee_id = null;

    public string $search = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $rate_type = '';
    public string $rate = '';

    public function rules(): array
    {
        return [
            'department_id' => 'required|integer',
            'designation_id' => 'required|integer',
            'employee_id' => 'nullable|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rate_type' => 'required|in:daily,monthly',
            'rate' => 'required|numeric',
        ];
    }

    public function updatedDepartmentId(): void
    {
        $this->designation_id = null;
        $this->employee_id = null;
        $this->search = '';
    }

    public function updatedDesignationId(): void
    {
        $this->employee_id = null;
        $this->search = '';
    }

    public function selectEmployee($id): void
    {
        $employee = Employee::inCompany()
            ->whereKey($id)
            ->first();

        if (! $employee) {
            return;
        }

        if ($this->designation_id && $employee->designation_id != $this->designation_id) {
            return;
        }

        $this->employee_id = $employee->id;
        $this->search = $employee->name;
    }

    public function save()
    {
        $this->validate();

        $departmentExists = Department::inCompany()
            ->whereKey($this->department_id)
            ->exists();

        if (! $departmentExists) {
            $this->addError('department_id', 'The selected department is invalid.');
            return;
        }

        $designation = Designation::inCompany()
            ->whereKey($this->designation_id)
            ->where('department_id', $this->department_id)
            ->first();

        if (! $designation) {
            $this->addError('designation_id', 'The selected designation is invalid.');
            return;
        }

        if ($this->employee_id) {
            $employee = Employee::inCompany()
                ->whereKey($this->employee_id)
                ->where('designation_id', $designation->id)
                ->first();

            if (! $employee) {
                $this->addError('employee_id', 'The selected employee is invalid.');
                return;
            }
        }

        Contract::create([
            'designation_id' => $designation->id,
            'employee_id' => $this->employee_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'rate_type' => $this->rate_type,
            'rate' => $this->rate,
        ]);

        session()->flash('success', 'Contract created successfully.');

        return $this->redirect(route('contracts.index'), navigate: true);
    }

    public function getDepartmentsProperty()
    {
        return Department::inCompany()
            ->orderBy('name')
            ->get();
    }

    public function getDesignationsProperty()
    {
        if (! $this->department_id) {
            return collect();
        }

        return Designation::inCompany()
            ->where('department_id', $this->department_id)
            ->orderBy('name')
            ->get();
    }

    public function getEmployeesProperty()
    {
        if (! $this->designation_id || trim($this->search) === '') {
            return collect();
        }

        return Employee::inCompany()
            ->where('designation_id', $this->designation_id)
            ->search($this->search)
            ->limit(8)
            ->get();
    }

    public function getSelectedEmployeeProperty()
    {
        if (! $this->employee_id) {
            return null;
        }

        return Employee::inCompany()
            ->whereKey($this->employee_id)
            ->first();
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Create Contract
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Add a new contract for the selected company.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('contracts.index') }}" wire:navigate>
                    <flux:button variant="ghost">
                        Back to Contracts
                    </flux:button>
                </a>
            </div>
        </div>

        <flux:separator />

        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition
                style="display:none;"
                class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
            >
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                        <flux:heading size="lg">Contract Information</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            Enter the contract details and optionally assign it to an employee.
                        </flux:subheading>
                    </div>

                    <div class="px-5 py-5 sm:px-6">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Department
                                </label>

                                <select
                                    wire:model.live="department_id"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >
                                    <option value="">Select department</option>
                                    @foreach ($this->departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>

                                @error('department_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Designation
                                </label>

                                <select
                                    wire:model.live="designation_id"
                                    @disabled(! $department_id)
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10 disabled:cursor-not-allowed disabled:bg-zinc-100"
                                >
                                    <option value="">
                                        {{ $department_id ? 'Select designation' : 'Select department first' }}
                                    </option>

                                    @foreach ($this->designations as $designation)
                                        <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                    @endforeach
                                </select>

                                @error('designation_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Employee
                                </label>

                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="search"
                                    @disabled(! $designation_id)
                                    placeholder="{{ $designation_id ? 'Search employee by name, email, or phone' : 'Select designation first' }}"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10 disabled:cursor-not-allowed disabled:bg-zinc-100"
                                >

                                @error('employee_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                @if ($designation_id && $search !== '' && ! $this->selectedEmployee)
                                    <div class="mt-3 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
                                        @forelse ($this->employees as $employee)
                                            <button
                                                type="button"
                                                wire:click="selectEmployee({{ $employee->id }})"
                                                class="flex w-full items-center justify-between px-4 py-3 text-left transition hover:bg-zinc-50"
                                            >
                                                <div>
                                                    <p class="text-sm font-medium text-zinc-900">{{ $employee->name }}</p>
                                                    <p class="text-xs text-zinc-500">{{ $employee->email }} · {{ $employee->phone }}</p>
                                                </div>
                                            </button>
                                        @empty
                                            <div class="px-4 py-3 text-sm text-zinc-500">
                                                No matching employees found.
                                            </div>
                                        @endforelse
                                    </div>
                                @endif

                                @if ($this->selectedEmployee)
                                    <div class="mt-3 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
                                        <p class="text-sm font-semibold text-zinc-900">{{ $this->selectedEmployee->name }}</p>
                                        <p class="mt-1 text-sm text-zinc-500">
                                            {{ $this->selectedEmployee->email }} · {{ $this->selectedEmployee->phone }}
                                        </p>
                                        <button
                                            type="button"
                                            wire:click="$set('employee_id', null); $set('search', '')"
                                            class="mt-3 text-sm font-medium text-red-600 hover:text-red-700"
                                        >
                                            Remove selection
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Start Date
                                </label>

                                <input
                                    type="date"
                                    wire:model="start_date"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    End Date
                                </label>

                                <input
                                    type="date"
                                    wire:model="end_date"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('end_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Rate Type
                                </label>

                                <select
                                    wire:model="rate_type"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >
                                    <option value="">Select rate type</option>
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                </select>

                                @error('rate_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Rate
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="rate"
                                    placeholder="Enter contract rate"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('rate')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>
                </flux:card>
            </div>

            <div class="xl:col-span-4">
                <div class="space-y-6">
                    <flux:card class="border border-zinc-200 shadow-sm">
                        <div class="border-b border-zinc-200 px-5 py-4">
                            <flux:heading size="lg">Record Info</flux:heading>
                        </div>

                        <div class="space-y-4 px-5 py-5">
                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Status
                                </p>
                                <div class="mt-2">
                                    <flux:badge color="blue">New Contract</flux:badge>
                                </div>
                            </div>

                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Employee Assignment
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    {{ $employee_id ? 'Selected' : 'Optional / Pending' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Rate Type
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    {{ $rate_type ? ucfirst($rate_type) : 'Pending' }}
                                </p>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>

            <div class="xl:col-span-12">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="flex flex-col gap-3 px-5 py-4 sm:px-6 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-zinc-900">
                                Save this contract
                            </p>
                            <p class="text-sm text-zinc-500">
                                Review the information before creating the contract record.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('contracts.index') }}" wire:navigate>
                                <flux:button variant="ghost">
                                    Cancel
                                </flux:button>
                            </a>

                            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">Create Contract</span>
                                <span wire:loading wire:target="save">Saving...</span>
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            </div>
        </form>
    </div>
</div>