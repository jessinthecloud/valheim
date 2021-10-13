<?php

namespace App\Converters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class JsonConverter extends FileConverter implements Converter
{
    /**
     * @required by Converter Interface 
     *          
     * Decode JSON 
     * Loop data
     * Insert into tables
     * Attach relationships
     */
    public function convert()
    {
        $this->data = $this->decode($this->contents);
        
        return $this->data;
    }

    /**
     * Remove invalid hex characters from a string
     *
     * @param  string $string string to sanitize
     *
     * @return string         sanitized string
     */
    public function removeInvalidHex(string $string) : string
    {
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    }

    /**
     * JSON decode
     * 
     * @param string $contents
     * @param bool   $associative
     *
     * @return mixed
     */
    public function decode(string $contents, bool $associative=true)
    {
        return json_decode(
            $this->removeInvalidHex($contents), 
            $associative
        );
    }
}