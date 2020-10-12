<?php

namespace Kace\EloquentDraftable\Tests;

use Illuminate\Database\Eloquent\Model;
use Kace\EloquentDraftable\Draftable;

class TestModel extends Model
{
    use Draftable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'published_at',
    ];
}
