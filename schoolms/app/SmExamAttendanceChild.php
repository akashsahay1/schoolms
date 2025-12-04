<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmExamAttendanceChild extends Model
{
    use HasFactory;
    public function studentInfo()
    {
        return $this->belongsTo('App\SmStudent', 'student_id', 'id')->with('class', 'section');
    }
}
