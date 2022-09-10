<?php

namespace App\Models;

use Illuminate\Support\Carbon;

/**
 * @property integer id
 * @property string subject
 * @property integer status
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Todo extends BaseModel
{
    protected $table="todos";

    protected $fillable = [
        "subject", "status"
    ];

    protected static array $SORTABLE_FIELDS = [
        "id", "status", "created_at"
    ];

    protected static array $SEARCHABLE_FIELDS = [
        "subject"
    ];

    protected static ?string $EXACT_SEARCH_FIELD = "subject";
    protected static ?string $EXACT_SEARCH_ORDER_FIELD = "created_at";

    public function getSearchUrl(): string
    {
        return "";
    }
}
