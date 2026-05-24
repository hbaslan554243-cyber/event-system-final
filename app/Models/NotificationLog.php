<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model {
    protected $fillable = ['user_id','event_id','type','subject','body','status','error_message','sent_at'];
    protected $casts = ['sent_at' => 'datetime'];
    public function user()  { return $this->belongsTo(User::class); }
    public function event() { return $this->belongsTo(Event::class); }
}
