<?php

use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Kace\EloquentDraftable\Tests\TestModel;

/**
 * Define the model's default state.
 */
$factory->define(TestModel::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});

/**
 * Indicate that the model is published.
 */
$factory->state(TestModel::class, 'published', function (Faker $faker) {
    return [
        'published_at' => Carbon::now(),
    ];
});

/**
 * Indicate that the model is scheduled publishing for tomorrow.
 */
$factory->state(TestModel::class, 'scheduled', function (Faker $faker) {
    return [
        'published_at' => Carbon::now()->addDay(),
    ];
});
