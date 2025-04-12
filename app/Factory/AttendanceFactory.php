<?php

namespace IRFANM\SIASHAF\Factory;

use IRFANM\SIASHAF\Domain\Attendance;

class AttendanceFactory
{
    public static function createAttendance(string $attendance_id, string $user_id, string $class_id, string $date, ?string $status = null, ?string $remarks = null,)
    {
        $attendance = new Attendance();
        $attendance->attendance_id = $attendance_id;
        $attendance->user_id = $user_id;
        $attendance->class_id = $class_id;
        $attendance->date = $date;
        $attendance->status = $status;
        $attendance->remarks = $remarks;
        $attendance->created_at = date('Y-m-d H:i:s');
        $attendance->updated_at = date('Y-m-d H:i:s');
        $attendance->deleted_at = null;

        return $attendance;
    }
}