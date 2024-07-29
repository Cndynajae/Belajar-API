<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemain extends Model
{
    use HasFactory;
    protected $fillabale = ['nama_pemain', 'posisi', 'foto', 'tg_lahir', 'harga_pasar', 'id_klub'];

    public function klub()
    {
        return $this->belongsTo(Klub::class, 'id_klub');
    }
}
