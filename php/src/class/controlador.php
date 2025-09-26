<?php
session_start();
require __DIR__ . '/../model/db_connect.php';

class Controlador {
    private $uploadDir;
    private $conn;

   public function upload($files, $userId) {
    if (!$userId) {
        return ['success' => false, 'error' => 'Usuário não autenticado'];
    }

    if (!isset($files['file'])) {
        return ['success' => false, 'error' => 'Nenhum arquivo enviado'];
    }

    $file = $files['file'];

    // 1️⃣ Checa se houve erro no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'O arquivo excede upload_max_filesize no php.ini',
            UPLOAD_ERR_FORM_SIZE => 'O arquivo excede MAX_FILE_SIZE do formulário',
            UPLOAD_ERR_PARTIAL => 'O upload do arquivo foi feito parcialmente',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausente',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo no disco',
            UPLOAD_ERR_EXTENSION => 'Upload bloqueado por extensão do PHP',
        ];
        $errMsg = $errorMessages[$file['error']] ?? 'Erro desconhecido no upload';
        return ['success' => false, 'error' => $errMsg];
    }

    $fileName   = basename($file['name']);
    $targetFile = $this->uploadDir . $fileName;

    // 2️⃣ Verifica se o arquivo temporário existe
    if (!file_exists($file['tmp_name'])) {
        return ['success' => false, 'error' => 'Arquivo temporário não encontrado'];
    }

    // 3️⃣ Move o arquivo para uploads/
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        error_log("Falha ao mover {$file['tmp_name']} para {$targetFile}");
        return ['success' => false, 'error' => 'Falha ao mover o arquivo para a pasta de uploads'];
    }

    // 4️⃣ Insere no banco usando PDO com try/catch
    try {
        $stmt = $this->conn->prepare("
            INSERT INTO uploaded_files (file_name, file_path, file_size, file_type, upload_date, uploaded_by)
            VALUES (?, ?, ?, ?, NOW(), ?)
        ");
        $stmt->execute([
            $fileName,
            $targetFile,
            $file['size'],
            $file['type'],
            $userId
        ]);
    } catch (PDOException $e) {
        error_log("Erro no insert do banco: " . $e->getMessage());
        return ['success' => false, 'error' => 'Falha ao registrar o arquivo no banco'];
    }

    // 5️⃣ Retorna sucesso
    return ['success' => true, 'file' => $fileName];
}

}
