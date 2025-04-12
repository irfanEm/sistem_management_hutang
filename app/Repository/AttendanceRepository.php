<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Domain\Attendance;
use IRFANM\SIASHAF\Helper\SoftDeletes;
use PDO;
use PDOException;

class AttendanceRepository
{
    use SoftDeletes;
    private \PDO $connection;
    private const TABLE_NAME = 'attendances';

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    private function mapRowToData(array $row): Attendance
    {
        $attendance =  new Attendance();

        $attendance->attendance_id = $row['attendance_id'];
        $attendance->user_id = $row['user_id'];
        $attendance->class_id = $row['class_id'];
        $attendance->date = $row['date'];
        $attendance->status = $row['status'];
        $attendance->remarks = $row['remarks'];
        $attendance->created_at = $row['created_at'];
        $attendance->updated_at = $row['updated_at'];
        $attendance->deleted_at = $row['deleted_at'] ?? null;
    
        return $attendance;
    }

    public function getAll(string $condition = '', array $params = []): array
    {
        try {
            $query = "SELECT * FROM " . self::TABLE_NAME;
            if ($condition) {
                $query .= " $condition";
            }

            $statement = $this->connection->prepare($query);
            $statement->execute($params);

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $err) {
            error_log("Query Error: $query");
            throw new \Exception("Error saat mengambil data attendances: " . $err->getMessage());
        }
    }

    public function save(Attendance $attendance): Attendance
    {
        try {
            $statement = $this->connection->prepare("
                INSERT INTO attendances (
                attendance_id, 
                user_id, 
                class_id, 
                date, 
                status, 
                remarks, 
                created_at, 
                updated_at, 
                deleted_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $attendance->attendance_id,
                $attendance->user_id,
                $attendance->class_id,
                $attendance->date,
                $attendance->status,
                $attendance->remarks,
                $attendance->created_at,
                $attendance->updated_at,
                $attendance->deleted_at,
            ]);

            return $attendance;
        } catch (PDOException $err) {
            error_log("Error saat menyimpan attendances: " . $err->getMessage());
            return $attendance;
        }
    }

    public function update(Attendance $attendance): Attendance
    {
        try {
            $statement = $this->connection->prepare("
                UPDATE attendances 
                SET status = ?, remarks = ?, updated_at = ?
                WHERE attendance_id = ?
            ");

            $statement->execute([
                $attendance->status,
                $attendance->remarks,
                $attendance->updated_at,
                $attendance->attendance_id,
            ]);

            return $attendance;
        } catch (PDOException $err) {
            error_log("Error saat memperbarui attendances: " . $err->getMessage());
            return $attendance;
        }
    }

    public function findByMemoryId(string $attendance_id): ?Attendance
    {
        try {
            $statement = $this->connection->prepare("SELECT * FROM attendances WHERE attendance_id = ?");
            $statement->execute([$attendance_id]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }

            return null;
        } catch (PDOException $err) {
            error_log("Error saat mencari attendances: " . $err->getMessage());
            return null;
        }
    }

    public function deletePermanently(string $memory_id): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM attendances WHERE memory_id = ?");
            return $statement->execute([$memory_id]);
        } catch (PDOException $err) {
            error_log("Error saat menghapus data attendances: " . $err->getMessage());
            return false;
        }
    }

    public function deleteAllPermanently(): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM attendances WHERE 1");
            return $statement->execute();
        } catch (PDOException $err) {
            error_log("Error saat menghapus semua data secara permanen: " . $err->getMessage());
            return false;
        }
    }
}
