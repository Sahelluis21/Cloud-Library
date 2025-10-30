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
        <!-- Logo  -->
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
            <!-- Filtro de ordenação -->
            <select id="sortOrder" class="form-select">
                <option value="newest">Últimos adicionados</option>
                <option value="oldest">Primeiros adicionados</option>
                <option value="largest">Maior tamanho</option>
                <option value="smallest">Menor tamanho</option>
                <option value="az">Nome A → Z</option>
                <option value="za">Nome Z → A</option>
            </select>

            <!-- Formulário de upload -->
            <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="arquivo" required>
                <button type="submit">Enviar</button>
            </form>

            @if (session('success'))
                <p style="color:green;">{{ session('success') }}</p>
            @endif
        </div>
    </div>

    <!-- Lista de arquivos -->
<div class="file-table-container">
    <table class="file-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Tamanho</th>
                <th>Tipo</th>
                <th>Data de Upload</th>
                <th>Dono</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($files as $file)
                <tr>
                    <td data-label="Nome">
                        <a href="{{ asset($file->file_path) }}" target="_blank">
                            {{ $file->file_name }}
                        </a>
                    </td>
                    <td data-label="Tamanho">
                        {{ number_format($file->file_size / 1024, 2) }} KB
                    </td>
                    <td data-label="Tipo">{{ $file->file_type }}</td>
                    <td data-label="Data de Upload">{{ $file->upload_date }}</td>
                    <td data-label="Dono">
                        {{ $file->owner ? $file->owner->name : 'Desconhecido' }}
                    </td>
                   <td data-label="Ações" class="file-actions">
                        {{-- Botão de download --}}
                        <a href="{{ asset($file->file_path) }}" 
                        download 
                        class="action-btn download" 
                        title="Baixar">⬇</a>

                        {{-- Botão de compartilhamento (exemplo, caso já exista lógica) --}}
                        <button class="action-btn share" title="Compartilhar">⤴</button>

                        {{-- Formulário para exclusão --}}
                        <form action="{{ route('files.delete', $file->id) }}" 
                            method="POST" 
                            style="display: inline;" 
                            onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="action-btn delete" 
                                    title="Excluir">🗑</button>
                        </form>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">
                        Nenhum arquivo encontrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</section>


</body>
</html>
