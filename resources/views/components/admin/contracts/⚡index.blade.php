<?php

use App\Models\Contract;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete($id): void
    {
        Contract::inCompany()
            ->whereKey($id)
            ->firstOrFail()
            ->delete();

        session()->flash('success', 'Contract deleted successfully.');

        if ($this->page > 1 && Contract::inCompany()->search($this->search)->paginate(10)->isEmpty()) {
            $this->previousPage();
        }
    }

    public function getContractsProperty()
    {
        return Contract::inCompany()
            ->search($this->search)
            ->latest()
            ->paginate(10);
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Contracts
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Manage contract records for the selected company.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('contracts.create') }}" wire:navigate>
                    <flux:button variant="primary">
                        Create Contract
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

        <flux:card class="border border-zinc-200 shadow-sm">
            <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <flux:heading size="lg">Contract List</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            View and manage all contracts assigned to the current company.
                        </flux:subheading>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="w-full sm:w-80">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Search contracts..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                            >
                        </div>

                        <div class="rounded-xl bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-700">
                            Total: {{ $this->contracts->total() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if ($this->contracts->count())
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    ID
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Employee
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Start Date
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    End Date
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Status
                                </th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @foreach ($this->contracts as $contract)
                                <tr wire:key="contract-{{ $contract->id }}">
                                    <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-zinc-900">
                                        #{{ $contract->id }}
                                    </td>

                                    <td class="px-5 py-4 text-sm text-zinc-700">
                                        {{ $contract->employee?->name ?? '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-500">
                                        {{ $contract->start_date ? \Illuminate\Support\Carbon::parse($contract->start_date)->format('M d, Y') : '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-500">
                                        {{ $contract->end_date ? \Illuminate\Support\Carbon::parse($contract->end_date)->format('M d, Y') : '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm">
                                        @php
                                            $isActive = $contract->start_date <= now()->toDateString() && $contract->end_date >= now()->toDateString();
                                        @endphp

                                        @if ($isActive)
                                            <flux:badge color="emerald">Active</flux:badge>
                                        @else
                                            <flux:badge color="zinc">Inactive</flux:badge>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('contracts.edit', $contract->id) }}" wire:navigate>
                                                <flux:button size="sm" variant="ghost">
                                                    Edit
                                                </flux:button>
                                            </a>

                                            <flux:button
                                                size="sm"
                                                variant="danger"
                                                wire:click="delete({{ $contract->id }})"
                                                wire:confirm="Are you sure you want to delete this contract?"
                                            >
                                                Delete
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <p class="mt-4 text-sm font-semibold text-zinc-900">No contracts found</p>
                        <p class="mt-1 text-sm text-zinc-500">
                            Create your first contract for this company.
                        </p>

                        <div class="mt-5">
                            <a href="{{ route('contracts.create') }}" wire:navigate>
                                <flux:button variant="primary">
                                    Create Contract
                                </flux:button>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if ($this->contracts->hasPages())
                <div class="border-t border-zinc-200 px-5 py-4 sm:px-6">
                    {{ $this->contracts->links() }}
                </div>
            @endif
        </flux:card>
    </div>
</div>