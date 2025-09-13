<?php
session_start();
require __DIR__ . '/../config/db_connect.php';

// Corrige o tipo de conteúdo
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
    exit();
}

$userId = $_SESSION['user_id'];
$uploadDir = realpath(__DIR__ . '/../uploads/') . '/';
  // Ajustado para refletir volume Docker

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["fileToUpload"])) {
    $originalName = basename($_FILES["fileToUpload"]["name"]);
    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $fileSize = $_FILES["fileToUpload"]["size"];
    $fileMimeType = $_FILES["fileToUpload"]["type"];

    $fileName = pathinfo($originalName, PATHINFO_FILENAME);
    $targetFile = $uploadDir . $originalName;

    // Evita sobrescrever arquivo existente
    $counter = 1;
    while (file_exists($targetFile)) {
        $newFileName = $fileName . "_$counter." . $fileExtension;
        $targetFile = $uploadDir . $newFileName;
        $counter++;
    }

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
        $absolutePath = realpath($targetFile);
        $finalFileName = basename($targetFile);

        try {
            $stmt = $conn->prepare("
                INSERT INTO uploaded_files (file_name, file_path, file_size, file_type, uploaded_by)
                VALUES (:name, :path, :size, :type, :uploaded_by)
            ");
            $stmt->bindParam(':name', $finalFileName);
            $stmt->bindParam(':path', $absolutePath);
            $stmt->bindParam(':size', $fileSize);
            $stmt->bindParam(':type', $fileMimeType);
            $stmt->bindParam(':uploaded_by', $userId);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'file_name' => $finalFileName,
                'file_size' => $fileSize,
                'file_type' => $fileMimeType
            ]);
            exit();
        } catch (PDOException $e) {
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro ao salvar no banco de dados']);
            exit();
        }
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Falha ao mover o arquivo']);
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Requisição inválida']);
    exit();
}
