<?php

use App\Models\Department;
use Livewire\Component;

new class extends Component
{
    public $departmentId;
    public $name = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function mount($id)
    {
        $department = Department::inCompany()->findOrFail($id);

        $this->departmentId = $department->id;
        $this->name = $department->name;
    }

    public function save()
    {
        $this->validate();

        $department = Department::inCompany()->findOrFail($this->departmentId);

        $department->update([
            'name' => $this->name,
        ]);

        $this->dispatch('department-updated', message: 'Department updated successfully.');
    }

    public function getDepartmentProperty()
    {
        return Department::inCompany()->findOrFail($this->departmentId);
    }
};
?>
<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Edit Department
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Update the department details for the selected company.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('departments.index') }}" wire:navigate>
                    <flux:button variant="ghost">
                        Back to Departments
                    </flux:button>
                </a>
            </div>
        </div>

        <flux:separator />

        <div
            x-data="{ show: false, message: '' }"
            x-on:department-updated.window="
                message = $event.detail.message;
                show = true;
                setTimeout(() => show = false, 3000);
            "
        >
            <div
                x-show="show"
                x-transition
                class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
                style="display:none;"
            >
                <span x-text="message"></span>
            </div>
        </div>

        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                        <flux:heading size="lg">Department Information</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            Edit the primary details for this department.
                        </flux:subheading>
                    </div>

                    <div class="px-5 py-5 sm:px-6">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Department Name
                                </label>

                                <input
                                    type="text"
                                    wire:model.blur="name"
                                    placeholder="Enter department name"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('name')
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
                                    Department ID
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    #{{ $this->department->id }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Status
                                </p>
                                <div class="mt-2">
                                    <flux:badge color="emerald">Ready to update</flux:badge>
                                </div>
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
                                Save your changes
                            </p>
                            <p class="text-sm text-zinc-500">
                                Make sure the department information is correct before updating.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('departments.index') }}" wire:navigate>
                                <flux:button variant="ghost">
                                    Cancel
                                </flux:button>
                            </a>

                            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">Update Department</span>
                                <span wire:loading wire:target="save">Saving...</span>
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            </div>
        </form>
    </div>
</div>