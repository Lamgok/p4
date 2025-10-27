<?php
// TodoModel.php

// Pastikan file config.php berisi detail koneksi PostgreSQL (DB_HOST, DB_NAME, dll.)
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
        // $filterStatus: '0'=FALSE (Belum Selesai), '1'=TRUE (Selesai), null=Semua
        $conditions = [];
        $params = [];
        
        // 1. Filter Status
        if ($filterStatus !== null && in_array($filterStatus, ['0', '1'])) {
            $conditions[] = 'is_finished = $' . (count($params) + 1);
            $params[] = $filterStatus == '1' ? 'TRUE' : 'FALSE';
        }

        // 2. Search (mencari di kolom title atau description)
        if (!empty($search)) {
            $conditions[] = '(title ILIKE $' . (count($params) + 1) . ' OR description ILIKE $' . (count($params) + 1) . ')';
            $params[] = '%' . trim($search) . '%';
        }

        // SELECT menggunakan kolom baru dan ORDER BY sort_order
        $query = 'SELECT id, title, description, is_finished, created_at, updated_at, sort_order FROM todo'; 
        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $query .= ' ORDER BY sort_order ASC, id DESC'; // Order by sort_order

        $result = pg_query_params($this->conn, $query, $params);
        $todos = [];
        if ($result && pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                // Konversi string 't'/'f' PostgreSQL menjadi boolean PHP
                $row['is_finished'] = filter_var($row['is_finished'], FILTER_VALIDATE_BOOLEAN);
                $todos[] = $row;
            }
        }
        return $todos;
    }
    
    // Fungsi untuk validasi duplikasi judul
    public function checkDuplicateTitle($title, $excludeId = null)
    {
        $title = trim($title);
        
        $params = [$title];
        $query = 'SELECT id FROM todo WHERE title = $1'; 
        
        if ($excludeId !== null) {
            $query .= ' AND id != $2';
            $params[] = $excludeId;
        }

        $result = pg_query_params($this->conn, $query, $params);
        return $result && pg_num_rows($result) > 0;
    }

    public function createTodo($title, $description = null)
    {
        $title = trim($title); 

        if ($this->checkDuplicateTitle($title)) {
            return false; 
        }

        $description = trim($description);
        $description = empty($description) ? null : $description;

        // Ambil nilai sort_order maksimum + 1
        $maxSortQuery = 'SELECT COALESCE(MAX(sort_order), 0) + 1 FROM todo';
        $maxSortResult = pg_query($this->conn, $maxSortQuery);
        $newSortOrder = pg_fetch_result($maxSortResult, 0, 0);

        // INSERT menggunakan 'title', 'description', dan 'sort_order'
        $query = 'INSERT INTO todo (title, description, sort_order) VALUES ($1, $2, $3)';
        $result = pg_query_params($this->conn, $query, [$title, $description, $newSortOrder]);
        return $result !== false;
    }

    public function updateTodo($id, $title, $isFinished, $description = null)
    {
        $title = trim($title);
        
        if ($this->checkDuplicateTitle($title, $id)) {
            return false;
        }
        
        $description = trim($description);
        $description = empty($description) ? null : $description;

        // Konversi 0/1 dari form menjadi TRUE/FALSE untuk PostgreSQL BOOLEAN
        $isFinishedValue = $isFinished == '1' ? 'TRUE' : 'FALSE';

        $query = 'UPDATE todo SET title=$1, is_finished=$2, description=$3 WHERE id=$4';
        $result = pg_query_params($this->conn, $query, [$title, $isFinishedValue, $description, $id]);
        return $result !== false;
    }
    
    // Fungsi untuk menyimpan urutan (sorting)
    public function updateSortOrder($todoIds)
    {
        if (!is_array($todoIds) || empty($todoIds)) {
            return false;
        }

        $success = true;
        // Gunakan transaksi untuk memastikan atomicity
        pg_query($this->conn, "BEGIN");

        foreach ($todoIds as $index => $id) {
            $sortOrder = $index + 1;
            $query = 'UPDATE todo SET sort_order=$1 WHERE id=$2';
            $result = pg_query_params($this->conn, $query, [$sortOrder, $id]);
            
            if (!$result) {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            pg_query($this->conn, "COMMIT");
        } else {
            pg_query($this->conn, "ROLLBACK");
        }

        return $success;
    }

    public function deleteTodo($id)
    {
        $query = 'DELETE FROM todo WHERE id=$1';
        $result = pg_query_params($this->conn, $query, [$id]);
        return $result !== false;
    }
}