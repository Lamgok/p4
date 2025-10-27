<?php
// TodoController.php

require_once (__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        $todoModel = new TodoModel();
        
        $filterStatus = isset($_GET['filter']) ? $_GET['filter'] : null;
        $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : null;

        $todos = $todoModel->getAllTodos($filterStatus, $searchQuery);
        
        $currentFilter = $filterStatus;
        $currentSearch = $searchQuery;
        
        $error = isset($_GET['error']) ? $_GET['error'] : null;
        
        include (__DIR__ . '/../views/TodoView.php');
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $description = isset($_POST['description']) ? $_POST['description'] : null;

            if (empty($title)) {
                header('Location: index.php?error=empty_title_create');
                return;
            }

            $todoModel = new TodoModel();
            
            if (!$todoModel->createTodo($title, $description)) {
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
            $title = trim($_POST['title']);
            $isFinished = $_POST['is_finished']; 
            $description = isset($_POST['description']) ? $_POST['description'] : null;
            
            if (empty($title)) {
                header('Location: index.php?error=empty_title_update');
                return;
            }

            $todoModel = new TodoModel();
            
            if (!$todoModel->updateTodo($id, $title, $isFinished, $description)) {
                header('Location: index.php?error=duplicate_title_update');
                return;
            }
        }
        header('Location: index.php');
    }
    
    // Fungsi untuk menerima data urutan dari AJAX
    public function updateSort()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_ids'])) {
            // Mengubah string ID yang dipisahkan koma menjadi array ID
            $todoIdsString = $_POST['todo_ids'];
            $todoIds = explode(',', $todoIdsString);
            
            // Memfilter agar hanya ID yang valid yang diproses
            $todoIds = array_filter($todoIds, 'is_numeric');

            if (!empty($todoIds)) {
                 $todoModel = new TodoModel();
                 if ($todoModel->updateSortOrder($todoIds)) {
                     http_response_code(200); // OK
                 } else {
                     http_response_code(500); // Internal Server Error
                 }
            } else {
                http_response_code(400); // Bad Request
            }
        }
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