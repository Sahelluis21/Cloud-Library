<?php
session_start();
require __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php?error=1');
    exit;
}

$file_id = (int) $_GET['id'];
$action = $_GET['action'] ?? 'share';

try {
    // Verifica se o arquivo pertence ao usu�rio
    $stmt = $conn->prepare('SELECT uploaded_by FROM uploaded_files WHERE id = :id');
    $stmt->execute([':id' => $file_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file || $file['uploaded_by'] != $user_id) {
        header('Location: ../index.php?error=1');
        exit;
    }

    if ($action === 'unshare') {
        $stmt = $conn->prepare('UPDATE uploaded_files SET is_shared = FALSE WHERE id = :id');
    } else {
        $stmt = $conn->prepare('UPDATE uploaded_files SET is_shared = TRUE WHERE id = :id');
    }

    $stmt->execute([':id' => $file_id]);

    // Verifica se a requisi��o � AJAX (por fetch ou XMLHttpRequest)
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        echo json_encode(['status' => 'success']);
        exit;
    }

    header('Location: ../home/index.php?view=pessoal&success=1&shared=1');
    exit;

} catch (PDOException $e) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        echo json_encode(['status' => 'error', 'message' => 'database error']);
        exit;
    }

    header('Location: ../index.php?error=1');
    exit;
}
