<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../config/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT file_path FROM uploaded_files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $filePathBanco = $file['file_path'];

        // Detecta se caminho é absoluto
        
        if (DIRECTORY_SEPARATOR === '/') {
            $isAbsolute = strpos($filePathBanco, '/') === 0;
        } else {
            $isAbsolute = preg_match('/^[a-zA-Z]:\\\\/', $filePathBanco) === 1;
        }

        if ($isAbsolute) {
            $filePath = $filePathBanco;
        } else {
            $baseDir = realpath(__DIR__ . '/../');
            $filePath = $baseDir . DIRECTORY_SEPARATOR . $filePathBanco;
        }

        if (!file_exists($filePath)) {
            echo "Arquivo não encontrado no caminho: $filePath";
            exit();
        }

        if (unlink($filePath)) {
            $deleteStmt = $conn->prepare("DELETE FROM uploaded_files WHERE id = ?");
            $deleteStmt->execute([$id]);

            if ($deleteStmt->rowCount() > 0) {
                header("Location: ../home/index.php?view=pessoal&success=1&deleted=1");
                exit();
            } else {
                echo "Erro ao deletar registro no banco de dados.";
                exit();
            }
        } else {
            echo "Falha ao apagar o arquivo físico.";
            exit();
        }
    } else {
        echo "Arquivo não encontrado no banco de dados.";
        exit();
    }
}

header("Location: ../home/index.php?deleted=0");
exit();
?>
