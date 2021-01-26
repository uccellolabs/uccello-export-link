<?php

namespace Uccello\UrlExport\Models;

use App\Models\User;
use Illuminate\Support\Str;
use Uccello\Core\Database\Eloquent\Model;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Module;

class ExportUrl extends Model
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'module_id',
        'user_id',
        'uuid',
        'data',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'object',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getExtensionAttribute()
    {
        return $this->data->extension ?? null;
    }

    public function getWithIdAttribute()
    {
        return $this->data->with_id ?? false;
    }

    public function getWithTimestampsAttribute()
    {
        return $this->data->with_timestamps ?? false;
    }

    public function getWithDescendantsAttribute()
    {
        return $this->data->with_descendants ?? false;
    }

    public function getColumnsAttribute()
    {
        return $this->data->columns ?? null;
    }

    public function getColumnsLabelsAttribute()
    {
        $columnsLabels = collect();

        if ($this->columns) {
            foreach ($this->columns as $column) {
                $columnsLabels[] = uctrans('field.'.$column, $this->module);
            }
        }

        return $columnsLabels;
    }

    public function getConditionsAttribute()
    {
        return $this->data->conditions ?? null;
    }

    public function getUserFriendlyConditionsAttribute()
    {
        $conditions = collect();

        if ($this->conditions && isset($this->conditions->search)) {
            foreach ((array) $this->conditions->search as $column => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $conditions[] = uctrans('field.'.$column, $this->module).' : '.$value;
            }
        }

        return $conditions;
    }

    public function getOrderAttribute()
    {
        return $this->data->order ?? null;
    }
}
