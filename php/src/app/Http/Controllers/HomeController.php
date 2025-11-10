<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Lista os arquivos e pastas do usuário logado
     */
    public function index()
    {
        $userId = auth()->id();

        // Pastas do usuário (somente raiz)
        $folders = Folder::where('owner_id', $userId)
                        ->whereNull('parent_id')
                        ->get();

        // Arquivos do usuário (não em pasta)
        $uploadedFiles = UploadedFile::with('owner')
            ->where('uploaded_by', $userId)
            ->whereNull('folder_id')
            ->orderBy('upload_date', 'desc')
            ->get();

        // Arquivos compartilhados de todos os usuários
        $sharedFiles = UploadedFile::with('owner')
            ->where('is_shared', true)
            ->orderBy('upload_date', 'desc')
            ->get();

        return view('home', compact('folders', 'uploadedFiles', 'sharedFiles'));
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
        $uploadedBy = auth()->id();

        $userFolder = storage_path('uploads/user_' . $uploadedBy);
        if (!file_exists($userFolder)) {
            mkdir($userFolder, 0755, true);
        }

        $timestamp = time();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = $originalName . '_' . $timestamp . '.' . $extension;

        $file->move($userFolder, $fileName);
        $relativePath = 'storage/uploads/user_' . $uploadedBy . '/' . $fileName;

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

        if ($file->uploaded_by != auth()->id()) {
            abort(403, 'Acesso negado');
        }

        $file->deleteFile();

        return back()->with('success', 'Arquivo excluído com sucesso!');
    }

    /**
     * Download e pré-visualização
     */
    public function download(Request $request, $id)
    {
        $file = UploadedFile::findOrFail($id);

        if (!$file->is_shared && Auth::id() !== $file->uploaded_by) {
            abort(403, 'Você não tem permissão para acessar este arquivo.');
        }

        $filePath = storage_path('uploads/user_' . $file->uploaded_by . '/' . $file->file_name);

        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        if ($request->has('preview')) {
            return response()->file($filePath, [
                'Content-Type' => $file->file_type,
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"'
            ]);
        }

        return response()->download($filePath, $file->file_name);
    }

    /**
     * Visualização inline via iframe
     */
    public function preview($id)
    {
        $file = UploadedFile::findOrFail($id);

        if (!$file->is_shared && Auth::id() !== $file->uploaded_by) {
            abort(403, 'Você não tem permissão para visualizar este arquivo.');
        }

        $filePath = storage_path('uploads/user_' . $file->uploaded_by . '/' . $file->file_name);

        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

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
}
