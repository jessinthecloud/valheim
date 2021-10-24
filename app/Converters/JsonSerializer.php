<?php

namespace App\Converters;

class JsonSerializer
{
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
}