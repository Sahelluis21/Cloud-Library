<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    /**
     * Exibe todas as pastas do usuário logado (nível raiz)
     */
    public function index()
   {
        $userId = Auth::id();

        // Busca as pastas de nível raiz do usuário
        $folders = Folder::where('owner_id', $userId)
            ->whereNull('parent_id')
            ->get();

        // Busca também os arquivos do usuário
        $files = UploadedFile::where('uploaded_by', $userId)->get();

        return view('home', compact('folders', 'files'));
     }

    /**
     * Cria uma nova pasta
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id'
        ]);

        Folder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'owner_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pasta criada com sucesso!');
    }

    /**
     * Mostra o conteúdo de uma pasta (subpastas + arquivos)
     */
    public function show($id)
    {
        $folder = Folder::findOrFail($id);
        $subfolders = Folder::where('parent_id', $folder->id)->get();
        $files = UploadedFile::where('folder_id', $folder->id)->get();

        return view('folders.show', compact('folder', 'subfolders', 'files'));
    }


    /**
     * Exclui uma pasta (e tudo dentro dela)
     */
    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);

        if ($folder->owner_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $folder->delete();

        return redirect()->back()->with('success', 'Pasta excluída com sucesso!');
    }
}
