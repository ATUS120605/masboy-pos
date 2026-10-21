<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['kategori_id', 'nama_menu', 'harga', 'stok', 'foto'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class);
    }
}
