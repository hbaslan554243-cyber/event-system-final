<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
