<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cloud Library ☁️</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>

    <!-- ============================ LAYOUT PRINCIPAL ============================ -->
    <div class="layout-container">

        <!-- ============================ SIDEBAR (ESQUERDA) ============================ -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <svg class="user-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z"/>
                </svg>
                <span class="username">Bem-vindo, {{ auth()->user()->username }}!</span>
            </div>

            <nav class="library-nav">
                <button class="nav-btn active" id="btn-shared" onclick="showLibrary('shared')">
                    <i class="bi bi-folder2-open me-2"></i> Biblioteca Compartilhada
                </button>
                <button class="nav-btn" id="btn-personal" onclick="showLibrary('personal')">
                    <i class="bi bi-folder me-2"></i> Biblioteca Pessoal
                </button>
            </nav>

            <div class="storage-section">
                <h3><i class="bi bi-hdd me-2"></i> Espaço de Armazenamento</h3>
                <div class="storage-progress">
                    <div class="storage-progress-filled" style="width: 45%;"></div>
                </div>
                <p class="storage-details">
                    <i class="bi bi-pie-chart me-1"></i> 450MB de 1GB usados
                </p>
            </div>

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

            <header class="main-header">
                <div class="user-info">
                    <h1 class="site-title">Cloud Library</h1>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Sair</button>
                </form>
            </header>

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

                <!-- ============================ DIV - BIBLIOTECA COMPARTILHADA ============================ -->
                <div id="shared" class="library-content">
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
                                @forelse($files->where('is_shared', true) as $file)
                                    <tr>
                                        <td data-label="Nome">
                                            <!-- Visualizar arquivo (abre em nova aba) -->
                                            <a href="{{ route('download', ['id' => $file->id, 'preview' => true]) }}" target="_blank">
                                                {{ $file->file_name }}
                                            </a>
                                        </td>
                                        <td data-label="Tamanho">
                                            {{ \Illuminate\Support\Number::fileSize($file->file_size) }}
                                        </td>

                                        <td data-label="Tipo">{{ $file->file_type }}</td>
                                        <td data-label="Data de Upload">{{ $file->upload_date }}</td>
                                        <td data-label="Dono">
                                            {{ $file->owner ? $file->owner->apelido ?? $file->owner->username : 'Desconhecido' }}
                                        </td>
                                        <td data-label="Ações" class="file-actions">
                                            <a href="{{ route('download', ['id' => $file->id]) }}" class="action-btn download" title="Baixar">⬇</a>

                                            <form action="{{ route('files.share', $file->id) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  onsubmit="return confirmShare('{{ $file->is_shared ? 'descompartilhar' : 'compartilhar' }}')">
                                                @csrf
                                                <button type="submit"
                                                        class="action-btn share"
                                                        title="{{ $file->is_shared ? 'Descompartilhar' : 'Compartilhar' }}">
                                                    ⤴
                                                </button>
                                            </form>

                                            <form action="{{ route('files.delete', $file->id) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn delete" title="Excluir">🗑</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #999;">
                                            Nenhum arquivo compartilhado encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ============================ DIV - BIBLIOTECA PESSOAL ============================ -->
                <div id="personal" class="library-content" style="display:none;">
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
                                @forelse($files->where('uploaded_by', auth()->user()->id) as $file)
                                    <tr>
                                        <td data-label="Nome">
                                            <a href="{{ route('download', ['id' => $file->id, 'preview' => true]) }}" target="_blank">
                                                {{ $file->file_name }}
                                            </a>
                                        </td>
                                        <td data-label="Tamanho">
                                            {{ \Illuminate\Support\Number::fileSize($file->file_size) }}
                                        </td>
                                        <td data-label="Tipo">{{ $file->file_type }}</td>
                                        <td data-label="Data de Upload">{{ $file->upload_date }}</td>
                                        <td data-label="Dono">
                                            {{ $file->owner ? $file->owner->apelido ?? $file->owner->username : 'Desconhecido' }}
                                        </td>
                                        <td data-label="Ações" class="file-actions">
                                            <a href="{{ route('download', ['id' => $file->id]) }}" 
                                               class="action-btn download" 
                                               title="Baixar"
                                               onclick="return confirm('Deseja baixar este arquivo?')">
                                                ⬇
                                            </a>

                                            <form action="{{ route('files.share', $file->id) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  onsubmit="return confirmShare('{{ $file->is_shared ? 'descompartilhar' : 'compartilhar' }}')">
                                                @csrf
                                                <button type="submit"
                                                        class="action-btn share"
                                                        title="{{ $file->is_shared ? 'Descompartilhar' : 'Compartilhar' }}">
                                                    ⤴
                                                </button>
                                            </form>

                                            <form action="{{ route('files.delete', $file->id) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn delete" title="Excluir">🗑</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #999;">
                                            Nenhum arquivo pessoal encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
        </main>

    </div> <!-- fim layout-container -->

    <!-- ============================ SCRIPT PARA TROCA DE ABAS E CONFIRMAÇÃO ============================ -->
    <script>
        function showLibrary(tab) {
            document.querySelectorAll('.library-content').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tab).style.display = 'block';
            if (tab === 'personal') {
                document.getElementById('btn-personal').classList.add('active');
            } else {
                document.getElementById('btn-shared').classList.add('active');
            }
        }

        function confirmShare(action) {
            return confirm(`Tem certeza que deseja ${action} este arquivo?`);
        }

        showLibrary('shared');
    </script>

</body>
</html>
