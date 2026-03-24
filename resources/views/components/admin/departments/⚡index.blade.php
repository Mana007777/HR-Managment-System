<?php

use App\Models\Department;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    public function delete($id)
    {
        $department = Department::find($id);

        if (! $department) {
            session()->flash('error', 'Department not found.');
            return;
        }

        $department->delete();

        session()->flash('success', 'Department deleted successfully.');
    }

    public function getDepartmentsProperty()
    {
        return Department::inCompany()->paginate(5);
    }
};
?>
<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Departments
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Manage departments for the selected company.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('departments.create') }}" wire:navigate>
                    <flux:button variant="primary">
                        Create Department
                    </flux:button>
                </a>
            </div>
        </div>

        <flux:separator />

        {{-- Flash Messages --}}
        @if (session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            style="display: none;"
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            style="display: none;"
            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
        @endif

        {{-- Table --}}
        <flux:card class="overflow-hidden border border-zinc-200 shadow-sm">
            <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                <flux:heading size="lg">Departments List</flux:heading>
                <flux:subheading class="mt-1 text-zinc-500">
                    View, edit, and delete departments under the active company.
                </flux:subheading>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200">
                    <thead class="bg-zinc-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                ID
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                Department Name
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 bg-white">
                        @forelse ($this->departments as $department)
                        <tr class="hover:bg-zinc-50/70">
                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-zinc-900">
                                #{{ $department->id }}
                            </td>

                            <td class="px-5 py-4 text-sm text-zinc-700">
                                {{ $department->name }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('departments.edit', $department->id) }}" wire:navigate>
                                        <flux:button variant="ghost" size="sm">
                                            Edit
                                        </flux:button>
                                    </a>

                                    <flux:button
                                        variant="danger"
                                        size="sm"
                                        wire:click="delete({{ $department->id }})"
                                        wire:confirm="Are you sure you want to delete this department?">
                                        Delete
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-sm text-zinc-500">
                                No departments found for the selected company.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->departments->hasPages())
            <div class="border-t border-zinc-200 px-5 py-4">
                {{ $this->departments->links() }}
            </div>
            @endif
        </flux:card>
    </div>
</div>