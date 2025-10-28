<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
   
public function index()
{
    $files = UploadedFile::with('owner')->orderBy('upload_date','desc')->get();

    return view('home', compact('files'));
}



public function upload(Request $request)
{
    // Validação do arquivo
    $request->validate([
        'arquivo' => 'required|file|max:3145728', // até 3MB
    ]);

    $file = $request->file('arquivo');

    // Usuário logado
    $uploadedBy = Auth::check() ? Auth::user()->id : null;

    // Pasta base de uploads
    $basePath = storage_path('uploads');

    // Pasta específica do usuário (ex: uploads/user_5/)
    $userFolder = $basePath . '/user_' . $uploadedBy;

    // Cria a pasta do usuário se não existir
    if (!file_exists($userFolder)) {
        mkdir($userFolder, 0755, true);
    }

    // Gera nome único para evitar duplicatas
    $timestamp = time();
    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $extension = $file->getClientOriginalExtension();
    $fileName = $originalName . '_' . $timestamp . '.' . $extension;

    // Caminho final do arquivo
    $destination = $userFolder;
    $file->move($destination, $fileName);

    // Caminho completo e relativo
    $filePathFull = $destination . '/' . $fileName;
    $relativePath = 'storage/uploads/user_' . $uploadedBy . '/' . $fileName;

    // Metadados do arquivo
    $fileSize = filesize($filePathFull);
    $fileType = $file->getClientMimeType();
    $uploadDate = now();
    $isShared = false;

    // Inserir no banco de dados
    DB::table('uploaded_files')->insert([
        'file_name'   => $fileName,
        'file_path'   => $relativePath,
        'file_size'   => $fileSize,
        'file_type'   => $fileType,
        'upload_date' => $uploadDate,
        'uploaded_by' => $uploadedBy,
        'is_shared'   => $isShared,
    ]);

    return back()->with('success', 'Arquivo enviado com sucesso!');
}




}