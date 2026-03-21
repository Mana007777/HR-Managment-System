<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('d  ashboard', 'admin.dashboard')->name('dashboard');
    Route::prefix('companies')->group(function () {
        Route::livewire('/', 'admin.companies.index')->name('companies.index');
        Route::livewire('/create', 'admin.companies.create')->name('companies.create');
        Route::livewire('/{id}/edit', 'admin.companies.edit')->name('companies.edit');
    });

    Route::middleware('company.context')->group(function () {
        Route::prefix('departments')->group(function () {
            Route::livewire('/', 'admin.departments.index')->name('departments.index');
            Route::livewire('/create', 'admin.departments.create')->name('departments.create');
            Route::livewire('/{id}/edit', 'admin.departments.edit')->name('departments.edit');
        });
        Route::prefix('designations')->group(function () {
            Route::livewire('/', 'admin.designations.index')->name('designations.index');
            Route::livewire('/create', 'admin.designations.create')->name('designations.create');
            Route::livewire('/{id}/edit', 'admin.designations.edit')->name('designations.edit');
        });
        Route::prefix('employees')->group(function () {
            Route::livewire('/', 'admin.employees.index')->name('employees.index');
            Route::livewire('/create', 'admin.employees.create')->name('employees.create');
            Route::livewire('/{id}/edit', 'admin.employees.edit')->name('employees.edit');
        });
        Route::prefix('contracts')->group(function () {
            Route::livewire('/', 'admin.contracts.index')->name('contracts.index');
            Route::livewire('/create', 'admin.contracts.create')->name('contracts.create');
            Route::livewire('/{id}/edit', 'admin.contracts.edit')->name('contracts.edit');
        });
        Route::prefix('payroll')->group(function () {
            Route::livewire('/', 'admin.payroll.index')->name('payroll.index');
            Route::livewire('/{id}/show', 'admin.payroll.show')->name('payroll.show');
        });
    });
});

require __DIR__ . '/settings.php';
