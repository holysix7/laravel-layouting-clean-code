<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AppLayout;

class AppLayoutBody extends Model
{
    protected $table = 'app_layout_bodies';
    use HasFactory;

    public function app_layout(){
        return $this->belongsTo(AppLayout::class, 'id', 'app_layout_id');
    }
}
