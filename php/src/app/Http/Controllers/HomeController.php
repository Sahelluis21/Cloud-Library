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
        
        $usedStorage = UploadedFile::where('uploaded_by', $userId)->sum('file_size');
        $storageLimit = 5 * 1024 * 1024 * 1024; // 5GB
        $percentage = ($usedStorage / $storageLimit) * 100;
        // Busca apenas arquivos do usuário ou compartilhados
        $files = UploadedFile::with('owner')
            ->where(function($query) use ($userId) {
                $query->where('uploaded_by', $userId)
                      ->orWhere('is_shared', true);
            })
            ->orderBy('upload_date', 'desc')
            ->get();

        return view('home', compact('files', 'usedStorage', 'storageLimit', 'percentage'));
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

        return back()->with('upload', 'Arquivo enviado com sucesso!');
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

        return back()->with('delete', 'Arquivo excluído com sucesso!');
    }


    public function download(Request $request, $id)
    {
        $file = UploadedFile::findOrFail($id);

        // Validação de permissão
        if (!$file->is_shared && Auth::id() !== $file->uploaded_by) {
            abort(403, 'Você não tem permissão para acessar este arquivo.');
        }

        // Caminho físico
        $filePath = storage_path('uploads/user_' . $file->uploaded_by . '/' . $file->file_name);

        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        // Se veio ?preview=true, mostra inline no navegador
        if ($request->has('preview')) {
            return response()->file($filePath, [
                'Content-Type' => $file->file_type,
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"'
            ]);
        }

        // Caso contrário, força download
        return response()->download($filePath, $file->file_name);
    }


    public function preview($id)
    {
            $file = UploadedFile::findOrFail($id);

        // Permissão: apenas dono ou compartilhado
        if (!$file->is_shared && Auth::id() !== $file->uploaded_by) {
            abort(403, 'Você não tem permissão para visualizar este arquivo.');
        }

        // Caminho real do arquivo
        $filePath = storage_path('uploads/user_' . $file->uploaded_by . '/' . $file->file_name);

        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        // URL segura para o iframe (mesma função do download, mas inline)
        $fileUrl = route('files.download', ['id' => $file->id, 'preview' => true]);

        return view('preview', compact('file', 'fileUrl'));
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

       return back()->with('share', $file->is_shared 
            ? 'Arquivo compartilhado com sucesso!' 
            : 'Arquivo descompartilhado com sucesso!');

    }

    
}

