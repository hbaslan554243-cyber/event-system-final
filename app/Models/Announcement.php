<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model {
    protected $fillable = ['event_id','created_by','title','message','type','send_email','send_sms','scheduled_at','sent_at'];
    protected $casts = ['scheduled_at' => 'datetime', 'sent_at' => 'datetime', 'send_email' => 'boolean', 'send_sms' => 'boolean'];
    public function event()   { return $this->belongsTo(Event::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
