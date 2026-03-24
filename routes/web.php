<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'admin.dashboard')->name('dashboard');
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::livewire('/', 'admin.companies.index')->name('index');
        Route::livewire('/create', 'admin.companies.create')->name('create');
        Route::livewire('/{id}/edit', 'admin.companies.edit')->name('edit');

        Route::get('/{company}/select', function (\App\Models\Company $company) {
            abort_unless(auth()->user()->companies->contains($company->id), 403);

            session(['company_id' => $company->id]);

            return redirect()->back();
        })->name('select');
    });

    Route::middleware('company.context')->group(function () {
        Route::prefix('departments')->name('departments.')->group(function () {
            Route::livewire('/', 'admin.departments.index')->name('index');
            Route::livewire('/create', 'admin.departments.create')->name('create');
            Route::livewire('/{id}/edit', 'admin.departments.edit')->name('edit');
        });

        Route::prefix('designations')->name('designations.')->group(function () {
            Route::livewire('/', 'admin.designations.index')->name('index');
            Route::livewire('/create', 'admin.designations.create')->name('create');
            Route::livewire('/{id}/edit', 'admin.designations.edit')->name('edit');
        });

        Route::prefix('employees')->name('employees.')->group(function () {
            Route::livewire('/', 'admin.employees.index')->name('index');
            Route::livewire('/create', 'admin.employees.create')->name('create');
            Route::livewire('/{id}/edit', 'admin.employees.edit')->name('edit');
        });

        Route::prefix('contracts')->name('contracts.')->group(function () {
            Route::livewire('/', 'admin.contracts.index')->name('index');
            Route::livewire('/create', 'admin.contracts.create')->name('create');
            Route::livewire('/{id}/edit', 'admin.contracts.edit')->name('edit');
        });

        Route::prefix('payroll')->name('payrolls.')->group(function () {
            Route::livewire('/', 'admin.payroll.index')->name('index');
            Route::livewire('/{id}/show', 'admin.payroll.show')->name('show');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::livewire('/', 'admin.payments.index')->name('index');
            Route::livewire('/{id}/show', 'admin.payments.show')->name('show');
        });
    });
});

require __DIR__ . '/settings.php';
