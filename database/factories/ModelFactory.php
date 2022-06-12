<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(Str::random(10)),
        'remember_token' => Str::random(10),
    ];
});

$factory->define(App\Models\Directory::class,function (Faker $faker) {
    $url = $faker->url;
    return [
        'directory_id' => null,
        'title' => $faker->title,
        'url_full' => '/'.$url,
        'url_part' => $url,
    ];
});

$factory->define(App\Models\Product::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'description' => $faker->text,
        'latest_price' => 1000,
    ];
});

$factory->define(App\Models\PStructure::class, function (Faker $faker) {
    return [
        'title' => $faker->unique()->title,
    ];
});

$factory->define(App\Models\Article::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
    ];
});
