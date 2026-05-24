<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
