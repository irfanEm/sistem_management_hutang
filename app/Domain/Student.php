<?php

namespace IRFANM\SIASHAF\Domain;

class Student
{
    public ?int $id = null;
    public string $user_id;
    public string $student_code;
    public string $first_name;
    public string $last_name;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $address = null;
    public ?string $date_of_birth = null;
    public string $class_id;
    public string $enrollment_date;
    public string $status = 'active';
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;
}