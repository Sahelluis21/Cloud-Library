{{-- resources/views/folders/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>📁 {{ $folder->name }}</h2>
    <a href="{{ route('folders.index') }}">⬅️ Voltar</a>

    {{-- Subpastas --}}
    <h3>Subpastas</h3>
    @if ($subfolders->isEmpty())
        <p>Nenhuma subpasta aqui.</p>
    @else
        <ul>
            @foreach ($subfolders as $subfolder)
                <li>
                    <a href="{{ route('folders.show', $subfolder->id) }}">
                        📂 {{ $subfolder->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Arquivos --}}
    <h3>Arquivos</h3>
    @if ($files->isEmpty())
        <p>Nenhum arquivo nesta pasta.</p>
    @else
        <ul>
            @foreach ($files as $file)
                <li>📄 {{ $file->file_name }}</li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
