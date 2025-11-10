<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $table = 'folders';

    protected $fillable = [
        'name',
        'parent_id',
        'owner_id',
    ];

    /**
     * Dono da pasta (usuário que criou).
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Arquivos contidos nesta pasta.
     */
    public function files()
    {
        return $this->hasMany(UploadedFile::class, 'folder_id');
    }

    /**
     * Subpastas dentro desta pasta.
     */
    public function subfolders()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /**
     * Pasta pai (caso esta seja uma subpasta).
     */
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }
}
