<?php
// TodoModel.php

require_once (__DIR__ . '/../config.php');

class TodoModel
{
    private $conn;

    public function __construct()
    {
        // Inisialisasi koneksi database PostgreSQL
        $this->conn = pg_connect('host=' . DB_HOST . ' port=' . DB_PORT . ' dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASSWORD);
        if (!$this->conn) {
            die('Koneksi database gagal');
        }
    }

    public function getAllTodos($filterStatus = null, $search = null)
    {
        // $filterStatus: 0=Belum Selesai, 1=Selesai, null=Semua
        $conditions = [];
        $params = [];

        // 1. Filter Status
        if ($filterStatus !== null && in_array($filterStatus, ['0', '1'])) {
            $conditions[] = 'status = $' . (count($params) + 1);
            $params[] = $filterStatus;
        }

        // 2. Search (mencari di kolom activity/title)
        if (!empty($search)) {
            $conditions[] = 'activity ILIKE $' . (count($params) + 1);
            $params[] = '%' . $search . '%';
        }

        $query = 'SELECT *, created_at as updated_at FROM todo'; // Simulasi updated_at = created_at
        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $query .= ' ORDER BY id DESC'; // Order untuk simulasi sorting

        $result = pg_query_params($this->conn, $query, $params);
        $todos = [];
        if ($result && pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                $todos[] = $row;
            }
        }
        return $todos;
    }
    
    // Fungsi baru untuk validasi judul
    public function checkDuplicateActivity($activity, $excludeId = null)
    {
        $params = [$activity];
        $query = 'SELECT id FROM todo WHERE activity = $1';
        
        if ($excludeId !== null) {
            $query .= ' AND id != $2';
            $params[] = $excludeId;
        }

        $result = pg_query_params($this->conn, $query, $params);
        return $result && pg_num_rows($result) > 0;
    }

    public function createTodo($activity)
    {
        // Validasi duplikasi
        if ($this->checkDuplicateActivity($activity)) {
            // Dalam aplikasi nyata, ini harus ditangani dengan pesan error yang baik
            // Untuk sementara, kita return false dan biarkan controller menanganinya
            return false; 
        }

        $query = 'INSERT INTO todo (activity) VALUES ($1)';
        $result = pg_query_params($this->conn, $query, [$activity]);
        return $result !== false;
    }

    public function updateTodo($id, $activity, $status)
    {
        // Validasi duplikasi, mengecualikan ID saat ini
        if ($this->checkDuplicateActivity($activity, $id)) {
            return false;
        }
        
        // Asumsi 'updated_at' tidak ada di DB, kita hanya update activity dan status
        $query = 'UPDATE todo SET activity=$1, status=$2 WHERE id=$3';
        $result = pg_query_params($this->conn, $query, [$activity, $status, $id]);
        return $result !== false;
    }

    public function deleteTodo($id)
    {
        $query = 'DELETE FROM todo WHERE id=$1';
        $result = pg_query_params($this->conn, $query, [$id]);
        return $result !== false;
    }
}