<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    use HasFactory;

    protected $table = 'uploaded_files';

    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'upload_date',
        'uploaded_by',
        'folder_id', // 🔹 nova coluna adicionada
    ];

    public $timestamps = false;

    /**
     * Dono do arquivo (usuário que enviou).
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Pasta à qual este arquivo pertence.
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    /**
     * Exclui o arquivo físico e o registro no banco.
     */
    public function deleteFile()
    {
        $fullPath = public_path($this->file_path);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $this->delete();
    }
}
