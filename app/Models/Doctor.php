<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctors';

    protected $fillable=[
        'user_id', 'department_id', 'phone_no', 'bio', 'image'
    ];
    // protected $fillable =['user_id','position','gender','shift','image','experience','phone_number','department_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
    public function appointments(){
        return $this->hasMany(Appointment::class);
    }

    
}
