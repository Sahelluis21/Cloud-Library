<?php

require __DIR__ . '/../model/db_connect.php';

class Controlador {
    private $conn;
    private $uploadDir;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->uploadDir = realpath(__DIR__ . '/../../uploads/') . '/';

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // ==== UPLOAD DE ARQUIVOS ====
    public function upload($fileData, $userId) {
        if (!$userId) {
            return ['success' => false, 'error' => 'Usuário não autenticado'];
        }

        if (!isset($fileData["fileToUpload"])) {
            return ['success' => false, 'error' => 'Nenhum arquivo enviado'];
        }

        $originalName = basename($fileData["fileToUpload"]["name"]);
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $fileSize = $fileData["fileToUpload"]["size"];
        $fileMimeType = $fileData["fileToUpload"]["type"];

        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
        $targetFile = $this->uploadDir . $originalName;

        // Evita sobrescrever
        $counter = 1;
        while (file_exists($targetFile)) {
            $newFileName = $fileName . "_$counter." . $fileExtension;
            $targetFile = $this->uploadDir . $newFileName;
            $counter++;
        }

        if (move_uploaded_file($fileData["fileToUpload"]["tmp_name"], $targetFile)) {
            $absolutePath = realpath($targetFile);
            $finalFileName = basename($targetFile);

            try {
                $stmt = $this->conn->prepare("
                    INSERT INTO uploaded_files (file_name, file_path, file_size, file_type, uploaded_by)
                    VALUES (:name, :path, :size, :type, :uploaded_by)
                ");
                $stmt->bindParam(':name', $finalFileName);
                $stmt->bindParam(':path', $absolutePath);
                $stmt->bindParam(':size', $fileSize);
                $stmt->bindParam(':type', $fileMimeType);
                $stmt->bindParam(':uploaded_by', $userId);
                $stmt->execute();

                return [
                    'success' => true,
                    'file_name' => $finalFileName,
                    'file_size' => $fileSize,
                    'file_type' => $fileMimeType
                ];
            } catch (PDOException $e) {
                if (file_exists($absolutePath)) {
                    unlink($absolutePath);
                }
                return ['success' => false, 'error' => 'Erro ao salvar no banco de dados'];
            }
        } else {
            return ['success' => false, 'error' => 'Falha ao mover o arquivo'];
        }
    }

    // ==== DELETE DE ARQUIVOS ==== //
    public function delete($fileId) {
        if (!$fileId) {
            return ['success' => false, 'error' => 'ID do arquivo não fornecido'];
        }

        $stmt = $this->conn->prepare("SELECT file_path FROM uploaded_files WHERE id = ?");
        $stmt->execute([$fileId]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$file) {
            return ['success' => false, 'error' => 'Arquivo não encontrado no banco de dados'];
        }

        $filePathBanco = $file['file_path'];

        if (DIRECTORY_SEPARATOR === '/') {
            $isAbsolute = strpos($filePathBanco, '/') === 0;
        } else {
            $isAbsolute = preg_match('/^[a-zA-Z]:\\\\/', $filePathBanco) === 1;
        }

        $filePath = $isAbsolute ? $filePathBanco : realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . $filePathBanco;

        if (!file_exists($filePath)) {
            return ['success' => false, 'error' => "Arquivo não encontrado no caminho: $filePath"];
        }

        if (unlink($filePath)) {
            $deleteStmt = $this->conn->prepare("DELETE FROM uploaded_files WHERE id = ?");
            $deleteStmt->execute([$fileId]);

            if ($deleteStmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Arquivo deletado com sucesso'];
            } else {
                return ['success' => false, 'error' => 'Erro ao deletar registro no banco de dados'];
            }
        } else {
            return ['success' => false, 'error' => 'Falha ao apagar o arquivo físico'];
        }
    }

    // ==== DOWNLOAD DE ARQUIVOS ====
    public function download($fileId) {
        if (!$fileId) {
            return ['success' => false, 'error' => 'ID do arquivo não fornecido'];
        }

        $stmt = $this->conn->prepare("SELECT file_name, file_path FROM uploaded_files WHERE id = ?");
        $stmt->execute([$fileId]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$file || !file_exists($file['file_path'])) {
            return ['success' => false, 'error' => 'Arquivo não encontrado'];
        }

        return [
            'success' => true,
            'file_name' => $file['file_name'],
            'file_path' => $file['file_path']
        ];
    }

    // ==== COMPARTILHAR / DESCOMPARTILHAR ARQUIVOS ====
    public function share($fileId, $userId, $action = 'share') {
        if (!$userId) {
            return ['success' => false, 'error' => 'Usuário não autenticado'];
        }

        if (!$fileId || !is_numeric($fileId)) {
            return ['success' => false, 'error' => 'ID do arquivo inválido'];
        }

        try {
            $stmt = $this->conn->prepare('SELECT uploaded_by FROM uploaded_files WHERE id = :id');
            $stmt->execute([':id' => $fileId]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$file || $file['uploaded_by'] != $userId) {
                return ['success' => false, 'error' => 'Arquivo não pertence ao usuário'];
            }

            $isShared = ($action === 'unshare') ? false : true;
            $stmt = $this->conn->prepare('UPDATE uploaded_files SET is_shared = :is_shared WHERE id = :id');
            $stmt->execute([':is_shared' => $isShared, ':id' => $fileId]);

            return ['success' => true, 'shared' => $isShared];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erro ao atualizar no banco'];
        }
    }

    public function registrarUsuario($username, $senha_plain) {
    $username = trim($username);

    if (empty($username) || empty($senha_plain)) {
        die('Usuário e senha são obrigatórios!');
    }

    // Verificar se o usuário já existe
    $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        die('Nome de usuário já está em uso.');
    }

    // Criar o hash da senha
    $hash = password_hash($senha_plain, PASSWORD_DEFAULT);

    // Inserir novo usuário no banco
    $stmt = $this->conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    if ($stmt->execute([$username, $hash])) {
        echo "Usuário cadastrado com sucesso!";
        header("Location: ../login.php"); // Descomente para redirecionar
        exit;
    } else {
        echo "Erro ao cadastrar usuário.";
    }
}

}
