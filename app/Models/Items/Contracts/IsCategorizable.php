<?php

namespace App\Models\Items\Contracts;

interface IsCategorizable
{
    /**
     * category/type as string
     *
     * @return string   type/category
     * @throws \ErrorException
     */
    public function type() : string;
}