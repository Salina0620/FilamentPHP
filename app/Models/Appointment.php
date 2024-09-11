<?php

namespace App\Models;

// This namespace is used to group related classes and avoid name conflicts.

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    // Method create() or update() used in bulk
    protected $fillable = ['patient_id', 'doctor_id', 'department_id', 'date_time'];

    public function patient()
    {
        // Each appointment is associated with one patient
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        // Belongs to = many to one
        return $this->belongsTo(Doctor::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    // In App\Models\Appointment.php

public function canReschedule(): bool
{
    return auth()->check() && auth()->user()->id === $this->doctor->user_id;
}
public function schedule()
{
    return $this->belongsTo(Schedule::class);
}

}