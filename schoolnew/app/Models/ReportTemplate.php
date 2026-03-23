<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'data_source',
        'columns',
        'filters',
        'sort',
        'created_by',
        'is_public',
    ];

    protected $casts = [
        'columns' => 'array',
        'filters' => 'array',
        'sort' => 'array',
        'is_public' => 'boolean',
    ];

    /**
     * Get the user who created this template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get available data sources
     */
    public static function getDataSources(): array
    {
        return [
            'students' => 'Students',
            'staff' => 'Staff',
            'attendance' => 'Student Attendance',
            'staff_attendance' => 'Staff Attendance',
            'fees' => 'Fee Collections',
            'library' => 'Library Issues',
            'transport' => 'Transport',
        ];
    }

    /**
     * Get available columns for each data source
     */
    public static function getAvailableColumns(string $dataSource): array
    {
        $columns = [
            'students' => [
                'admission_no' => 'Admission Number',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'full_name' => 'Full Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'gender' => 'Gender',
                'date_of_birth' => 'Date of Birth',
                'blood_group' => 'Blood Group',
                'religion' => 'Religion',
                'nationality' => 'Nationality',
                'address' => 'Address',
                'city' => 'City',
                'state' => 'State',
                'class_name' => 'Class',
                'section_name' => 'Section',
                'academic_year' => 'Academic Year',
                'admission_date' => 'Admission Date',
                'status' => 'Status',
                'parent_name' => 'Parent Name',
                'parent_phone' => 'Parent Phone',
                'parent_email' => 'Parent Email',
                'created_at' => 'Created Date',
            ],
            'staff' => [
                'employee_id' => 'Employee ID',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'full_name' => 'Full Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'gender' => 'Gender',
                'date_of_birth' => 'Date of Birth',
                'joining_date' => 'Joining Date',
                'department_name' => 'Department',
                'designation_name' => 'Designation',
                'qualification' => 'Qualification',
                'experience' => 'Experience',
                'salary' => 'Salary',
                'address' => 'Address',
                'city' => 'City',
                'state' => 'State',
                'status' => 'Status',
                'created_at' => 'Created Date',
            ],
            'attendance' => [
                'student_admission_no' => 'Admission Number',
                'student_name' => 'Student Name',
                'class_name' => 'Class',
                'section_name' => 'Section',
                'date' => 'Date',
                'status' => 'Status',
                'remarks' => 'Remarks',
            ],
            'staff_attendance' => [
                'staff_employee_id' => 'Employee ID',
                'staff_name' => 'Staff Name',
                'department_name' => 'Department',
                'date' => 'Date',
                'check_in' => 'Check In',
                'check_out' => 'Check Out',
                'status' => 'Status',
                'remarks' => 'Remarks',
            ],
            'fees' => [
                'receipt_no' => 'Receipt Number',
                'student_admission_no' => 'Admission Number',
                'student_name' => 'Student Name',
                'class_name' => 'Class',
                'section_name' => 'Section',
                'fee_type' => 'Fee Type',
                'amount' => 'Amount',
                'discount' => 'Discount',
                'fine' => 'Fine',
                'paid_amount' => 'Paid Amount',
                'payment_method' => 'Payment Method',
                'payment_date' => 'Payment Date',
                'status' => 'Status',
            ],
            'library' => [
                'book_title' => 'Book Title',
                'book_isbn' => 'ISBN',
                'book_category' => 'Category',
                'member_name' => 'Member Name',
                'member_type' => 'Member Type',
                'issue_date' => 'Issue Date',
                'due_date' => 'Due Date',
                'return_date' => 'Return Date',
                'fine_amount' => 'Fine Amount',
                'status' => 'Status',
            ],
            'transport' => [
                'student_admission_no' => 'Admission Number',
                'student_name' => 'Student Name',
                'class_name' => 'Class',
                'route_name' => 'Route Name',
                'vehicle_number' => 'Vehicle Number',
                'driver_name' => 'Driver Name',
                'pickup_point' => 'Pickup Point',
                'transport_fee' => 'Transport Fee',
            ],
        ];

        return $columns[$dataSource] ?? [];
    }

    /**
     * Get available filters for each data source
     */
    public static function getAvailableFilters(string $dataSource): array
    {
        $filters = [
            'students' => [
                'class_id' => ['label' => 'Class', 'type' => 'select', 'model' => 'SchoolClass'],
                'section_id' => ['label' => 'Section', 'type' => 'select', 'model' => 'Section'],
                'academic_year_id' => ['label' => 'Academic Year', 'type' => 'select', 'model' => 'AcademicYear'],
                'gender' => ['label' => 'Gender', 'type' => 'select', 'options' => ['male' => 'Male', 'female' => 'Female']],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive', 'alumni' => 'Alumni']],
                'admission_date_from' => ['label' => 'Admission From', 'type' => 'date'],
                'admission_date_to' => ['label' => 'Admission To', 'type' => 'date'],
            ],
            'staff' => [
                'department_id' => ['label' => 'Department', 'type' => 'select', 'model' => 'Department'],
                'designation_id' => ['label' => 'Designation', 'type' => 'select', 'model' => 'Designation'],
                'gender' => ['label' => 'Gender', 'type' => 'select', 'options' => ['male' => 'Male', 'female' => 'Female']],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
                'joining_date_from' => ['label' => 'Joining From', 'type' => 'date'],
                'joining_date_to' => ['label' => 'Joining To', 'type' => 'date'],
            ],
            'attendance' => [
                'class_id' => ['label' => 'Class', 'type' => 'select', 'model' => 'SchoolClass'],
                'section_id' => ['label' => 'Section', 'type' => 'select', 'model' => 'Section'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'half_day' => 'Half Day']],
                'date_from' => ['label' => 'Date From', 'type' => 'date'],
                'date_to' => ['label' => 'Date To', 'type' => 'date'],
            ],
            'staff_attendance' => [
                'department_id' => ['label' => 'Department', 'type' => 'select', 'model' => 'Department'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'half_day' => 'Half Day']],
                'date_from' => ['label' => 'Date From', 'type' => 'date'],
                'date_to' => ['label' => 'Date To', 'type' => 'date'],
            ],
            'fees' => [
                'class_id' => ['label' => 'Class', 'type' => 'select', 'model' => 'SchoolClass'],
                'section_id' => ['label' => 'Section', 'type' => 'select', 'model' => 'Section'],
                'fee_type_id' => ['label' => 'Fee Type', 'type' => 'select', 'model' => 'FeeType'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['paid' => 'Paid', 'partial' => 'Partial', 'unpaid' => 'Unpaid']],
                'payment_method' => ['label' => 'Payment Method', 'type' => 'select', 'options' => ['cash' => 'Cash', 'card' => 'Card', 'bank' => 'Bank Transfer', 'online' => 'Online']],
                'date_from' => ['label' => 'Date From', 'type' => 'date'],
                'date_to' => ['label' => 'Date To', 'type' => 'date'],
            ],
            'library' => [
                'category_id' => ['label' => 'Category', 'type' => 'select', 'model' => 'BookCategory'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['issued' => 'Issued', 'returned' => 'Returned', 'overdue' => 'Overdue']],
                'date_from' => ['label' => 'Issue Date From', 'type' => 'date'],
                'date_to' => ['label' => 'Issue Date To', 'type' => 'date'],
            ],
            'transport' => [
                'class_id' => ['label' => 'Class', 'type' => 'select', 'model' => 'SchoolClass'],
                'section_id' => ['label' => 'Section', 'type' => 'select', 'model' => 'Section'],
                'route_id' => ['label' => 'Route', 'type' => 'select', 'model' => 'TransportRoute'],
                'vehicle_id' => ['label' => 'Vehicle', 'type' => 'select', 'model' => 'Vehicle'],
            ],
        ];

        return $filters[$dataSource] ?? [];
    }
}
