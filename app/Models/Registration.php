<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Registration extends Model {
    use SoftDeletes;
    protected $fillable = [
        'registration_number','event_id','user_id','ticket_type_id','quantity',
        'unit_price','total_amount','discount_amount','final_amount','status',
        'payment_status','coupon_code','attendee_info','qr_code','checked_in_at','notes',
    ];
    protected $casts = [
        'attendee_info' => 'array', 'checked_in_at' => 'datetime',
        'unit_price' => 'decimal:2', 'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2', 'final_amount' => 'decimal:2',
    ];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->registration_number = $m->registration_number ?? 'REG-' . strtoupper(Str::random(8)));
    }
    public function event()      { return $this->belongsTo(Event::class); }
    public function user()       { return $this->belongsTo(User::class); }
    public function ticketType() { return $this->belongsTo(TicketType::class); }
    public function tickets()    { return $this->hasMany(Ticket::class); }
    public function payment()    { return $this->hasOne(Payment::class); }
}
