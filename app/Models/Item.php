<?php

namespace App\Models;

use App\Models\ItemSharedData;
use App\Http\Controllers\JsonAdapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public string $name; // internalID - as seen on wiki
    public $data; // ItemData -- not used, is not instanced from game
    public $shared_data; // ItemSharedData

    public function __construct($data=null/*string $name='', ItemSharedData $shared_data=null*/)
    {
        $this->name = $data['name'] ?? null;
        $this->data = $data['itemData'] ?? null;
        $this->shared_data = $data['shared'] ?? null;
    }
}
