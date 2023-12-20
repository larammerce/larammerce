<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\Repositories\SystemLanguageRepositoryInterface;
use App\Models\SystemLanguage;
use Illuminate\Database\Eloquent\Builder;

class SystemLanguageRepositoryEloquent implements SystemLanguageRepositoryInterface
{
    private Builder $query;

    public function __construct()
    {
        $this->query = SystemLanguage::query();
    }

    /**
     * @param int $id
     * @param $record
     * @return mixed
     */
    public function update(int $id, array $record)
    {
        return $this->query->find($id)
            ->first()
            ->update($record);
    }

    /**
     * @param int $id
     * @param $record
     * @return mixed
     */
    public function create($record)
    {
        return $this->query->create($record);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return SystemLanguage::all();
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function findByShortName(string $value)
    {
        return $this->query->where('short_name', $value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function findByName(string $value)
    {
        return $this->query->where('name', $value);
    }
}
