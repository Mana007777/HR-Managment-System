<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
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
        return $query->where('company_id',$this->company_id);
    }

    public function getMonthYearAttribute()
    {
        return $this->year . '-' . $this->month;
    }

    public function getMonthStringAttribute()
    {
        return Carbon::parse($this->month_year)->format('F Y');
    }
}
