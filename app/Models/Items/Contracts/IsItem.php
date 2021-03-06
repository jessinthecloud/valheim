<?php

namespace App\Models\Items\Contracts;

interface IsItem
{
    public function sharedData();
    public function hasRecipes() : bool;
    public function hasSharedData() : bool;
    public function isWeapon() : bool;
    public function isArmor() : bool;
    public function isConsumable() : bool;
    public function isFood() : bool;
}