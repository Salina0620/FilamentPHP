<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    // Define the table if it's not the plural form of the model name
    protected $table = 'shifts';

    // Add fillable properties if needed
    protected $fillable = [
        'doctor_id',
        'shift_type',
        'date',
        'start_time',
        'end_time',
    ];

    // Define relationships if needed
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }


    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
