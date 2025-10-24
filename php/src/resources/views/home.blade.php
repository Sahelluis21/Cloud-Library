<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cloud Library</title>
</head>
<body>

<!-- ============================ CABEÇALHO ============================ -->
<div class="header">
    <div class="floating-title-logo">
        <h1 class="site-title">Cloud Library</h1>

        <!-- logo -->
        <div class="logo">
            <img src="logos/lgo library.png" alt="Logo" />
        </div>
    </div>

    <div class="user-container">
        <div class="user-info">
            <!-- usuário -->
            <svg class="user-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z"/>
            </svg>

            <!--  usuário logado -->
            <span class="username"> Bem vindo {{ auth()->user()->username }}! </span>
        </div>

        <!-- logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Sair</button>
        </form>
    </div>
</div>

<!-- ============================ (SIDEBAR) ============================ -->
<button class="sidebar-toggle"><i class="bi bi-list"></i></button>

<!-- ============================ PRINCIPAL ============================ -->
<div class="main-content">
    <div class="dual-layout-container">

        <!-- ============================ LADO ESQUERDO ============================ -->
        <div class="left-side">
            <div class="help-container">
                <div class="help-area">
                    <h5 class="text-center mb-4">Olá! Esta é a <br><b>Cloud Library</b></h5>

                    <!--  rotas "biblioteca" (pessoal/compartilhada) -->
                    <div class="d-flex flex-column gap-2">
                        <button class="btn w-100 btn-outline-secondary">
                            <i class="bi bi-folder2-open me-2"></i> Biblioteca Compartilhada
                        </button>

                        <button class="btn w-100 btn-outline-primary">
                            <i class="bi bi-folder me-2"></i> Biblioteca Pessoal
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================ BARRA DE ARMAZENAMENTO ============================ -->
            <div class="storage-container">
                <div class="storage-bar">
                    <div class="storage-info"><i class="bi bi-hdd me-2"></i> Espaço de Armazenamento</div>

                    <!--  porcentagem de uso -->
                    <div class="storage-progress">
                        <div class="storage-progress-filled" style="width: 45%;"></div>
                    </div>

                    <!--Descrição -->
                    <div class="storage-details">
                        <span><i class="bi bi-pie-chart me-1"></i> 450MB de 1GB usados</span>
                    </div>
                </div>
            </div>

            <!-- ============================ INFORMAÇÕES FIXAS ============================ -->
            <div class="info-container">
                <div class="info-area">
                    <h5><i class="bi bi-info-circle me-2"></i>Informações Importantes</h5>
                    <p>• Armazenamento seguro com criptografia</p>
                    <p>• Suporte a arquivos de até 2GB</p>
                    <p>• Compatível com todos os formatos comuns</p>
                </div>
            </div>
        </div>

        <!-- ============================ LADO DIREITO ============================ -->
        <div class="right-side">
            <div class="documents-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Arquivos Disponíveis ☁️</h5>

                    <div class="d-flex align-items-center gap-2">
                        <!-- ⚙️ Filtro de ordenação -->
                        <select id="sortOrder" class="form-select form-select-sm" style="width: 220px;">
                            <option value="newest">Últimos adicionados</option>
                            <option value="oldest">Primeiros adicionados</option>
                            <option value="largest">Maior tamanho</option>
                            <option value="smallest">Menor tamanho</option>
                            <option value="az">Nome A → Z</option>
                            <option value="za">Nome Z → A</option>
                        </select>

                        <!-- formulário de upload (rota Laravel) -->
                        <form id="uploadForm" action="#" method="post" enctype="multipart/form-data" class="mb-0">
                            <input type="file" id="fileToUpload" name="file" class="d-none" required />
                            <button type="button" class="upload-button" onclick="document.getElementById('fileToUpload').click()" title="Adicionar arquivo">
                                <i class="bi bi-plus-circle"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Aqui será listado os arquivos (ex: foreach Blade) -->
                <div class="file-list">
                    <p>Nenhum arquivo encontrado.</p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
