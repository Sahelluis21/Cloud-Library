{{-- resources/views/folders/index.blade.php --}}
@extends('home')

@section('content')
<div class="container">
    <h2>Minhas Pastas</h2>

    {{-- Formulário para criar nova pasta --}}
    <form action="{{ route('folders.store') }}" method="POST" style="margin-bottom: 20px;">
        @csrf
        <input type="text" name="name" placeholder="Nome da nova pasta" required>
        <button type="submit">Criar Pasta</button>
    </form>

    {{-- Mensagem de sucesso --}}
    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    {{-- Listagem de pastas --}}
    @if($folders->isEmpty())
        <p>Nenhuma pasta criada ainda.</p>
    @else
        <ul>
            @foreach ($folders as $folder)
                <li>
                    <a href="{{ route('folders.show', $folder->id) }}">
                        📁 {{ $folder->name }}
                    </a>
                    <form action="{{ route('folders.destroy', $folder->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Excluir esta pasta e todo o conteúdo?')">🗑️</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
