<?php

namespace IRFANM\SIASHAF\Factory;

use IRFANM\SIASHAF\Domain\Teacher;

class TeacherFactory
{
    public static function createTeacher(string $user_id, string $teacher_code, string $first_name, string $last_name, string $email, string $phone, string $address, string $date_of_birth, string $hire_date, string $department, ?string $status = 'active'): Teacher
    {
        $teacher = new Teacher();
        $teacher->user_id = $user_id;
        $teacher->teacher_code = $teacher_code;
        $teacher->first_name = $first_name;
        $teacher->last_name = $last_name;
        $teacher->email = $email;
        $teacher->phone = $phone;
        $teacher->address = $address;
        $teacher->date_of_birth = $date_of_birth;
        $teacher->hire_date = $hire_date;
        $teacher->department = $department;
        $teacher->status = $status;
        $teacher->created_at = date("Y-m-d H:i:s");
        $teacher->updated_at = date("Y-m-d H:i:s");
        return $teacher;
    }
}

