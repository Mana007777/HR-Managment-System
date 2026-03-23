<?php

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    public function delete($id)
    {
        $company = Company::find($id);

        if (! $company) {
            return;
        }

        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        session()->flash('message', 'Company deleted successfully.');
    }

    public function getCompaniesProperty()
    {
        return Company::latest()->paginate(10);
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-5">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Companies
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Manage and review all registered companies from one clean workspace.
                </flux:subheading>
            </div>

            <div class="flex shrink-0 items-center gap-3">
                <div class="hidden rounded-xl border border-zinc-200 bg-white px-4 py-2.5 shadow-sm lg:flex lg:flex-col">
                    <span class="text-[11px] font-medium uppercase tracking-wide text-zinc-500">
                        Total : 
                    </span>
                    <span class="text-base font-semibold text-zinc-900">
                        {{ $this->companies->total() }}  companies
                    </span>
                </div>
            </div>
        </div>

        <flux:separator />

        @if (session('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('message') }}
        </div>
        @endif

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <flux:card class="border border-zinc-200 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-500">Total companies</p>
                        <p class="mt-1 text-2xl font-semibold tracking-tight text-zinc-900">
                            {{ $this->companies->total() }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 10h.01M15 10h.01M9 14h.01M15 14h.01" />
                        </svg>
                    </div>
                </div>
            </flux:card>

            <flux:card class="border border-zinc-200 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-500">Shown on this page</p>
                        <p class="mt-1 text-2xl font-semibold tracking-tight text-zinc-900">
                            {{ $this->companies->count() }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10" />
                        </svg>
                    </div>
                </div>
            </flux:card>

            <flux:card class="border border-zinc-200 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-500">Data state</p>
                        <p class="mt-1 text-sm font-semibold text-zinc-900">
                            Sorted by newest first
                        </p>
                    </div>

                    <flux:badge color="emerald">Live</flux:badge>
                </div>
            </flux:card>
        </div>

        <flux:card class="overflow-hidden border border-zinc-200 shadow-sm">
            <div class="flex flex-col gap-3 border-b border-zinc-200 px-5 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0">
                    <flux:heading size="lg" class="text-zinc-900">
                        Company Directory
                    </flux:heading>

                    <flux:subheading class="mt-1 text-zinc-500">
                        Browse all companies and manage records quickly.
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-2">
                    <flux:badge color="zinc">
                        {{ $this->companies->total() }} records
                    </flux:badge>
                </div>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full table-fixed border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-5 text-xs font-semibold uppercase tracking-wide text-zinc-500 w-[30%]">
                                Company
                            </th>
                            <th class="px-6 py-5 text-xs font-semibold uppercase tracking-wide text-zinc-500 w-[25%]">
                                Email
                            </th>
                            <th class="px-6 py-5 text-xs font-semibold uppercase tracking-wide text-zinc-500 w-[15%]">
                                Phone
                            </th>
                            <th class="px-6 py-5 text-xs font-semibold uppercase tracking-wide text-zinc-500 w-[18%]">
                                Address
                            </th>
                            <th class="px-6 py-5 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 w-[12%]">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($this->companies as $company)
                        <tr class="bg-white shadow-sm hover:shadow-md transition rounded-xl">

                            <td class="px-6 py-8 align-middle">
                                <div class="flex items-center gap-4">
                                    @if ($company->logo)
                                    <img
                                        src="{{ asset('storage/' . $company->logo) }}"
                                        class="h-12 w-12 rounded-xl border border-zinc-200 object-cover">
                                    @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-900 text-white font-semibold">
                                        {{ strtoupper(substr($company->name, 0, 1)) }}
                                    </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="text-[15px] font-semibold text-zinc-900 truncate">
                                            {{ $company->name }}
                                        </div>
                                        <div class="text-xs text-zinc-500 mt-1">
                                            ID: {{ $company->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5 text-sm text-zinc-700">
                                <div class="truncate">
                                    {{ $company->email ?: '—' }}
                                </div>
                            </td>

                            <td class="px-6 py-5 text-sm text-zinc-700">
                                {{ $company->phone ?: '—' }}
                            </td>

                            <td class="px-6 py-5 text-sm text-zinc-700">
                                <div class="truncate">
                                    {{ $company->address ?: '—' }}
                                </div>
                            </td>

                            <td class="px-6 py-5">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="{{ route('companies.edit', $company->id) }}" wire:navigate>
                                        <flux:button variant="ghost" size="sm" class="px-3">
                                            Edit
                                        </flux:button>
                                    </a>

                                    <flux:button
                                        variant="danger"
                                        size="sm"
                                        class="px-4"
                                        wire:click="delete({{ $company->id }})"
                                        wire:confirm="Are you sure?">
                                        Delete
                                    </flux:button>
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-zinc-500">
                                No companies found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->companies->hasPages())
            <div class="border-t border-zinc-200 px-5 py-4 sm:px-6">
                {{ $this->companies->links() }}
            </div>
            @endif
        </flux:card>
    </div>
</div>