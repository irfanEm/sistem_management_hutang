<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Domain\MasterHafalan;
use IRFANM\SIASHAF\Helper\SoftDeletes;
use PDO;
use PDOException;

class MasterHafalanRepository
{
    use SoftDeletes;

    private PDO $connection;
    private const TABLE_NAME = 'master_hafalan';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    private function mapRowToData(array $row): MasterHafalan
    {
        $masterHafalan =  new MasterHafalan();

        $masterHafalan->memory_id = $row['memory_id'];
        $masterHafalan->title = $row['title'];
        $masterHafalan->ayat = $row['ayat'];
        $masterHafalan->description = $row['description'] ?? null;
        $masterHafalan->created_at = $row['created_at'];
        $masterHafalan->updated_at = $row['updated_at'];
        $masterHafalan->deleted_at = $row['deleted_at'] ?? null;
    
        return $masterHafalan;
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
            throw new \Exception("Error saat mengambil data master_hafalan: " . $err->getMessage());
        }
    }

    public function save(MasterHafalan $hafalan): MasterHafalan
    {
        try {
            $statement = $this->connection->prepare("
                INSERT INTO master_hafalan (memory_id, title, ayat, description, created_at, updated_at, deleted_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $hafalan->memory_id,
                $hafalan->title,
                $hafalan->ayat,
                $hafalan->description,
                $hafalan->created_at,
                $hafalan->updated_at,
                $hafalan->deleted_at,
            ]);

            return $hafalan;
        } catch (PDOException $err) {
            error_log("Error saat menyimpan master_hafalan: " . $err->getMessage());
            return $hafalan;
        }
    }

    public function update(MasterHafalan $hafalan): MasterHafalan
    {
        try {
            $statement = $this->connection->prepare("
                UPDATE master_hafalan 
                SET title = ?, ayat = ?, description = ?, created_at = ?, updated_at = ?, deleted_at = ?
                WHERE memory_id = ?
            ");

            $statement->execute([
                $hafalan->title,
                $hafalan->ayat,
                $hafalan->description,
                $hafalan->created_at,
                $hafalan->updated_at,
                $hafalan->deleted_at,
                $hafalan->memory_id,
            ]);

            return $hafalan;
        } catch (PDOException $err) {
            error_log("Error saat memperbarui master_hafalan: " . $err->getMessage());
            return $hafalan;
        }
    }

    public function findByMemoryId(string $memory_id): ?MasterHafalan
    {
        try {
            $statement = $this->connection->prepare("SELECT * FROM master_hafalan WHERE memory_id = ? AND deleted_at IS NULL");
            $statement->execute([$memory_id]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }

            return null;
        } catch (PDOException $err) {
            error_log("Error saat mencari master_hafalan: " . $err->getMessage());
            return null;
        }
    }

    public function deletePermanently(string $memory_id): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM master_hafalan WHERE memory_id = ?");
            return $statement->execute([$memory_id]);
        } catch (PDOException $err) {
            error_log("Error saat menghapus data master_hafalan: " . $err->getMessage());
            return false;
        }
    }

    public function deleteAllPermanently(): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM master_hafalan WHERE 1");
            return $statement->execute();
        } catch (PDOException $err) {
            error_log("Error saat menghapus semua data secara permanen: " . $err->getMessage());
            return false;
        }
    }
}
