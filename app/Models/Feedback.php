<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model {
    protected $table = 'feedbacks'; 
    protected $fillable = [
        'event_id','user_id','overall_rating','venue_rating','organization_rating',
        'content_rating','comment','suggestions','would_recommend','is_public','is_approved',
    ];
    protected $casts = ['would_recommend' => 'boolean', 'is_public' => 'boolean', 'is_approved' => 'boolean'];
    public function event() { return $this->belongsTo(Event::class); }
    public function user()  { return $this->belongsTo(User::class); }
}
