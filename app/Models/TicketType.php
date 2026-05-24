<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model {
    protected $fillable = [
        'event_id','name','description','type','price','quantity_available',
        'quantity_sold','max_per_person','sale_start','sale_end','perks','is_active',
    ];
    protected $casts = [
        'price' => 'decimal:2', 'sale_start' => 'datetime', 'sale_end' => 'datetime',
        'perks' => 'array', 'is_active' => 'boolean',
    ];
    public function event()        { return $this->belongsTo(Event::class); }
    public function registrations(){ return $this->hasMany(Registration::class); }
    public function getQuantityRemainingAttribute(): int {
        return max(0, $this->quantity_available - $this->quantity_sold);
    }
public function getIsAvailableAttribute(): bool
{
    if (!$this->is_active) return false;
    if ($this->quantity_remaining <= 0) return false;
    // Only check dates if they are actually set
    if ($this->sale_start && now() < $this->sale_start) return false;
    if ($this->sale_end && now() > $this->sale_end) return false;
    return true;
}
}
