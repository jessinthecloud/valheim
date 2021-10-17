<?php

namespace App\Models\Items\Contracts;

interface IsCraftable
{
    /**
     * items can have multiple recipes for their variants
     * e.g., Bronze and 5 Bronze bars
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipes();
}