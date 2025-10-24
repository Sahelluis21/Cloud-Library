<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cloud Library</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>

<!-- ============================ LAYOUT PRINCIPAL ============================ -->
<div class="layout-container">

    <!-- ============================ SIDEBAR (ESQUERDA) ============================ -->
    <aside class="sidebar">
        <!-- Logo e título -->
        <div class="sidebar-header">
            <h1 class="site-title">Cloud Library</h1>
        </div>

        <!-- Abas da biblioteca -->
        <nav class="library-nav">
            <button class="nav-btn active">
                <i class="bi bi-folder2-open me-2"></i> Biblioteca Compartilhada
            </button>
            <button class="nav-btn">
                <i class="bi bi-folder me-2"></i> Biblioteca Pessoal
            </button>
        </nav>

        <!-- Espaço de armazenamento -->
        <div class="storage-section">
            <h3><i class="bi bi-hdd me-2"></i> Espaço de Armazenamento</h3>
            <div class="storage-progress">
                <div class="storage-progress-filled" style="width: 45%;"></div>
            </div>
            <p class="storage-details"><i class="bi bi-pie-chart me-1"></i> 450MB de 1GB usados</p>
        </div>

        <!-- Informações fixas -->
        <div class="info-section">
            <h3><i class="bi bi-info-circle me-2"></i> Informações Importantes</h3>
            <ul>
                <li>Armazenamento seguro com criptografia</li>
                <li>Suporte a arquivos de até 2GB</li>
                <li>Compatível com todos os formatos comuns</li>
            </ul>
        </div>
    </aside>

    <!-- ============================ ÁREA PRINCIPAL (DIREITA) ============================ -->
    <main class="main-content">

        <!-- Cabeçalho do usuário -->
        <header class="main-header">
            <div class="user-info">
                <svg class="user-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z"/>
                </svg>
                <span class="username">Bem-vindo, {{ auth()->user()->username }}!</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Sair</button>
            </form>
        </header>

        <!-- Conteúdo principal -->
        <section class="documents-container">
            <div class="documents-header">
                <h2>Arquivos Disponíveis ☁️</h2>

                <div class="documents-controls">
                    <select id="sortOrder" class="form-select">
                        <option value="newest">Últimos adicionados</option>
                        <option value="oldest">Primeiros adicionados</option>
                        <option value="largest">Maior tamanho</option>
                        <option value="smallest">Menor tamanho</option>
                        <option value="az">Nome A → Z</option>
                        <option value="za">Nome Z → A</option>
                    </select>

                    <form id="uploadForm" action="#" method="post" enctype="multipart/form-data">
                        <input type="file" id="fileToUpload" name="file" class="d-none" required />
                        <button type="button" class="upload-button" onclick="document.getElementById('fileToUpload').click()" title="Adicionar arquivo">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Lista de arquivos -->
            <div class="file-list">
                <p>Nenhum arquivo encontrado.</p>
            </div>
        </section>
    </main>
</div>

</body>
</html>
