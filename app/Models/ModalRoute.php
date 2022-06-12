<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;

/**
 * @property int id
 * @property string route
 * @property int modal_id
 * @property bool children_included
 * @property bool self_included
 *
 * @property Modal modal
 */
class ModalRoute extends BaseModel
{
    protected $table = 'modal_routes';

    protected $fillable = [
        'route', 'modal_id', 'children_included', 'self_included'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'created_at'];
    protected static array $roleFillable = [
        "super_user" => ["*"],
        "cms_manager" => ["*"]
    ];

    public function setRouteAttribute($route)
    {
        $this->attributes["route"] = static::fixTrailingSlash($route);
    }

    public function modal(): BelongsTo
    {
        return $this->belongsTo(Modal::class, 'modal_id');
    }

    public function getSearchUrl(): string
    {
        return '';
    }

    #[Pure]
    private static function fixTrailingSlash(string $url): string
    {
        $url = (Str::startsWith($url, "/") ? $url : "/" . $url);
        return Str::endsWith($url, "/") ? $url : $url . "/";
    }

    private static function parentPaths(string $path): ?array
    {
        $paths = [""];
        $parent_count = substr_count($path, '/');
        $checking_path = $path;
        for ($i = 0; $i <= $parent_count; $i++) {
            $parent_path = dirname($checking_path);
            $paths[] = static::fixTrailingSlash($parent_path);
            $checking_path = $parent_path;
        }
        array_pop($paths);
        return $paths;
    }

    public static function findRoute($path): null|static|Model
    {
        $path = static::fixTrailingSlash($path);
        return static::query()
            ->where(['route' => $path, 'self_included' => true])
            ->orderBy("updated_at", "desc")
            ->first()
            ?:
            static::query()
                ->where('children_included', true)
                ->whereIn('route', static::parentPaths($path))
                ->orderBy("updated_at", "desc")
                ->first();
    }
}
