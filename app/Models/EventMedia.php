<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EventMedia extends Model {
    protected $fillable = ['event_id','file_path','file_name','file_type','mime_type','file_size','title','description','category','is_public','sort_order'];
    protected $casts = ['is_public' => 'boolean'];
    public function event() { return $this->belongsTo(Event::class); }
    public function getUrlAttribute(): string { return asset('storage/' . $this->file_path); }
}
