<?php

namespace IRFANM\SIMAHU\Repository;

use IRFANM\SIMAHU\Domain\Hutang;
use PDO;
use PDOException;

class HutangRepository
{
    private PDO $connection;
    private const TABLE_NAME = 'debts';

    public function __construct(PDO $conn)
    {
        $this->connection = $conn;
    }

    public function mapRowToData(array $row): Hutang
    {
        $hutang = new Hutang();
        $hutang->id = $row['id'];
        $hutang->debt_id = $row['debt_id'];
        $hutang->user_id = $row['user_id'];
        $hutang->agent_id = $row['agent_id'];
        $hutang->payment_method_id = $row['payment_method_id'];
        $hutang->tanggal_hutang = $row['tanggal_hutang'];
        $hutang->tanggal_jatuh_tempo = $row['tanggal_jatuh_tempo'];
        $hutang->sisa_hutang = $row['sisa_hutang']; // Ditambahkan
        $hutang->created_at = $row['created_at'];
        $hutang->updated_at = $row['updated_at'];

        return $hutang;
    }

    public function save(Hutang $hutang): Hutang
    {
        try{
            // Perbaikan syntax SQL
            $stmt = $this->connection->prepare("
                INSERT INTO ".self::TABLE_NAME." 
                (debt_id, user_id, agent_id, payment_method_id, tanggal_hutang, 
                tanggal_jatuh_tempo, sisa_hutang, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $hutang->debt_id,
                $hutang->user_id,
                $hutang->agent_id,
                $hutang->payment_method_id,
                $hutang->tanggal_hutang,
                $hutang->tanggal_jatuh_tempo,
                $hutang->sisa_hutang,
                $hutang->created_at,
                $hutang->updated_at
            ]);
            
            // Set ID terakhir
            $hutang->id = $this->connection->lastInsertId();
            
            return $hutang;

        } catch(PDOException $err) {
            error_log("Gagal menyimpan data hutang : " . $err->getMessage());
            return $hutang;
        }
    }

    public function update(Hutang $hutang): Hutang
    {
        try {
            $stmt = $this->connection->prepare("
                UPDATE ".self::TABLE_NAME." 
                SET user_id = ?,
                    agent_id = ?,
                    payment_method_id = ?,
                    tanggal_hutang = ?,
                    tanggal_jatuh_tempo = ?,
                    sisa_hutang = ?,
                    updated_at = ?
                WHERE debt_id = ?
            ");

            $stmt->execute([
                $hutang->user_id,
                $hutang->agent_id,
                $hutang->payment_method_id,
                $hutang->tanggal_hutang,
                $hutang->tanggal_jatuh_tempo,
                $hutang->sisa_hutang,
                $hutang->updated_at,
                $hutang->debt_id
            ]);

            return $hutang;
        } catch(PDOException $err) {
            error_log("Gagal update data hutang: " . $err->getMessage());
            return $hutang;
        }
    }

    public function deleteById(string $debt_id): bool
    {
        try {
            $stmt = $this->connection->prepare("
                DELETE FROM ".self::TABLE_NAME." 
                WHERE debt_id = ?
            ");
            return $stmt->execute([$debt_id]);
        } catch(PDOException $err) {
            error_log("Gagal menghapus data hutang: " . $err->getMessage());
            return false;
        }
    }

    public function findById(string $debt_id): ?Hutang
    {
        try {
            $stmt = $this->connection->prepare("
                SELECT * FROM ".self::TABLE_NAME." 
                WHERE debt_id = ?
            ");
            $stmt->execute([$debt_id]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }
            
            return null;
        } catch(PDOException $err) {
            error_log("Gagal mencari data hutang: " . $err->getMessage());
            return null;
        }
    }

    public function findAll(string $condition = "", array $params = []): array
    {
        try {
            $query = "SELECT * FROM ".self::TABLE_NAME;
            
            if (!empty($condition)) {
                $query .= " WHERE " . $condition;
            }
            
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            
            $result = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $this->mapRowToData($row);
            }
            
            // error_log("query : " . $query);
            return $result;
        } catch(PDOException $err) {
            error_log("Gagal mengambil data hutang: " . $err->getMessage());
            return [];
        }
    }

    public function deleteAllPermanently(): bool
    {
        try {
            $stmt = $this->connection->prepare("
                DELETE FROM ".self::TABLE_NAME." 
                WHERE 1
            ");
            return $stmt->execute();
        } catch(PDOException $err) {
            error_log("Gagal menghapus semua data hutang : " . $err->getMessage());
            return false;
        }
    }
}