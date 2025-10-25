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
}
