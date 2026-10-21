<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class StokService
{
    public static function kurangiStok(int $menuId, int $qty): void
    {
        DB::transaction(function () use ($menuId, $qty) {
            $menu = Menu::lockForUpdate()->findOrFail($menuId);
            $menu->stok = max(0, $menu->stok - $qty);
            $menu->save();
        });
    }
}
