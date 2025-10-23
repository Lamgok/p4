<?php
// TodoController.php

require_once (__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        $todoModel = new TodoModel();
        
        // Ambil filter dan search dari GET
        $filterStatus = isset($_GET['filter']) ? $_GET['filter'] : null;
        $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : null;

        // Ambil data todo dengan filter dan search
        $todos = $todoModel->getAllTodos($filterStatus, $searchQuery);
        
        // Kirim status filter dan search query ke View
        $currentFilter = $filterStatus;
        $currentSearch = $searchQuery;
        
        // Inisialisasi pesan error
        $error = isset($_GET['error']) ? $_GET['error'] : null;
        
        include (__DIR__ . '/../views/TodoView.php');
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activity = $_POST['activity'];
            $todoModel = new TodoModel();
            
            if (!$todoModel->createTodo($activity)) {
                // Tambahkan pesan error jika validasi gagal
                header('Location: index.php?error=duplicate_title_create');
                return;
            }
        }
        header('Location: index.php');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $activity = $_POST['activity'];
            $status = $_POST['status'];
            $todoModel = new TodoModel();
            
            if (!$todoModel->updateTodo($id, $activity, $status)) {
                 // Tambahkan pesan error jika validasi gagal
                header('Location: index.php?error=duplicate_title_update');
                return;
            }
        }
        header('Location: index.php');
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $todoModel->deleteTodo($id);
        }
        header('Location: index.php');
    }
}