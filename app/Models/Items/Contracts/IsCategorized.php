<?php

namespace App\Models\Items\Contracts;

interface IsCategorized
{
    /**
     * category/type as string
     *
     * @return string   type/category
     * @throws \ErrorException
     */
    public function type() : string;
}