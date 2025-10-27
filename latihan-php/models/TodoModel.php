<?php
require_once(__DIR__ . '/../config.php');

class TodoModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = pg_connect('host=' . DB_HOST . ' port=' . DB_PORT . ' dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASSWORD);
        if (!$this->conn) {
            die('Koneksi database gagal');
        }
    }

    public function getAllTodos($filter = 'all', $search = '')
    {
        $query = "SELECT * FROM todo WHERE 1=1";
        $params = [];

        // Filter berdasarkan status
        if ($filter === 'finished') {
            $query .= " AND is_finished = true";
        } elseif ($filter === 'unfinished') {
            $query .= " AND is_finished = false";
        }

        // Pencarian
        if (!empty($search)) {
            $query .= " AND (title ILIKE $1 OR description ILIKE $1)";
            $params[] = "%$search%";
        }

        $query .= " ORDER BY sort_order ASC, created_at DESC";
        
        $result = pg_query_params($this->conn, $query, $params);
        $todos = [];
        if ($result && pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                $todos[] = $row;
            }
        }
        return $todos;
    }

    public function getTodoById($id)
    {
        $query = "SELECT * FROM todo WHERE id = $1";
        $result = pg_query_params($this->conn, $query, [$id]);
        return $result ? pg_fetch_assoc($result) : null;
    }

    public function createTodo($title, $description)
    {
        // Cek duplikasi title (CASE-INSENSITIVE menggunakan LOWER)
        $checkQuery = "SELECT id FROM todo WHERE LOWER(title) = LOWER($1)";
        $checkResult = pg_query_params($this->conn, $checkQuery, [$title]);
        
        // Perbaikan: Cek jika query berhasil sebelum pakai pg_num_rows
        if ($checkResult === false) {
            return ['success' => false, 'message' => 'Error saat mengecek duplikasi title'];
        }
        
        if (pg_num_rows($checkResult) > 0) {
            return ['success' => false, 'message' => 'Judul todo "'.$title.'" sudah ada'];
        }

        // Insert data baru
        $query = "INSERT INTO todo (title, description) VALUES ($1, $2)";
        $result = pg_query_params($this->conn, $query, [$title, $description]);
        
        if ($result === false) {
            $error = pg_last_error($this->conn);
            return ['success' => false, 'message' => 'Gagal menyimpan data: ' . $error];
        }
        
        return ['success' => true, 'message' => 'Todo berhasil dibuat'];
    }

    public function updateTodo($id, $title, $description, $is_finished)
    {
        // Cek duplikasi title (CASE-INSENSITIVE menggunakan LOWER)
        $checkQuery = "SELECT id FROM todo WHERE LOWER(title) = LOWER($1) AND id != $2";
        $checkResult = pg_query_params($this->conn, $checkQuery, [$title, $id]);
        
        if ($checkResult === false) {
            return ['success' => false, 'message' => 'Error saat mengecek duplikasi title'];
        }
        
        if (pg_num_rows($checkResult) > 0) {
            return ['success' => false, 'message' => 'Judul todo "'.$title.'" sudah ada '];
        }

        $query = "UPDATE todo SET title=$1, description=$2, is_finished=$3, updated_at=CURRENT_TIMESTAMP WHERE id=$4";
        $result = pg_query_params($this->conn, $query, [$title, $description, $is_finished ? 'true' : 'false', $id]);
        
        if ($result === false) {
            $error = pg_last_error($this->conn);
            return ['success' => false, 'message' => 'Gagal mengupdate data: ' . $error];
        }
        
        return ['success' => true, 'message' => 'Todo berhasil diupdate'];
    }

    public function deleteTodo($id)
    {
        $query = "DELETE FROM todo WHERE id=$1";
        $result = pg_query_params($this->conn, $query, [$id]);
        
        if ($result === false) {
            $error = pg_last_error($this->conn);
            return ['success' => false, 'message' => 'Gagal menghapus data: ' . $error];
        }
        
        return ['success' => true, 'message' => 'Todo berhasil dihapus'];
    }

    public function updateSortOrder($todos)
    {
        foreach ($todos as $index => $todoId) {
            $query = "UPDATE todo SET sort_order = $1 WHERE id = $2";
            $result = pg_query_params($this->conn, $query, [$index, $todoId]);
            
            if ($result === false) {
                // Log error tapi lanjutkan proses
                error_log('Error updating sort order for todo: ' . $todoId);
            }
        }
        return true;
    }
}