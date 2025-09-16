<?php
//inicia a sessão e faz a conexão com o banco
session_start();
require __DIR__.'/../../model/db_connect.php';

//se não estiver logado ser redirecionado para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
//pega o usuario da sessão
$user_id = $_SESSION['user_id'];
//pega os arquivos SOMENTE do usuario logado
$stmt = $conn->prepare("SELECT username FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $userData ? $userData['username'] : 'Usuário';


// Qual seção/tabela está ativa? Padrão é "compartilhada"
$view = $_GET['view'] ?? 'compartilhada';

//função de formatar tamanhos
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
//função de formatar tipo de arquivo
 function getFriendlyFileType($mime) {
    if (strpos($mime, 'image/') === 0) return 'JPEG';
    if ($mime === 'application/pdf') return 'PDF';
    if (strpos($mime, 'video/') === 0) return 'MP4';
    if (strpos($mime, 'application/vnd.openxmlformats-officedocument') === 0) return 'Documento Office';
    return 'Outro';
                    }


//função de pegar o tamanho do disco rigido                    
function getDiskUsage() {
    $diskPath = '/';
    $totalSpace = disk_total_space($diskPath);
    $freeSpace = disk_free_space($diskPath);
    $usedSpace = $totalSpace - $freeSpace;

return [
        'total' => $totalSpace,
        'free' => $freeSpace,
        'used' => $usedSpace,
        'used_percentage' => round(($usedSpace / $totalSpace) * 100, 2)
    ];
}
$diskUsage = getDiskUsage();

// função para filtrar dados da tabela de arquivos
$order = $_GET['order'] ?? 'newest';
switch ($order) {
    case 'oldest':
        $orderBy = 'upload_date ASC';
        break;
    case 'largest':
        $orderBy = 'file_size DESC';
        break;
    case 'smallest':
        $orderBy = 'file_size ASC';
        break;
    case 'az':
        $orderBy = 'file_name ASC';
        break;
    case 'za':
        $orderBy = 'file_name DESC';
        break;
    case 'newest':
    default:
        $orderBy = 'upload_date DESC';
        break;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cloud Library</title>
    <link rel="icon" href=" /../../logos/lgo library.png" type="image/png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="/../../frontend/style.css" />

   <!--<script src="../frontend/theme-switcher.js" defer></script>// congelado para quando formos adicionar a feature
    <script src="../configarq/upload-handler.js"></script> // congelado para quando formos adicionar a feature -->

</head>
<body>
    
<div class="header">
    <div class="floating-title-logo">
        <h1 class="site-title">Cloud Library</h1>
        <div class="logo">
            <img src=" /../../logos/lgo library.png" alt="Logo" />
        </div>
    </div>
 <!-- Container de Usuario -->   
<?php if (isset($_SESSION['user_id'])): ?>
    <div class="user-container">
        <div class="user-info">
            <svg class="user-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z"/>
            </svg>
            <span class="username">Olá, <?= htmlspecialchars($username) ?></span>
        </div>
        <button class="logout-btn" onclick="window.location.href='/../../class/controlador.php?action=logout'">Sair</button>

    </div>
<?php endif; ?>
</div>
<!-- botão da sidebar -->
<button class="sidebar-toggle">
    <i class="bi bi-list"></i>
</button>

<!-- Notificações -->
<?php if (isset($_GET['success'])): ?>
    <div id="uploadToast" class="toast-notify success">Upload feito com sucesso!</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div id="errorToast" class="toast-notify error">Erro ao fazer upload.</div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
    <div id="deleteToast" class="toast-notify delete">Arquivo excluído com sucesso!</div>
<?php endif; ?>

<?php if (isset($_GET['shared'])): ?>
    <div id="shareToast" class="toast-notify success">Arquivo compartilhado com sucesso!</div>
<?php endif; ?>


<!-- itens da sidebar e os submenu delas -->
<div class="sidebar">
    <ul class="sidebar-menu">
        <li class="theme-menu-item">
            <div class="menu-header">
                <i class="bi bi-palette me-2"></i> Temas
                <i class="bi bi-chevron-down float-end toggle-icon"></i>
            </div>
            <ul class="theme-submenu">
                <li data-theme="light"><i class="bi bi-sun me-2"></i> Claro</li>
                <li data-theme="dark"><i class="bi bi-moon me-2"></i> Escuro</li>
            </ul>
        </li>
        <li class="support-menu-item">
            <div class="menu-header">
                <i class="bi bi-headset me-2"></i> Suporte
                <i class="bi bi-chevron-down float-end toggle-icon"></i>
            </div>
            <ul class="support-submenu">
                <li><i class="bi bi-envelope me-2"></i>Sahelluis77@gmail.com</li>
                    <i class="bi bi-linkedin me-2"></i>
                    <a href="https://www.linkedin.com/in/sahel-luis-99643445522879456314/" target="_blank" style="color: inherit; text-decoration: none;">
                        Sahel Luis
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</div>

<!-- Inicio do conteudo principal -->
<div class="main-content">
    <div class="dual-layout-container">
        <div class="left-side">
            <div class="help-container">
                <div class="help-area">
                    <h5 class="text-center mb-4">Olá! Esta é a <br><b>Cloud Library</b></br></h5>

<!-- Container de boas vindas e Seleção de tipo de biblioteca -->

<div class="d-flex flex-column gap-2">
    <button onclick="location.href='index.php?view=compartilhada'" 
        class="btn w-100 <?= $view === 'compartilhada' ? 'btn-secondary active-view' : 'btn-outline-secondary' ?>">
        <i class="bi bi-folder2-open me-2"></i> Biblioteca Compartilhada
    </button>
    <button onclick="location.href='index.php?view=pessoal'" 
        class="btn w-100 <?= $view === 'pessoal' ? 'btn-primary active-view' : 'btn-outline-primary' ?>">
        <i class="bi bi-folder me-2"></i> Biblioteca Pessoal
    </button>
</div>

<!-- Container de Armazenamento -->
                </div>
            </div>

            <div class="storage-container">
                <div class="storage-bar">
                    <div class="storage-info">
                        <i class="bi bi-hdd me-2"></i> Espaço de Armazenamento
                    </div>
                    <div class="storage-progress">
                        <div class="storage-progress-filled" style="width: <?php echo $diskUsage['used_percentage']; ?>%"></div>
                    </div>
                    <div class="storage-details">
                        <span><i class="bi bi-pie-chart me-1"></i> <?php echo formatSizeUnits($diskUsage['used']); ?></span>
                        <span><?php echo formatSizeUnits($diskUsage['total']); ?></span>
                    </div>
                </div>
            </div>

<!-- Container de Informações importantes-->
            <div class="info-container">
                <div class="info-area">
                    <h5><i class="bi bi-info-circle me-2"></i>Informações Importantes</h5>
                    <p>• Armazenamento seguro com criptografia</p>
                    <p>• Suporte a arquivos de até 2GB</p>
                    <p>• Compatível com todos os formatos comuns</p>
                </div>
            </div>
        </div>

<!-- Container da tabela de Arquivos -->
        <div class="right-side">
            <div class="documents-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Arquivos Disponíveis <?php echo "\u{2601}\u{FE0F}"; ?></h5>
                    <div class="d-flex align-items-center gap-2">
<!-- Caixa de seleção de filtro -->
                        <select id="sortOrder" class="form-select form-select-sm" style="width: 220px;" onchange="applySort()">
                        <option value="newest" <?= $order === 'newest' ? 'selected' : '' ?>>Últimos adicionados</option>
                        <option value="oldest" <?= $order === 'oldest' ? 'selected' : '' ?>>Primeiros adicionados</option>
                        <option value="largest" <?= $order === 'largest' ? 'selected' : '' ?>>Maior tamanho</option>
                        <option value="smallest" <?= $order === 'smallest' ? 'selected' : '' ?>>Menor tamanho</option>
                        <option value="az" <?= $order === 'az' ? 'selected' : '' ?>>Nome A → Z</option>
                        <option value="za" <?= $order === 'za' ? 'selected' : '' ?>>Nome Z → A</option>
                    </select>
<!-- Botão de upload -->
                      <form id="uploadForm" action="/../../class/controlador.php" method="post"    enctype="multipart/form-data" class="mb-0">
                         <input type="file" id="fileToUpload" name="fileToUpload" class="d-none" required />
                         <button type="button" class="upload-button" onclick="document.getElementById('fileToUpload').click()" title="Adicionar arquivo">
                            <i class="bi bi-plus-circle"></i>
                    </button>
                </form>

                    </div>
                </div>

<!-- Tabela de arquivos -->
                <div class="table-responsive">
                    <?php
// se a tabela estiver em biblioteca pessoal
                    try {
                        if ($view === 'pessoal') {
                            $stmt = $conn->prepare("
                                SELECT f.*, u.username AS uploader_name
                                FROM uploaded_files f
                                JOIN users u ON f.uploaded_by = u.id
                                WHERE f.uploaded_by = :user_id
                                ORDER BY $orderBy
                            ");
                            $stmt->execute([':user_id' => $user_id]);
// se estiver em biblioteca compartilhada                            
                        } else {
                            $stmt = $conn->prepare("
                                SELECT f.*, u.username AS uploader_name
                                FROM uploaded_files f
                                JOIN users u ON f.uploaded_by = u.id
                                WHERE f.is_shared = TRUE
                                ORDER BY $orderBy
                            ");
                            $stmt->execute();
                        }
// informações da tabela
                        if ($stmt->rowCount() > 0) {
                            echo '<table class="table table-hover align-middle">';
                            echo '<thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Proprietário</th>
                                        <th>Tamanho</th>
                                        <th>Data</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                  </thead>
                                  <tbody>';
// faz a conversão do tipo do arq e do tamnho
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $fileTypeFriendly = getFriendlyFileType($row['file_type']);
                                $fileUrl = '/uploads/' . rawurlencode($row['file_name']);

                                echo '<tr>';
                                echo '<td>
                                        <span class="file-icon me-2">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </span>
                                        <a href="' . $fileUrl . '" target="_blank">' . htmlspecialchars($row['file_name']) . '</a>
                                    </td>';
                                echo '<td><span class="badge bg-light text-dark">' . htmlspecialchars($row['uploader_name']) . '</span></td>';
                                echo '<td>' . formatSizeUnits($row['file_size']) . '</td>';
                                echo '<td>' . date('d/m/Y H:i', strtotime($row['upload_date'])) . '</td>';
// Botão compartilhar (so se não estiver compartilhado e estiver na biblioteca pessoal)
                                echo '<td class="text-end">';

echo '<td class="text-end">';

// Botão Download
echo '<a href="/../../class/controlador.php?id=' . $row['id'] . '" class="btn btn-sm btn-outline-dark me-1" title="Download">
        <i class="bi bi-download"></i>
      </a>';

// Botão Compartilhar / Cancelar Compartilhamento (apenas na biblioteca pessoal)
if ($view === 'pessoal') {
    if (!$row['is_shared']) {
        // Não compartilhado, ícone verde para compartilhar
        echo '<a href="/../../class/controlador.php?id=' . $row['id'] . '" class="btn btn-sm btn-outline-success me-1" title="Compartilhar" onclick="return confirm(\'Deseja compartilhar este arquivo?\')">
                <i class="bi bi-share-fill"></i>
              </a>';
    } else {
// Ja compartilhado, ícone vermelho para cancelar compartilhamento
        echo '<a href="/../../class/controlador.php?id=' . $row['id'] . '&action=unshare" class="btn btn-sm btn-outline-danger me-1" title="Cancelar compartilhamento" onclick="return confirm(\'Deseja cancelar o compartilhamento deste arquivo?\')">
                <i class="bi bi-share-fill"></i>
              </a>';
    }
}

// Botão excluir
if ($view === 'pessoal') {
    if (!$row['is_shared']) {
echo '<a href="/../../class/controlador.php?id=' . $row['id'] . '" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm(\'Tem certeza que deseja excluir?\')">
        <i class="bi bi-trash"></i>
      </a>';

echo '</td>';
    }
}
// infos de erro
                                echo '</tr>';
                            }

                            echo '</tbody></table>';
                        } else {
                            echo '<div class="text-center py-4 text-muted">
                                    <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Nenhum arquivo encontrado</p>
                                  </div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-light">Erro ao carregar arquivos: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- COdigos em js -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- Toasts ---
    function showToast(toastId, queryParam) {
        const toast = document.getElementById(toastId);
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get(queryParam) === '1' && toast) {
            toast.style.display = 'block';
            toast.style.opacity = '1';

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 500);

                if (history.replaceState) {
                    const url = new URL(window.location.href);
                    url.searchParams.delete(queryParam);
                    history.replaceState(null, '', url);
                }
            }, 3000);
        }
    }

    showToast('uploadToast', 'success');
    showToast('errorToast', 'error');
    showToast('deleteToast', 'deleted');

// --- Sidebar toggle ---
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if(sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

   

    // --- Suporte: submenu com altura dinâmica corrigido ---
const supportMenuItem = document.querySelector('.support-menu-item');
const submenu = supportMenuItem?.querySelector('.support-submenu');
const toggleHeader = supportMenuItem?.querySelector('.menu-header');

if (supportMenuItem && submenu && toggleHeader) {
    toggleHeader.addEventListener('click', function () {
        const isActive = supportMenuItem.classList.contains('active');

        if (isActive) {
            // Força recálculo antes de animar o fechamento
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            requestAnimationFrame(() => {
                submenu.style.maxHeight = '0px';
            });
            supportMenuItem.classList.remove('active');
        } else {
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            supportMenuItem.classList.add('active');
        }
    });

        // Ajuste de altura ao redimensionar a janela
        window.addEventListener('resize', function () {
            if (supportMenuItem.classList.contains('active')) {
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
            }
        });
    }
});
function applySort() {
    const sortOrder = document.getElementById("sortOrder").value;
    const urlParams = new URLSearchParams(window.location.search);
    const currentView = urlParams.get("view") || "pessoal";

    // Reconstrói a URL preservando parâmetros já existentes
    urlParams.set("order", sortOrder);
    urlParams.set("view", currentView);

    window.location.search = urlParams.toString();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>