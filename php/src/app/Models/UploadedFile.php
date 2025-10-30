<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    protected $table = 'uploaded_files';

    protected $fillable = [

        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'upload_date',
        'uploaded_by',
    ];

    public $timestamps = false;

    public function owner() 
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');    
    }

    public function deleteFile()
    {
        $fullPath = public_path($this->file_path);

        // Exclui o arquivo físico se existir
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Exclui o registro no banco
        $this->delete();
    }
}

