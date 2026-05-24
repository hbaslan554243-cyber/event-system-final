<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

// ── Category ──────────────────────────────────────────────────
class Category extends Model {
    protected $fillable = ['name','slug','icon','color','description'];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?? Str::slug($m->name));
    }
    public function events() { return $this->hasMany(Event::class); }
}

// ── Venue ─────────────────────────────────────────────────────
class Venue extends Model {
    use SoftDeletes;
    protected $fillable = [
        'name','address','city','state','country','zip_code','latitude','longitude',
        'capacity','description','contact_person','contact_phone','contact_email',
        'website','amenities','images','is_active',
    ];
    protected $casts = ['amenities' => 'array', 'images' => 'array', 'is_active' => 'boolean'];
    public function events() { return $this->hasMany(Event::class); }
    public function hasConflict(\DateTime $start, \DateTime $end, ?int $excludeEventId = null): bool {
        $query = $this->events()->where(function($q) use ($start, $end) {
            $q->where('start_date', '<', $end)->where('end_date', '>', $start);
        })->whereNotIn('status', ['cancelled', 'draft']);
        if ($excludeEventId) $query->where('id', '!=', $excludeEventId);
        return $query->exists();
    }
}

// ── TicketType ────────────────────────────────────────────────
class TicketType extends Model {
    protected $fillable = [
        'event_id','name','description','type','price','quantity_available',
        'quantity_sold','max_per_person','sale_start','sale_end','perks','is_active',
    ];
    protected $casts = [
        'price' => 'decimal:2', 'sale_start' => 'datetime', 'sale_end' => 'datetime',
        'perks' => 'array', 'is_active' => 'boolean',
    ];
    public function event()       { return $this->belongsTo(Event::class); }
    public function registrations(){ return $this->hasMany(Registration::class); }
    public function getQuantityRemainingAttribute(): int {
        return max(0, $this->quantity_available - $this->quantity_sold);
    }
    public function getIsAvailableAttribute(): bool {
        if (!$this->is_active) return false;
        if ($this->quantity_remaining <= 0) return false;
        if ($this->sale_start && now() < $this->sale_start) return false;
        if ($this->sale_end && now() > $this->sale_end) return false;
        return true;
    }
}

// ── Registration ──────────────────────────────────────────────
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
    public function event()       { return $this->belongsTo(Event::class); }
    public function user()        { return $this->belongsTo(User::class); }
    public function ticketType()  { return $this->belongsTo(TicketType::class); }
    public function tickets()     { return $this->hasMany(Ticket::class); }
    public function payment()     { return $this->hasOne(Payment::class); }
}

// ── Ticket ────────────────────────────────────────────────────
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

// ── Payment ───────────────────────────────────────────────────
class Payment extends Model {
    protected $fillable = [
        'transaction_id','registration_id','user_id','amount','currency','gateway',
        'status','gateway_transaction_id','gateway_response','receipt_path','notes','paid_at',
    ];
    protected $casts = [
        'amount' => 'decimal:2', 'gateway_response' => 'array', 'paid_at' => 'datetime',
    ];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->transaction_id = $m->transaction_id ?? 'TXN-' . strtoupper(Str::random(12)));
    }
    public function registration() { return $this->belongsTo(Registration::class); }
    public function user()         { return $this->belongsTo(User::class); }
}

// ── Feedback ──────────────────────────────────────────────────
class Feedback extends Model {
    protected $fillable = [
        'event_id','user_id','overall_rating','venue_rating','organization_rating',
        'content_rating','comment','suggestions','would_recommend','is_public','is_approved',
    ];
    protected $casts = ['would_recommend' => 'boolean', 'is_public' => 'boolean', 'is_approved' => 'boolean'];
    public function event() { return $this->belongsTo(Event::class); }
    public function user()  { return $this->belongsTo(User::class); }
}

// ── Announcement ──────────────────────────────────────────────
class Announcement extends Model {
    protected $fillable = ['event_id','created_by','title','message','type','send_email','send_sms','scheduled_at','sent_at'];
    protected $casts = ['scheduled_at' => 'datetime', 'sent_at' => 'datetime', 'send_email' => 'boolean', 'send_sms' => 'boolean'];
    public function event()   { return $this->belongsTo(Event::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}

// ── EventMedia ────────────────────────────────────────────────
class EventMedia extends Model {
    protected $fillable = ['event_id','file_path','file_name','file_type','mime_type','file_size','title','description','category','is_public','sort_order'];
    protected $casts = ['is_public' => 'boolean'];
    public function event()    { return $this->belongsTo(Event::class); }
    public function getUrlAttribute(): string { return asset('storage/' . $this->file_path); }
}

// ── Resource ──────────────────────────────────────────────────
class Resource extends Model {
    protected $fillable = ['name','category','description','quantity_total','quantity_available','unit','cost_per_unit','is_active'];
    protected $casts = ['is_active' => 'boolean', 'cost_per_unit' => 'decimal:2'];
    public function events() { return $this->belongsToMany(Event::class, 'event_resources')->withPivot('quantity_needed','quantity_assigned','status','notes'); }
}

// ── Coupon ────────────────────────────────────────────────────
class Coupon extends Model {
    protected $fillable = ['event_id','code','type','value','max_uses','used_count','min_purchase','valid_from','valid_until','is_active'];
    protected $casts = ['valid_from' => 'datetime', 'valid_until' => 'datetime', 'is_active' => 'boolean', 'value' => 'decimal:2'];
    public function event() { return $this->belongsTo(Event::class); }
    public function isValid(float $amount = 0): bool {
        if (!$this->is_active) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        if (now() < $this->valid_from || now() > $this->valid_until) return false;
        if ($amount < $this->min_purchase) return false;
        return true;
    }
    public function calculateDiscount(float $amount): float {
        return $this->type === 'percentage'
            ? round($amount * $this->value / 100, 2)
            : min($this->value, $amount);
    }
}

// ── NotificationLog ───────────────────────────────────────────
class NotificationLog extends Model {
    protected $fillable = ['user_id','event_id','type','subject','body','status','error_message','sent_at'];
    protected $casts = ['sent_at' => 'datetime'];
    public function user()  { return $this->belongsTo(User::class); }
    public function event() { return $this->belongsTo(Event::class); }
}
