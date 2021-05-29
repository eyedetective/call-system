<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use  SoftDeletes,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','name', 'email', 'password', 'api_token','permission', 'department'
    ];

    protected static $options = [
        'permission' => ['Admin', 'Supervisor', 'Agent']
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'api_token', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

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
