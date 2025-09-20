<?php
// controller/HomeController.php
require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../model/file.php';

class HomeController {

    private $userModel;
    private $fileModel;

    public function __construct() {
        $this->userModel = new User();
        $this->fileModel = new File();
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $username = $this->userModel->getUsername($userId);

        // Seção e ordenação
        $view = $_GET['view'] ?? 'compartilhada';
        $order = $_GET['order'] ?? 'newest';

        // Pegando arquivos
        $files = $this->fileModel->getFiles($userId, $view, $order);

        // Informações de disco
        $diskUsage = $this->fileModel->getDiskUsage();

        // Chama a view
        require __DIR__ . '/../public/home.php';
    }
}
