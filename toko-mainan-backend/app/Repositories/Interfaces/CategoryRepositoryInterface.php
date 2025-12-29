<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getWithProducts();
    public function findBySlug($slug);
}
