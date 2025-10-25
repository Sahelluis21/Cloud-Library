<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
   
public function index()
{
    return view('home'); // ou o nome correto da sua view
}


public function upload(Request $request)
{
    // Validação do arquivo
    $request->validate([
        'arquivo' => 'required|file|max:3145728', // até 3MB
    ]);

    $file = $request->file('arquivo');

    // Criar pasta destino se não existir
    $destination = public_path('uploads');
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }

    // Gerar nome único para evitar duplicatas
    $timestamp = time();
    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $extension = $file->getClientOriginalExtension();
    $fileName = $originalName . '_' . $timestamp . '.' . $extension;

    // Mover arquivo para destino
    $file->move($destination, $fileName);

    // Caminho completo do arquivo final
    $filePathFull = $destination . '/' . $fileName;
    $fileSize = filesize($filePathFull); // tamanho seguro
    $fileType = $file->getClientMimeType();

    // Informações adicionais
    $uploadDate = now();
    $uploadedBy = Auth::check() ? Auth::user()->id : null;
    $isShared = false;

    // Inserir no banco de dados
    DB::table('uploaded_files')->insert([
        'file_name'   => $fileName,
        'file_path'   => 'uploads/' . $fileName,
        'file_size'   => $fileSize,
        'file_type'   => $fileType,
        'upload_date' => $uploadDate,
        'uploaded_by' => $uploadedBy,
        'is_shared'   => $isShared,
    ]);

    return back()->with('success', 'Arquivo enviado com sucesso!');
}



}