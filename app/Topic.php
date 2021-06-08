<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Topic extends Model
{
    use  SoftDeletes;

    protected $fillable = [
        'name', 'active'
    ];

    public $dates = ['created_at','updated_at','delete_at'];

    protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->api_token = hash('sha256', Str::random(60));
            if(!$model->created_by) $model->created_by = auth()->user()->id;
            if(!$model->updated_by) $model->updated_by = auth()->user()->id;
        });
        self::updating(function($model){
            $model->updated_by = auth()->user()->id;
        });
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User','created_by','id')->withTrashed();
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User','updated_by','id')->withTrashed();
    }
}
