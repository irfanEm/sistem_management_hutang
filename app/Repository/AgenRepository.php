<?php

namespace IRFANM\SIMAHU\Repository;

use IRFANM\SIMAHU\Domain\Agen;
use PDO;
use PDOException;

class AgenRepository
{
    private PDO $connection;
    private const TABLE_NAME = 'agents';

    public function __construct(PDO $conn)
    {
        $this->connection = $conn;
    }

    private function mapRowToData(array $row): Agen
    {
        $agen = new Agen();
        $agen->id = $row['id'];
        $agen->kode_agen = $row['kode_agen'];
        $agen->nama_agen = $row['nama_agen'];
        $agen->kontak = $row['kontak'];
        $agen->created_at = $row['created_at'];
        $agen->updated_at = $row['updated_at'];

        return $agen;
    }

    public function save(Agen $agen): Agen
    {
        try{
            $stmt = $this->connection->prepare("INSERT INTO agents (kode_agen, nama_agen, kontak, created_at, updated_at) VALUES(?, ?, ?, ?, ?)");

            $stmt->execute([
                $agen->kode_agen,
                $agen->nama_agen,
                $agen->kontak,
                $agen->created_at,
                $agen->updated_at
            ]);

            $agen->id = $this->connection->lastInsertId();
            return $agen;
        }catch(PDOException $err){
            error_log("Gagal menyimpan data agen : " . $err->getMessage());
            return $agen;
        }
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
        } catch (\PDOException $err) {
            error_log("Query: $query");
            throw new \Exception("Error saat mengambil data " . self::TABLE_NAME . ": " . $err->getMessage());
        }
    }

    public function update(Agen $agen): Agen
    {
        try{
            $stmt = $this->connection->prepare("UPDATE agents SET nama_agen = ?, kontak = ?, updated_at = ? WHERE kode_agen = ?");

            $stmt->execute([
                $agen->nama_agen,
                $agen->kontak, 
                $agen->updated_at,
                $agen->kode_agen
            ]);

            return $agen;
        }catch(PDOException $err){
            error_log("Gagal memperbarui data agen : " . $err->getMessage());
            return $agen;
        }
    }

    public function findById(string $kode_agen): ?Agen
    {
        try{
            $stmt = $this->connection->prepare("SELECT * FROM agents WHERE kode_agen = ?");
            $stmt->execute([$kode_agen]);

            if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                return $this->mapRowToData($row);
            }

            return null;

        }catch(PDOException $err){
            error_log("Error saat mencari data agen : " . $err->getMessage());
            return null;
        }
    }

    public function deletePermanently(string $kode_agen): bool
    {
        try{
            $stmt = $this->connection->prepare("DELETE FROM agents WHERE kode_agen = ?");
            return $stmt->execute([$kode_agen]);
        }catch(PDOException $err){
            error_log("Gagal menghapus data agen : " . $err->getMessage());
            return false;
        }
    }

    public function deleteAllPermanently(): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM agents WHERE 1");
            return $statement->execute();
        } catch (PDOException $err) {
            error_log("Error saat menghapus semua data secara permanen: " . $err->getMessage());
            return false;
        }
    }
}
