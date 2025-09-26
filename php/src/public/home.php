<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cloud Library</title>
    <link rel="icon" href="../public/logos/lgo library.png" type="image/png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>

<div class="header">
    <div class="floating-title-logo">
        <h1 class="site-title">Cloud Library</h1>
        <div class="logo">
            <img src="/../../logos/lgo library.png" alt="Logo" />
        </div>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-container">
            <div class="user-info">
                <svg class="user-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z"/>
                </svg>
                <span class="username">Olá, <?= htmlspecialchars($username) ?></span>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php?action=logout'">Sair</button>
        </div>
    <?php endif; ?>
</div>

<!-- Sidebar toggle -->
<button class="sidebar-toggle"><i class="bi bi-list"></i></button>

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

<!-- Sidebar -->
  <?php //include __DIR__ . '../view/templates/sidebar.php'; ?> 
 

<!-- Main Content -->
<div class="main-content">
    <div class="dual-layout-container">
        <div class="left-side">
            <div class="help-container">
                <div class="help-area">
                    <h5 class="text-center mb-4">Olá! Esta é a <br><b>Cloud Library</b></h5>

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
                </div>
            </div>

            <div class="storage-container">
                <div class="storage-bar">
                    <div class="storage-info"><i class="bi bi-hdd me-2"></i> Espaço de Armazenamento</div>
                    <div class="storage-progress">
                        <div class="storage-progress-filled" style="width: <?= $diskUsage['used_percentage'] ?>%"></div>
                    </div>
                    <div class="storage-details">
                        <span><i class="bi bi-pie-chart me-1"></i> <?= File::formatSize($diskUsage['used']) ?></span>
                        <span><?= File::formatSize($diskUsage['total']) ?></span>
                    </div>
                </div>
            </div>

            <div class="info-container">
                <div class="info-area">
                    <h5><i class="bi bi-info-circle me-2"></i>Informações Importantes</h5>
                    <p>• Armazenamento seguro com criptografia</p>
                    <p>• Suporte a arquivos de até 2GB</p>
                    <p>• Compatível com todos os formatos comuns</p>
                </div>
            </div>
        </div>

        <div class="right-side">
            <div class="documents-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Arquivos Disponíveis &#x2601;&#xFE0F;</h5>
                    <div class="d-flex align-items-center gap-2">
                        <select id="sortOrder" class="form-select form-select-sm" style="width: 220px;" onchange="applySort()">
                            <option value="newest" <?= $order === 'newest' ? 'selected' : '' ?>>Últimos adicionados</option>
                            <option value="oldest" <?= $order === 'oldest' ? 'selected' : '' ?>>Primeiros adicionados</option>
                            <option value="largest" <?= $order === 'largest' ? 'selected' : '' ?>>Maior tamanho</option>
                            <option value="smallest" <?= $order === 'smallest' ? 'selected' : '' ?>>Menor tamanho</option>
                            <option value="az" <?= $order === 'az' ? 'selected' : '' ?>>Nome A → Z</option>
                            <option value="za" <?= $order === 'za' ? 'selected' : '' ?>>Nome Z → A</option>
                        </select>

                            <form id="uploadForm" action="/upload" method="post" enctype="multipart/form-data" class="mb-0">
                                <input type="file" id="fileToUpload" name="file" class="d-none" required />
                            <button type="button" class="upload-button" onclick="document.getElementById('fileToUpload').click()" title="Adicionar arquivo">
                                <i class="bi bi-plus-circle"></i>
                            </button>
                        </form>

<script>
    const fileInput = document.getElementById('fileToUpload');
    const uploadButton = document.querySelector('.upload-button');
    const form = document.getElementById('uploadForm');

    // Abre o seletor de arquivos
    uploadButton.addEventListener('click', () => fileInput.click());

    // Envia o formulário assim que o usuário escolher o arquivo
    fileInput.addEventListener('change', () => {
        if(fileInput.files.length > 0) {
            form.submit();
        }
    });
</script>

                    </div>
                </div>
        
                <div class="table-responsive">
                    <?php if (!empty($files)): ?>
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Proprietário</th>
                                    <th>Tamanho</th>
                                    <th>Data</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($files as $file): 
                                    $fileType = File::getFriendlyFileType($file['file_type']);
                                    $fileUrl = '/uploads/' . rawurlencode($file['file_name']);
                                ?>
                                    <tr>
                                        <td>
                                            <span class="file-icon me-2"><i class="bi bi-file-earmark-text"></i></span>
                                            <a href="<?= $fileUrl ?>" target="_blank"><?= htmlspecialchars($file['file_name']) ?></a>
                                        </td>
                                        <td><span class="badge bg-light text-dark"><?= htmlspecialchars($file['uploader_name']) ?></span></td>
                                        <td><?= File::formatSize($file['file_size']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($file['upload_date'])) ?></td>
                                        <td class="text-end">
                                            <a href="../class/controlador.php?id=<?= $file['id'] ?>" class="btn btn-sm btn-outline-dark me-1" title="Download"><i class="bi bi-download"></i></a>

                                            <?php if ($view === 'pessoal'): ?>
                                                <?php if (!$file['is_shared']): ?>
                                                    <a href="../class/controlador.php?id=<?= $file['id'] ?>" class="btn btn-sm btn-outline-success me-1" title="Compartilhar" onclick="return confirm('Deseja compartilhar este arquivo?')">
                                                        <i class="bi bi-share-fill"></i>
                                                    </a>
                                                    <a href="../class/controlador.php?id=<?= $file['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="../class/controlador.php?id=<?= $file['id'] ?>&action=unshare" class="btn btn-sm btn-outline-danger me-1" title="Cancelar compartilhamento" onclick="return confirm('Deseja cancelar o compartilhamento deste arquivo?')">
                                                        <i class="bi bi-share-fill"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
                            <p class="mt-2">Nenhum arquivo encontrado</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
