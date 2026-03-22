<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'designation_id',
        'address',
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function department()
    {
        return $this->designation->department;
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function scopeInCompany($query)
    {
        return $query->whereHas('designation', function ($q) {
            $q->inCompany();
        });
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', "%{$term}%")
                     ->orWhere('email', 'LIKE', "%{$term}%")
                     ->orWhere('phone', 'LIKE', "%{$term}%");
    }

    public function getActiveContract($start_date = null, $end_date = null)
    {
        $start_date = $start_date ?? now();
        $end_date = $end_date ?? now();
        return $this->contracts()
                    ->where('start_date', '<=', $end_date)
                    ->where('end_date', '>=', $start_date)
                    ->first();
    }
}
