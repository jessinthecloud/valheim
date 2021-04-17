<?php

namespace App\Models;

use App\Http\Controllers\JsonAdapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CraftingStation extends Model
{
    use HasFactory;

    public string $name;
    public string $name_EN;

    public function __construct($data=null/*$name=null, $name_EN=null*/)
    {
        $this->name = $data['name'] ?? '';
        $this->name_EN = $data['name_EN'] ?? $this->name;
    }
}
