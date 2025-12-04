<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Modules\ParentRegistration\Entities\SmStudentRegistration;

class SmsTemplate extends Model
{
    use HasFactory;
    protected $table = "sms_templates";

    public static function smsTempleteToBody($body, $data)
    {
        $user = null;
        if (@$data['user_email']) {
            $user = User::where('email', $data['user_email'])->first();
        }

        if($user->role_id == 2){
            $body = str_replace('[student_name]', @$user->full_name, $body);
        }elseif($user->role_id == 3){
            $body = str_replace('[student_name]', @$data['student_info']->first_name, $body);
            $body = str_replace('[parent_name]', @$user->full_name, $body);
        }else{
            $body = str_replace('[name]', @$user->full_name, $body);
            $body = str_replace('[attendance_date]', @$data['attendance_date'], $body);
            $body = str_replace('[password]', '123456', $body);
        }
        
        if (@$data['slug'] == 'student') {
            $student_info = SmStudent::find(@$data['id']);
            $body = str_replace('[student_name]', @$student_info->full_name, $body);
            $body = str_replace('[user_name]', @$user->username . '/' . @$user->email, $body);

        } elseif (@$data['slug'] == 'parent') {
            $parent_info = SmParent::find(@$data['id']);
            $student_info = SmStudent::where('parent_id', @$parent_info->id)->first();
            $body = str_replace('[parent_name]', @$parent_info->guardians_name, $body);
            $body = str_replace('[student_name]', @$student_info->full_name, $body);
            $body = str_replace('[user_name]', @$user->username, $body);
        } else {
            $body = str_replace('[user_name]', @$user->username, $body);
        }

        $body = str_replace('[title]', @$data['title'], $body);
        $body = str_replace('[description]', @$data['description'], $body);

        return $body;
    }

    public static function emailTempleteToBody($body, $data)
    {
       $user = null;
        if (@$data['user_email']) {
            $user = User::where('email', $data['user_email'])->first();
            $school = SmSchool::find($user->school_id);

            $body = str_replace('[name]', @$user->full_name, $body);
            $body = str_replace('[email]', @$user->email, $body);
            $body = str_replace('[school_name]', @$school->school_name, $body);
        }

        $body = str_replace('[user_name]', @$data['user_name'], $body);

        //Global Variable End

        //Password Reset Start
        $body = str_replace('[admission_number]', @$data['admission_number'], $body);
        $reset_link = url('reset/password' . '/' . @$data['user_email'] . '/' . @$data['random']);

        $body = str_replace('[reset_link]', @$reset_link, $body);
        //Password Reset End

        // FrontEnd Contact Start
        $body = str_replace('[contact_name]', @$data['contact_name'], $body);
        $body = str_replace('[contact_email]', @$data['contact_email'], $body);
        $body = str_replace('[contact_subject]', @$data['subject'], $body);
        $body = str_replace('[contact_message]', @$data['contact_message'], $body);
        // FrontEnd Contact End

        // Login Information Start
        $body = str_replace('[password]', '123456', $body);

        $body = str_replace('[title]', @$data['title'], $body);
        $body = str_replace('[description]', @$data['description'], $body);

        if ($data['slug'] == 'student') {
            $student_info = SmStudent::find(@$data['id']);
            $parent_info = SmParent::find(@$student_info->parent_id);

            $body = str_replace('[student_name]', @$student_info->full_name, $body);
            $body = str_replace('[father_name]', @$parent_info->fathers_name, $body);
            $body = str_replace('[username]', @$user->username . '/' . @$user->email, $body);
            $body = str_replace('[admission_number]', @$student_info->admission_no, $body);
        } elseif ($data['slug'] == 'parent') {
            $parent_info = SmParent::find(@$data['id']);
            $student_info = SmStudent::where('parent_id', @$parent_info->id)->first();

            $body = str_replace('[name]', @$parent_info->guardians_name, $body);
            $body = str_replace('[student_name]', @$student_info->full_name, $body);
            $body = str_replace('[username]', @$user->username, $body);
            $body = str_replace('[email]', @$user->email, $body);
            $body = str_replace('[father_name]', @$student_info->father_name, $body);
            $body = str_replace('[admission_number]', @$student_info->admission_no, $body);
        } else {
            $body = str_replace('[username]', @$user->username, $body);
        }
        // Login Information End

        //Bank Reject Payment Start
        $body = str_replace('[student_name]', $data['student_name'], $body);
        $body = str_replace('[parent_name]', $data['parent_name'], $body);
        $body = str_replace('[note]', $data['note'], $body);
        $body = str_replace('[date]', $data['date'], $body);
        //Bank Reject Payment End

        //lead module 
        $body = str_replace('[assign_user]', $data['lead_assign_user'], $body);
        $body = str_replace('[lead_name]', $data['lead_name'], $body);
        $body = str_replace('[lead_email]', $data['lead_email'], $body);
        $body = str_replace('[lead_phone]', $data['lead_phone'], $body);
        $body = str_replace('[reminder_date]', $data['reminder_date'], $body);
        $body = str_replace('[reminder_time]', $data['reminder_time'], $body);
        $body = str_replace('[reminder_description]', $data['reminder_description'], $body);
        //end module

        // Wallet Start
        $body = str_replace('[add_balance]', generalSetting()->currency_symbol . number_format(@$data['add_balance'], 2, '.', ''), $body);
        $body = str_replace('[method]', @$data['method'], $body);
        $body = str_replace('[create_date]', dateConvert(@$data['create_date']), $body);
        $body = str_replace('[current_balance]', generalSetting()->currency_symbol . number_format(@$data['current_balance'], 2, '.', ''), $body);
        $body = str_replace('[reject_reason]', @$data['reject_reason'], $body);
        $body = str_replace('[previous_balance]', @$data['previous_balance'], $body);
        $body = str_replace('[refund_amount]', generalSetting()->currency_symbol . number_format(@$data['refund_amount'], 2, '.', ''), $body);
        // Wallet End

        return $body;
    }
}
