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
    
    $request->validate([
        'arquivo' => 'required|file|max:3145728', 
    ]);

    
    $file = $request->file('arquivo');

    
    $fileName = time() . '_' . $file->getClientOriginalName();
    $destination = public_path('uploads');

    
    $file->move($destination, $fileName);

    
    $filePath = 'uploads/' . $fileName;
    $fileSize = $file->getSize(); 
    $fileType = $file->getClientMimeType();
    $uploadDate = now();
    $uploadedBy = Auth::check() ? Auth::user()->id : null;
    $isShared = false;

    
    DB::table('uploaded_files')->insert([
        'file_name'   => $fileName,
        'file_path'   => $filePath,
        'file_size'   => $fileSize,
        'file_type'   => $fileType,
        'upload_date' => $uploadDate,
        'uploaded_by' => $uploadedBy,
        'is_shared'   => $isShared,
    ]);

   
    return back()->with('success', 'Arquivo enviado com sucesso!');
}

}