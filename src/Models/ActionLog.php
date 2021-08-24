<?php

namespace Asd\ActionLog\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer user_id
 * @property integer model_id
 * @property string model
 * @property string type
 * @property string timestamp
 * @property Collection fields
 */
class ActionLog extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    const TYPE_CREATED = 'created';
    const TYPE_UPDATED = 'updated';
    const TYPE_DELETED = 'deleted';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'model_id',
        'model',
        'type',
        'timestamp'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'model',
        'model_id',
        'user_id',
        'id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fields()
    {
        return $this->belongsToMany(ActionLogField::class, 'action_log_pivot', 'action_log_id');
    }
}
