<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model {
    protected $fillable = ['name','category','description','quantity_total','quantity_available','unit','cost_per_unit','is_active'];
    protected $casts = ['is_active' => 'boolean', 'cost_per_unit' => 'decimal:2'];
    public function events() {
        return $this->belongsToMany(Event::class, 'event_resources')
                    ->withPivot('quantity_needed','quantity_assigned','status','notes');
    }
}
