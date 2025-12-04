<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmSubjectAttendance extends Model
{
    use HasFactory;
    public function student()
    {
        return $this->belongsTo('App\SmStudent', 'student_id', 'id');
    }
}
