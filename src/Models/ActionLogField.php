<?php

namespace Asd\ActionLog\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLogField extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field',
        'new',
        'old',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'pivot',
        'id'
    ];
}