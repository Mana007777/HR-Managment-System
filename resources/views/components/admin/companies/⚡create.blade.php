<?php

use App\Models\Company;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $name = '';
    public $email = '';
    public $website = '';
    public $logo;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,svg',
        ];
    }

    public function save()
    {
        $this->validate();

        $company = new Company();
        $company->name = $this->name;
        $company->email = $this->email;
        $company->website = $this->website;

        if ($this->logo) {
            $company->logo = $this->logo->store('logos', 'public');
        }

        $company->save();

        session()->flash('success', 'Company created successfully.');

        return redirect()->route('companies.index');
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Create Company
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Add a new company, upload branding, and keep your records organized.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('companies.index') }}" wire:navigate>
                    <flux:button variant="ghost">
                        Back to Companies
                    </flux:button>
                </a>
            </div>
        </div>

        <flux:separator />

        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            {{-- Main Form --}}
            <div class="xl:col-span-8">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                        <flux:heading size="lg">Company Information</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            Enter the primary details for the new company.
                        </flux:subheading>
                    </div>

                    <div class="px-5 py-5 sm:px-6">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            {{-- Name --}}
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Company Name
                                </label>
                                <input
                                    type="text"
                                    wire:model.blur="name"
                                    placeholder="Enter company name"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Email Address
                                </label>
                                <input
                                    type="email"
                                    wire:model.blur="email"
                                    placeholder="company@example.com"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Website --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Website
                                </label>
                                <input
                                    type="url"
                                    wire:model.blur="website"
                                    placeholder="https://example.com"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >
                                @error('website')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>
                </flux:card>
            </div>

            {{-- Side Panel --}}
            <div class="xl:col-span-4">
                <div class="space-y-6">

                    {{-- Logo Card --}}
                    <flux:card class="border border-zinc-200 shadow-sm">
                        <div class="border-b border-zinc-200 px-5 py-4">
                            <flux:heading size="lg">Company Logo</flux:heading>
                            <flux:subheading class="mt-1 text-zinc-500">
                                Upload a logo for the company.
                            </flux:subheading>
                        </div>

                        <div class="px-5 py-5">
                            <div class="flex flex-col items-center rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 px-4 py-6 text-center">

                                @if ($logo)
                                    <img
                                        src="{{ $logo->temporaryUrl() }}"
                                        alt="Logo preview"
                                        class="h-24 w-24 rounded-2xl border border-zinc-200 bg-white object-cover shadow-sm"
                                    >
                                    <p class="mt-4 text-sm font-medium text-zinc-900">Logo preview</p>
                                    <p class="mt-1 text-xs text-zinc-500">This image will be used as the company logo.</p>
                                @else
                                    <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-zinc-900 text-2xl font-semibold text-white shadow-sm">
                                        {{ strtoupper(substr($name ?: 'C', 0, 1)) }}
                                    </div>
                                    <p class="mt-4 text-sm font-medium text-zinc-900">No logo uploaded</p>
                                    <p class="mt-1 text-xs text-zinc-500">Add one to give the company a visual identity.</p>
                                @endif

                                <label class="mt-5 inline-flex cursor-pointer items-center rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50">
                                    <span>{{ $logo ? 'Change Logo' : 'Upload Logo' }}</span>
                                    <input type="file" wire:model="logo" class="hidden" accept=".jpg,.jpeg,.png,.svg">
                                </label>

                                <p class="mt-3 text-xs text-zinc-500">
                                    JPG, PNG, or SVG. Max 2MB.
                                </p>

                                @error('logo')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <div wire:loading wire:target="logo" class="mt-3 text-sm text-zinc-500">
                                    Uploading...
                                </div>
                            </div>
                        </div>
                    </flux:card>

                    {{-- Meta Card --}}
                    <flux:card class="border border-zinc-200 shadow-sm">
                        <div class="border-b border-zinc-200 px-5 py-4">
                            <flux:heading size="lg">Record Info</flux:heading>
                        </div>

                        <div class="space-y-4 px-5 py-5">
                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">Status</p>
                                <div class="mt-2">
                                    <flux:badge color="sky">New record</flux:badge>
                                </div>
                            </div>

                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">Logo</p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    {{ $logo ? 'Ready to upload' : 'Optional' }}
                                </p>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>

            {{-- Bottom Actions --}}
            <div class="xl:col-span-12">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="flex flex-col gap-3 px-5 py-4 sm:px-6 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-zinc-900">Create this company</p>
                            <p class="text-sm text-zinc-500">
                                Review the information before saving the new company record.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('companies.index') }}" wire:navigate>
                                <flux:button variant="ghost">
                                    Cancel
                                </flux:button>
                            </a>

                            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">Create Company</span>
                                <span wire:loading wire:target="save">Saving...</span>
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            </div>
        </form>
    </div>
</div>