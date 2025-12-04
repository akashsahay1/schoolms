<?php

namespace App;

use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmOnlineExam extends Model
{
    use HasFactory;
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new StatusAcademicSchoolScope);
    }
 
    
    public function studentInfo()
    {
        return $this->belongsTo('App\SmStudent', 'id', 'student_id');
    }

    public function class()
    {
        return $this->belongsTo('App\SmClass', 'class_id', 'id');
    }

    public function section()
    {
        return $this->belongsTo('App\SmSection', 'section_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo('App\SmSubject', 'subject_id', 'id');
    }

    public function assignQuestions()
    {
        return $this->hasMany('App\SmOnlineExamQuestionAssign', 'online_exam_id', 'id');
    }

    public static function obtainedMarks($exam_id, $student_id)
    {

        try {
            $marks = SmStudentTakeOnlineExam::select('status', 'total_marks')->where('online_exam_id', $exam_id)->where('student_id', $student_id)->first();
            return $marks;
        } catch (\Exception $e) {
            $data = [];
            return $data;
        }
    }

}
