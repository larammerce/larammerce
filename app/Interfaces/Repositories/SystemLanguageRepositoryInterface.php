<?php

namespace App\Interfaces\Repositories;

interface SystemLanguageRepositoryInterface
{
    public function findByShortName(string $value);

    public function findByName(string $value);

    public function update(int $id, array $record);

    public function create(array $record);

    public function all();

}
