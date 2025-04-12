<?php

namespace IRFANM\SIASHAF\Factory;

use IRFANM\SIASHAF\Domain\Student;

class StudentFactory
{
    public static function createStudent(string $user_id, string $student_code, string $first_name, string $last_name, string $email, string $phone, string $address, string $date_of_birth, string $class_id, string $enrollment_date, ?string $status = 'active'): Student
    {
        $student = new Student();
        $student->user_id = $user_id;
        $student->student_code = $student_code;
        $student->first_name = $first_name;
        $student->last_name = $last_name;
        $student->email = $email;
        $student->phone = $phone;
        $student->address = $address;
        $student->date_of_birth = $date_of_birth;
        $student->class_id = $class_id;
        $student->enrollment_date = $enrollment_date;
        $student->status = $status;
        $student->created_at = date("Y-m-d H:i:s");
        $student->updated_at = date("Y-m-d H:i:s");
        $student->deleted_at = null;

        return $student;
    }
}
