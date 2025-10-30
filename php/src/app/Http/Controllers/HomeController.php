<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    /**
     * Lista os arquivos do usuário logado e arquivos compartilhados
     */
    public function index()
    {
        $userId = auth()->id();

        // Busca apenas arquivos do usuário ou compartilhados
        $files = UploadedFile::with('owner')
            ->where(function($query) use ($userId) {
                $query->where('uploaded_by', $userId)
                      ->orWhere('is_shared', true);
            })
            ->orderBy('upload_date', 'desc')
            ->get();

        return view('home', compact('files'));
    }

    /**
     * Faz upload de um arquivo
     */
    public function upload(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|max:3145728', // até 3MB
        ]);

        $file = $request->file('arquivo');
        $uploadedBy = auth()->id(); // ID do usuário logado

        // Pasta base do usuário
        $userFolder = storage_path('uploads/user_' . $uploadedBy);
        if (!file_exists($userFolder)) {
            mkdir($userFolder, 0755, true);
        }

        // Nome único para evitar conflitos
        $timestamp = time();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = $originalName . '_' . $timestamp . '.' . $extension;

        // Caminho final
        $file->move($userFolder, $fileName);
        $relativePath = 'storage/uploads/user_' . $uploadedBy . '/' . $fileName;

        // Inserir no banco
        UploadedFile::create([
            'file_name'   => $fileName,
            'file_path'   => $relativePath,
            'file_size'   => filesize($userFolder . '/' . $fileName),
            'file_type'   => $file->getClientMimeType(),
            'upload_date' => now(),
            'uploaded_by' => $uploadedBy,
            'is_shared'   => false,
        ]);

        return back()->with('success', 'Arquivo enviado com sucesso!');
    }

    /**
     * Deleta um arquivo (somente se for dono)
     */
    public function delete($id)
    {
        $file = UploadedFile::findOrFail($id);

        // Verifica se o usuário logado é o dono
        if ($file->uploaded_by != auth()->id()) {
            abort(403, 'Acesso negado');
        }

        $file->deleteFile();

        return back()->with('success', 'Arquivo excluído com sucesso!');
    }

    /**
     * Faz download de um arquivo (somente se for dono ou compartilhado)
     */
    public function download($id)
    {
        $file = UploadedFile::findOrFail($id);

        if ($file->uploaded_by != auth()->id() && !$file->is_shared) {
            abort(403, 'Acesso negado');
        }

        $path = storage_path('uploads/user_' . $file->uploaded_by . '/' . $file->file_name);

        if (!file_exists($path)) {
            abort(404, 'Arquivo não encontrado');
        }

        return response()->download($path, $file->file_name);
    }

    /**
     * Alterna o estado de compartilhamento do arquivo (somente dono)
     */
    public function toggleShare($id)
    {
        $file = UploadedFile::findOrFail($id);

        if ($file->uploaded_by != auth()->id()) {
            abort(403, 'Acesso negado');
        }

        $file->is_shared = !$file->is_shared;
        $file->save();

        return back()->with('success', 'Status de compartilhamento atualizado!');
    }

    public function share($id)
{
    // Pega o arquivo, apenas se ele pertence ao usuário logado
    $file = UploadedFile::where('id', $id)
                        ->where('uploaded_by', auth()->user()->id)
                        ->firstOrFail();

    // Alterna o valor de is_shared
    $file->is_shared = !$file->is_shared;
    $file->save();

    return back()->with('success', 'Arquivo atualizado com sucesso!');
}

}

