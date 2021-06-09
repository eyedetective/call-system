<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['customer_name','customer_phone','topic_id','comment','request','response','call_status','status','call_durations','source','ip','utm_source','utm_medium','utm_content','utm_term','referenceCall','outgoingCall','rep_user_id','assign_user_id','schedule_datetime'];

    protected static function boot()
    {
        parent::boot();

        self::updating(function ($model) {
            if($model->call_status == 'Answered'){
                $model->rep_user_id = auth()->user()->id;
            }
        });
    }

    public function repBy()
    {
        return $this->belongsTo('App\User','rep_user_id','id');
    }

    public function assignTo()
    {
        return $this->belongsTo('App\User','assign_user_id','id');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topic','topic_id','id');
    }

}
