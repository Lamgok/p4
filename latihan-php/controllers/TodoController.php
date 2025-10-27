<?php
require_once(__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        $todoModel = new TodoModel();
        
        $filter = $_GET['filter'] ?? 'all';
        $search = $_GET['search'] ?? '';
        
        $todos = $todoModel->getAllTodos($filter, $search);
        include(__DIR__ . '/../views/TodoView.php');
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            // Validasi input
            if (empty($title)) {
                session_start();
                $_SESSION['error'] = 'Judul tidak boleh kosong';
                header('Location: index.php');
                exit;
            }
            
            if (strlen($title) < 3) {
                session_start();
                $_SESSION['error'] = 'Judul harus minimal 3 karakter';
                header('Location: index.php');
                exit;
            }
            
            $todoModel = new TodoModel();
            $result = $todoModel->createTodo($title, $description);
            
            session_start();
            if (!$result['success']) {
                $_SESSION['error'] = $result['message'];
            } else {
                $_SESSION['success'] = $result['message'];
            }
        }
        header('Location: index.php');
        exit;
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $is_finished = isset($_POST['is_finished']) ? true : false;
            
            if (empty($title) || empty($id)) {
                session_start();
                $_SESSION['error'] = 'Data tidak lengkap';
                header('Location: index.php');
                exit;
            }
            
            if (strlen($title) < 3) {
                session_start();
                $_SESSION['error'] = 'Judul harus minimal 3 karakter';
                header('Location: index.php');
                exit;
            }
            
            $todoModel = new TodoModel();
            $result = $todoModel->updateTodo($id, $title, $description, $is_finished);
            
            session_start();
            if (!$result['success']) {
                $_SESSION['error'] = $result['message'];
            } else {
                $_SESSION['success'] = $result['message'];
            }
        }
        header('Location: index.php');
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $result = $todoModel->deleteTodo($id);
            
            session_start();
            if (!$result['success']) {
                $_SESSION['error'] = $result['message'];
            } else {
                $_SESSION['success'] = $result['message'];
            }
        }
        header('Location: index.php');
        exit;
    }

    public function detail()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $todo = $todoModel->getTodoById($id);
            include(__DIR__ . '/../views/TodoDetailView.php');
        } else {
            header('Location: index.php');
        }
    }

    public function updateSort()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $todos = $data['todos'] ?? [];
            
            if (!empty($todos)) {
                $todoModel = new TodoModel();
                $result = $todoModel->updateSortOrder($todos);
                echo json_encode(['success' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            }
        }
    }
}