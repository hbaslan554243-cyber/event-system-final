<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

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
