<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function scopeInCompany($query)
{
    return $query->whereHas('employee', function ($q) {
        $q->inCompany();
    });
}

    public function getDurationAttribute()
    {
        return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date));
    }

    public function scopeSearch($query, $term)
    {
        return $query->whereHas('employee', function ($q) use ($term) {
            $q->where('name', 'LIKE', "%$term%");
        });
    }

    public function getTotalEarnings($monthYear)
    {
        return $this->rate_type === 'monthly' ? $this->rate : $this->rate * Carbon::parse($monthYear)->daysInMonth;
    }

    
}
