<?php

namespace IRFANM\SIASHAF\Domain;

class Teacher
{
    public string $user_id;
    public string $teacher_code;
    public string $first_name;
    public string $last_name;
    public string $email;
    public ?string $phone;
    public ?string $address;
    public ?string $date_of_birth;
    public ?string $hire_date;
    public ?string $department;
    public string $status;
    public string $created_at;
    public string $updated_at;
    public ?string $deleted_at = null;
}
