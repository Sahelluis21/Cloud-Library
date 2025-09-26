<?php
session_start();
require __DIR__ . '/../model/db_connect.php';

class Controlador {
    private $uploadDir;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;

        // Caminho da pasta de uploads
        $this->uploadDir = __DIR__ . '/../uploads/';

        // Se a pasta não existir, cria
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }

        // Ajusta permissões (não quebra caso não tenha suporte no container)
        if (function_exists('posix_getuid')) {
            @chown($this->uploadDir, 'www-data');
            @chgrp($this->uploadDir, 'www-data');
        }
        @chmod($this->uploadDir, 0775);
    }

    // ==== UPLOAD DE ARQUIVOS ====
    public function upload($files, $userId) {
        if (!$userId) {
            return ['success' => false, 'error' => 'Usuário não autenticado'];
        }

        if (!isset($files['file'])) {
            return ['success' => false, 'error' => 'Nenhum arquivo enviado'];
        }

        $file = $files['file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Erro no upload: ' . $file['error']];
        }

        $fileName   = basename($file['name']);
        $targetFile = $this->uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            error_log("Falha ao mover {$file['tmp_name']} para {$targetFile}");
            return ['success' => false, 'error' => 'Falha ao mover o arquivo'];
        }

        // Salvar no banco
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

        return ['success' => true, 'file' => $fileName];
    }

}
