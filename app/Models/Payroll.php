<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'company_id',
        'month',
        'year',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeInCompany($query)
    {
        return $query->where('company_id', session('company_id'));
    }

    public function getMonthYearAttribute()
    {
        return $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT);
    }

    public function getMonthStringAttribute()
    {
        return Carbon::parse($this->month_year . '-01')->format('F Y');
    }
}