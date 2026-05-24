<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model {
    protected $fillable = [
        'ticket_number','registration_id','event_id','user_id','ticket_type_id',
        'qr_code_path','qr_code_data','status','used_at',
    ];
    protected $casts = ['used_at' => 'datetime'];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->ticket_number = $m->ticket_number ?? 'TKT-' . strtoupper(Str::random(10)));
    }
    public function registration() { return $this->belongsTo(Registration::class); }
    public function event()        { return $this->belongsTo(Event::class); }
    public function user()         { return $this->belongsTo(User::class); }
    public function ticketType()   { return $this->belongsTo(TicketType::class); }
}
