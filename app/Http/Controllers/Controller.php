<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * decode a JSON file
     *
     * @param  [string] $filepath filepath of JSON file
     *
     * @return Object    Decoded JSON
     */
    public function decodeJsonFromFile($filepath, bool $toArray=false)
    {
        return json_decode(file_get_contents($filepath), $toArray);
    }
}
