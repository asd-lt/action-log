<?php

namespace Asd\ActionLog\Models\Traits;

use Asd\ActionLog\Models\ActionLog;
use Asd\ActionLog\Models\ActionLogField;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

trait ActionLogTrait
{
    /**
     * @var null|string
     */
    protected $action = null;

    /**
     * Register events to log
     */
    static public function bootActionLogTrait()
    {
        static::updated(function ($model) {
            $model->addUpdatedActionLog();
        });

        static::created(function ($model) {
            $model->createActionLog(ActionLog::TYPE_CREATED);
        });

        static::deleted(function ($model) {
            $model->createActionLog(ActionLog::TYPE_DELETED);
        });
    }


    /**
     * Saves updated fields
     */
    public function addUpdatedActionLog()
    {
        $actionLog = $this->createActionLog(ActionLog::TYPE_UPDATED);

        $fields = $this->getLoggableFields();

        if (!empty($fields)) {
            $original = new static($this->getOriginal());

            foreach ($fields as $field) {
                if ($this->{$field} != $original->{$field}) {
                    $this->addFieldLog($field, $this->{$field}, $original->{$field}, $actionLog);
                }
            }
        }
    }

    /**
     * Add single field log
     *
     * @param $field
     * @param $new
     * @param null $old
     * @param null $actionLog
     */
    public function addFieldLog($field, $new, $old = null, $actionLog = null)
    {
        if ($actionLog === null) {
            $actionLog = $this->createActionLog(ActionLog::TYPE_UPDATED);
        }

        $actionLogField = ActionLogField::firstOrCreate([
            'field' => $field,
            'new' => is_string($new) ? $new : json_encode($new),
            'old' => is_string($old) ? $old : json_encode($old),
        ]);

        if ($actionLogField) {
            $actionLog->fields()->attach($actionLogField->getKey());
        }
    }

    /**
     * Add multiple fields log at once
     *
     * @param $fields
     */
    public function addMultipleFieldLog($fields)
    {
        if (!empty($fields)) {
            $actionLog = $this->createActionLog(ActionLog::TYPE_UPDATED);
            foreach ($fields as $fieldName => $fieldValue) {
                $this->addFieldLog($fieldName, $fieldValue, null, $actionLog);
            }
        }
    }

    /**
     * Create action log
     *
     * @param $type
     * @return ActionLog
     */
    public function createActionLog($type)
    {
        if ($this->action && $this->action->type == $type) {
            return $this->action;
        } else {
            return $this->action = ActionLog::create([
                'user_id' => $this->getUserId(),
                'model_id' => $this->getKey(),
                'model' => static::class,
                'type' => $type,
            ]);
        }
    }

    /**
     * Get loggable fields
     *
     * @return array
     */
    public function getLoggableFields()
    {
        if (empty($this->loggableFields)) {
            $fields = $this->fillable;
        } else {
            $fields = $this->loggableFields;
        }

        // return except some fields
        return array_keys(Arr::except(array_flip($fields), $this->actionLogAttributesExcept ?? []));
    }

    /**
     * @return mixed
     */
    public function actionLog()
    {
        return $this->hasMany(ActionLog::class, 'model_id');
    }

    /**
     * Return latest timestamp
     *
     * @param null $field
     * @param null $value
     *
     * @return Carbon
     * @throws \Exception
     */
    public function getLatestTimestamp($field = null, $value = null)
    {
        if (!$field && !$value) {
            $logRecord = $this->actionLog()->with('fields')->get(['timestamp'])->last();
        } else {
            $logRecord = $this->actionLog()->whereHas('fields', function ($query) use ($field, $value) {
                $query->where('field', $field);

                if ($value) {
                    $query->where('new', $value);
                }
            })->get(['timestamp'])->last();
        }

        return $logRecord ? new Carbon($logRecord->timestamp) : null;
    }

    /**
     * @return null
     */
    private function getUserId()
    {
        $guard = config('auth.defaults.action_log_guard', config('auth.defaults.guard'));
        return Auth::guard($guard)->id() ?? null;
    }
}
