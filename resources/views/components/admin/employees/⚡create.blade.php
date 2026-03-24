<?php

use Livewire\Component;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public ?int $department_id = null;
    public ?int $designation_id = null;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'department_id' => 'required|integer',
            'designation_id' => 'required|integer',
        ];
    }

    public function updatedDepartmentId(): void
    {
        $this->designation_id = null;
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

        Employee::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'designation_id' => $designation->id,
        ]);

        session()->flash('success', 'Employee created successfully.');

        return $this->redirect(route('employees.index'), navigate: true);
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
};
?>
<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Create Employee
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Add a new employee to the selected company.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('employees.index') }}" wire:navigate>
                    <flux:button variant="ghost">
                        Back to Employees
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
                        <flux:heading size="lg">Employee Information</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            Enter the primary details for the new employee.
                        </flux:subheading>
                    </div>

                    <div class="px-5 py-5 sm:px-6">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Full Name
                                </label>

                                <input
                                    type="text"
                                    wire:model="name"
                                    placeholder="Enter employee name"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    wire:model="email"
                                    placeholder="Enter email address"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Phone Number
                                </label>

                                <input
                                    type="text"
                                    wire:model="phone"
                                    placeholder="Enter phone number"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

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
                                        <option value="{{ $department->id }}">
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('department_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Designation
                                </label>

                                <select
                                    wire:model="designation_id"
                                    @disabled(! $department_id)
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10 disabled:cursor-not-allowed disabled:bg-zinc-100"
                                >
                                    <option value="">
                                        {{ $department_id ? 'Select designation' : 'Select department first' }}
                                    </option>

                                    @foreach ($this->designations as $designation)
                                        <option value="{{ $designation->id }}">
                                            {{ $designation->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('designation_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Address
                                </label>

                                <textarea
                                    wire:model="address"
                                    rows="4"
                                    placeholder="Enter employee address"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                ></textarea>

                                @error('address')
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
                                    <flux:badge color="blue">New Employee</flux:badge>
                                </div>
                            </div>

                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Department Selection
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    {{ $department_id ? 'Selected' : 'Pending' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Designation Selection
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    {{ $designation_id ? 'Selected' : 'Pending' }}
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
                                Save this employee
                            </p>
                            <p class="text-sm text-zinc-500">
                                Review the information before creating the employee record.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('employees.index') }}" wire:navigate>
                                <flux:button variant="ghost">
                                    Cancel
                                </flux:button>
                            </a>

                            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">Create Employee</span>
                                <span wire:loading wire:target="save">Saving...</span>
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            </div>
        </form>
    </div>
</div>