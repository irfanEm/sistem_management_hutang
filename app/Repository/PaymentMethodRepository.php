<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Domain\PaymentMethod;
use PDO;
use PDOException;

class PaymentMethodRepository
{
    private PDO $connection;
    private const TABLE_NAME = 'payment_methods';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    private function mapRowToData(array $row): PaymentMethod
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->id = $row['id'];
        $paymentMethod->kode_metode = $row['kode_metode'];
        $paymentMethod->nama_metode = $row['nama_metode'];
        $paymentMethod->created_at = $row['created_at'];
        $paymentMethod->updated_at = $row['updated_at'];

        return $paymentMethod;
    }

    public function save(PaymentMethod $paymentMethod): PaymentMethod
    {
        try {
            if ($paymentMethod->id === 0) {
                // Insert new
                $statement = $this->connection->prepare("
                    INSERT INTO ".self::TABLE_NAME." 
                    (kode_metode, nama_metode, created_at, updated_at) 
                    VALUES (?, ?, ?, ?)
                ");

                $statement->execute([
                    $paymentMethod->kode_metode,
                    $paymentMethod->nama_metode,
                    $paymentMethod->created_at,
                    $paymentMethod->updated_at
                ]);

                $paymentMethod->id = $this->connection->lastInsertId();
            } else {
                // Update existing
                $statement = $this->connection->prepare("
                    UPDATE ".self::TABLE_NAME." 
                    SET kode_metode = ?,
                        nama_metode = ?,
                        updated_at = ?
                    WHERE id = ?
                ");

                $paymentMethod->updated_at = date('Y-m-d H:i:s');
                
                $statement->execute([
                    $paymentMethod->kode_metode,
                    $paymentMethod->nama_metode,
                    $paymentMethod->updated_at,
                    $paymentMethod->id
                ]);
            }

            return $paymentMethod;
        } catch (PDOException $err) {
            error_log("Error PaymentMethodRepository::save: " . $err->getMessage());
            throw $err;
        }
    }

    public function deleteById(int $id): bool
    {
        try {
            $statement = $this->connection->prepare("
                DELETE FROM ".self::TABLE_NAME." 
                WHERE id = ?
            ");
            return $statement->execute([$id]);
        } catch (PDOException $err) {
            error_log("Error PaymentMethodRepository::deleteById: " . $err->getMessage());
            return false;
        }
    }

    public function findById(int $id): ?PaymentMethod
    {
        try {
            $statement = $this->connection->prepare("
                SELECT * FROM ".self::TABLE_NAME." 
                WHERE id = ?
            ");
            $statement->execute([$id]);
            
            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }
            
            return null;
        } catch (PDOException $err) {
            error_log("Error PaymentMethodRepository::findById: " . $err->getMessage());
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
            
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            
            $result = [];
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $this->mapRowToData($row);
            }
            
            return $result;
        } catch (PDOException $err) {
            error_log("Error PaymentMethodRepository::findAll: " . $err->getMessage());
            return [];
        }
    }

    public function deleteAllPermanently(): bool
    {
        try {
            $statement = $this->connection->prepare("
                DELETE FROM ".self::TABLE_NAME." 
                WHERE 1
            ");
            return $statement->execute();
        } catch (PDOException $err) {
            error_log("Error PaymentMethodRepository::deleteAllPermanently: " . $err->getMessage());
            return false;
        }
    }
}