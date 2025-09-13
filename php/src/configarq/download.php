<?php
require (__DIR__.'/../model/db_connect.php');

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT file_name, file_path FROM uploaded_files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch();
    
    if($file && file_exists($file['file_path'])) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file['file_name']).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file['file_path']));
        readfile($file['file_path']);
        exit;
    }
}
header("Location: ../home/index.php");
?>

