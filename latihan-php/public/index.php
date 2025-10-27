<?php
// index.php

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'index';
}

// Pastikan jalur ke Controller sudah benar
include (__DIR__ . '/controllers/TodoController.php');

$todoController = new TodoController();
switch ($page) {
    case 'index':
        $todoController->index();
        break;
    case 'create':
        $todoController->create();
        break;
    case 'update':
        $todoController->update();
        break;
    case 'delete':
        $todoController->delete();
        break;
    case 'updateSort': // Rute untuk AJAX drag-and-drop
        $todoController->updateSort();
        break;
}