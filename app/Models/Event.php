<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organizer_id','category_id','venue_id','title','slug','description',
        'short_description','banner_image','gallery_images','start_date','end_date',
        'timezone','status','max_attendees','is_featured','is_online',
        'online_meeting_url','avg_rating','total_reviews','tags','requirements','faq',
    ];

    protected $casts = [
    'start_date'   => 'datetime',
    'end_date'     => 'datetime',
    'created_at'   => 'datetime',
    'updated_at'   => 'datetime',
    'ticket_price' => 'decimal:2',
    'is_published' => 'boolean',
];

    // ── Boot ─────────────────────────────────────────────────
    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($event) => $event->slug = $event->slug ?? Str::slug($event->title));
        static::updating(function ($event) {
            $now = now();
            if ($event->status === 'published') {
                if ($now < $event->start_date) $event->status = 'upcoming';
                elseif ($now >= $event->start_date && $now <= $event->end_date) $event->status = 'ongoing';
                elseif ($now > $event->end_date) $event->status = 'completed';
            }
        });
    }

    // ── Accessors ────────────────────────────────────────────
    public function getBannerUrlAttribute(): string
    {
        return $this->banner_image
            ? asset('storage/' . $this->banner_image)
            : asset('images/default-event.jpg');
    }

    public function getAvailableTicketsAttribute(): int
    {
        return max(0, ($this->max_attendees ?? PHP_INT_MAX) - $this->registrations()->where('status', '!=', 'cancelled')->count());
    }

    public function getIsFullAttribute(): bool
    {
        return $this->max_attendees && $this->registrations()->whereNotIn('status', ['cancelled'])->count() >= $this->max_attendees;
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft'     => ['label' => 'Draft',     'class' => 'badge-secondary'],
            'upcoming'  => ['label' => 'Upcoming',  'class' => 'badge-info'],
            'ongoing'   => ['label' => 'Live',      'class' => 'badge-success'],
            'completed' => ['label' => 'Completed', 'class' => 'badge-dark'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-danger'],
            default     => ['label' => 'Published', 'class' => 'badge-primary'],
        };
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopePublished($q)    { return $q->whereIn('status', ['published','upcoming','ongoing']); }
    public function scopeUpcoming($query) {return $query->where('status', 'upcoming')->where('start_date', '>=', today());}
    public function scopeFeatured($q)     { return $q->where('is_featured', true); }
    public function scopeByCategory($q, $id) { return $q->where('category_id', $id); }

    // ── Relationships ─────────────────────────────────────────
    public function organizer()   { return $this->belongsTo(User::class, 'organizer_id'); }
    public function category()    { return $this->belongsTo(Category::class); }
    public function venue()       { return $this->belongsTo(Venue::class); }
    public function ticketTypes() { return $this->hasMany(TicketType::class); }
    public function registrations(){ return $this->hasMany(Registration::class); }
    public function tickets()     { return $this->hasMany(Ticket::class); }
public function payments()
{
    return $this->hasManyThrough(Payment::class, Registration::class)
                ->where('payments.status', 'completed');
}    public function feedbacks()   { return $this->hasMany(Feedback::class); }
    public function media()       { return $this->hasMany(EventMedia::class)->orderBy('sort_order'); }
    public function announcements(){ return $this->hasMany(Announcement::class); }
    public function resources()   { return $this->belongsToMany(Resource::class, 'event_resources')->withPivot('quantity_needed','quantity_assigned','status','notes'); }
}
