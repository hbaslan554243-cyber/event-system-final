<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model {
    protected $fillable = ['name','slug','icon','color','description'];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?? Str::slug($m->name));
    }
    public function events() { return $this->hasMany(Event::class); }
}
