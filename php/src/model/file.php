<?php
// model/File.php
require_once 'db_connect.php';

class File {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getFiles($userId, $view, $order) {
        $orderBy = match($order) {
            'oldest' => 'upload_date ASC',
            'largest' => 'file_size DESC',
            'smallest' => 'file_size ASC',
            'az' => 'file_name ASC',
            'za' => 'file_name DESC',
            default => 'upload_date DESC',
        };

        if ($view === 'pessoal') {
            $stmt = $this->conn->prepare("
                SELECT f.*, u.username AS uploader_name
                FROM uploaded_files f
                JOIN users u ON f.uploaded_by = u.id
                WHERE f.uploaded_by = :user_id
                ORDER BY $orderBy
            ");
            $stmt->execute([':user_id' => $userId]);
        } else {
            $stmt = $this->conn->prepare("
                SELECT f.*, u.username AS uploader_name
                FROM uploaded_files f
                JOIN users u ON f.uploaded_by = u.id
                WHERE f.is_shared = TRUE
                ORDER BY $orderBy
            ");
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDiskUsage() {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        $usedSpace = $totalSpace - $freeSpace;

        return [
            'total' => $totalSpace,
            'free' => $freeSpace,
            'used' => $usedSpace,
            'used_percentage' => round(($usedSpace / $totalSpace) * 100, 2)
        ];
    }

    public static function formatSize($bytes) {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' bytes';
    }

    public static function getFriendlyFileType($mime) {
        if (strpos($mime, 'image/') === 0) return 'JPEG';
        if ($mime === 'application/pdf') return 'PDF';
        if (strpos($mime, 'video/') === 0) return 'MP4';
        if (strpos($mime, 'application/vnd.openxmlformats-officedocument') === 0) return 'Documento Office';
        return 'Outro';
    }
}
