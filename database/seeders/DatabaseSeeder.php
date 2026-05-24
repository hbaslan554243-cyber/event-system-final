<?php

namespace Database\Seeders;

use App\Models\{User, Category, Venue, Event, TicketType, Registration, Ticket, Payment, Feedback, Resource};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            VenueSeeder::class,
            ResourceSeeder::class,
            EventSeeder::class,
        ]);
    }
}

// ── Category Seeder ───────────────────────────────────────────
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Concert & Music',    'icon' => '🎵', 'color' => '#ec4899'],
            ['name' => 'Tech Conference',    'icon' => '💻', 'color' => '#6366f1'],
            ['name' => 'Workshop',           'icon' => '🔧', 'color' => '#f59e0b'],
            ['name' => 'Seminar',            'icon' => '📚', 'color' => '#10b981'],
            ['name' => 'Sports & Fitness',   'icon' => '⚽', 'color' => '#3b82f6'],
            ['name' => 'Arts & Culture',     'icon' => '🎨', 'color' => '#8b5cf6'],
            ['name' => 'Food & Beverage',    'icon' => '🍕', 'color' => '#ef4444'],
            ['name' => 'Networking',         'icon' => '🤝', 'color' => '#14b8a6'],
            ['name' => 'Online Event',       'icon' => '🌐', 'color' => '#64748b'],
            ['name' => 'Festival',           'icon' => '🎉', 'color' => '#f97316'],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::create(array_merge($cat, ['slug' => Str::slug($cat['name'])]));
        }
    }
}

// ── User Seeder ───────────────────────────────────────────────
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'              => 'System Admin',
            'email'             => 'admin@eventpro.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        // Organizers
        $organizers = [
            ['name' => 'Maria Santos',    'email' => 'maria@eventpro.com'],
            ['name' => 'Juan Dela Cruz',  'email' => 'juan@eventpro.com'],
            ['name' => 'Ana Reyes',       'email' => 'ana@eventpro.com'],
        ];

        foreach ($organizers as $org) {
            User::create(array_merge($org, [
                'password'          => Hash::make('password'),
                'role'              => 'organizer',
                'is_active'         => true,
                'email_verified_at' => now(),
                'phone'             => '+63 9' . rand(100000000, 999999999),
            ]));
        }

        // Attendees
        $attendees = [
            'Pedro Reyes', 'Luz Garcia', 'Carlos Manalo', 'Rosa Aquino',
            'Miguel Torres', 'Elena Bautista', 'Ramon Villanueva', 'Sofia Mendoza',
            'Diego Cruz', 'Isabella Ramos',
        ];

        foreach ($attendees as $i => $name) {
            User::create([
                'name'              => $name,
                'email'             => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'password'          => Hash::make('password'),
                'role'              => 'attendee',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}

// ── Venue Seeder ──────────────────────────────────────────────
class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            [
                'name'            => 'SMX Convention Center',
                'address'         => 'SM Mall of Asia Complex, Harbor Drive',
                'city'            => 'Pasay City',
                'country'         => 'Philippines',
                'capacity'        => 5000,
                'contact_person'  => 'Events Team',
                'contact_phone'   => '+63 2 8556 8888',
                'contact_email'   => 'events@smx.com',
                'amenities'       => ['WiFi', 'Parking', 'Air Conditioning', 'Projector', 'Sound System', 'Catering'],
            ],
            [
                'name'            => 'Makati Diamond Residences Events Hall',
                'address'         => 'Makati Avenue corner Edsa',
                'city'            => 'Makati City',
                'country'         => 'Philippines',
                'capacity'        => 500,
                'contact_person'  => 'Banquet Manager',
                'contact_phone'   => '+63 2 8848 0000',
                'amenities'       => ['WiFi', 'Parking', 'Air Conditioning', 'AV Equipment'],
            ],
            [
                'name'            => 'UP Town Center Event Space',
                'address'         => 'Katipunan Avenue',
                'city'            => 'Quezon City',
                'country'         => 'Philippines',
                'capacity'        => 200,
                'amenities'       => ['WiFi', 'Open Space', 'Stage'],
            ],
            [
                'name'            => 'Davao City Waterfront Hotel Ballroom',
                'address'         => 'Claro M. Recto Street',
                'city'            => 'Davao City',
                'country'         => 'Philippines',
                'capacity'        => 1000,
                'amenities'       => ['WiFi', 'Parking', 'Air Conditioning', 'Stage', 'Catering'],
            ],
            [
                'name'            => 'Cebu International Convention Center',
                'address'         => 'Brgy. Punta Engaño',
                'city'            => 'Lapu-Lapu City',
                'country'         => 'Philippines',
                'capacity'        => 3000,
                'amenities'       => ['WiFi', 'Parking', 'Multiple Halls', 'Catering', 'Business Center'],
            ],
        ];

        foreach ($venues as $venue) {
            Venue::create($venue);
        }
    }
}

// ── Resource Seeder ───────────────────────────────────────────
class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            ['name' => 'Folding Chairs',      'category' => 'furniture',    'quantity_total' => 500, 'quantity_available' => 500, 'unit' => 'piece',  'cost_per_unit' => 15],
            ['name' => 'Rectangular Tables',  'category' => 'furniture',    'quantity_total' => 100, 'quantity_available' => 100, 'unit' => 'piece',  'cost_per_unit' => 50],
            ['name' => 'Projector (4K)',       'category' => 'av_equipment', 'quantity_total' => 10,  'quantity_available' => 10,  'unit' => 'unit',   'cost_per_unit' => 2000],
            ['name' => 'LED Screen (10ft)',    'category' => 'av_equipment', 'quantity_total' => 5,   'quantity_available' => 5,   'unit' => 'unit',   'cost_per_unit' => 5000],
            ['name' => 'Wireless Microphone', 'category' => 'av_equipment', 'quantity_total' => 20,  'quantity_available' => 20,  'unit' => 'unit',   'cost_per_unit' => 500],
            ['name' => 'Sound System (PA)',    'category' => 'av_equipment', 'quantity_total' => 8,   'quantity_available' => 8,   'unit' => 'set',    'cost_per_unit' => 3000],
            ['name' => 'Extension Cords',     'category' => 'electrical',   'quantity_total' => 50,  'quantity_available' => 50,  'unit' => 'piece',  'cost_per_unit' => 50],
            ['name' => 'Catering Packages',   'category' => 'catering',     'quantity_total' => 1000,'quantity_available' => 1000,'unit' => 'pax',    'cost_per_unit' => 350],
            ['name' => 'Stage Platform (4x4)','category' => 'staging',      'quantity_total' => 20,  'quantity_available' => 20,  'unit' => 'module', 'cost_per_unit' => 800],
            ['name' => 'Backdrop Stand',      'category' => 'staging',      'quantity_total' => 15,  'quantity_available' => 15,  'unit' => 'piece',  'cost_per_unit' => 300],
        ];

        foreach ($resources as $res) {
            Resource::create($res);
        }
    }
}

// ── Event Seeder ──────────────────────────────────────────────
class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organizers  = User::where('role', 'organizer')->get();
        $venues      = Venue::all();
        $categories  = \App\Models\Category::all();
        $attendees   = User::where('role', 'attendee')->get();

        $events = [
            [
                'title'             => 'PhilTech Summit 2025',
                'category'          => 'Tech Conference',
                'status'            => 'upcoming',
                'start_date'        => Carbon::now()->addDays(30),
                'end_date'          => Carbon::now()->addDays(31),
                'description'       => "The Philippines' biggest tech conference bringing together developers, designers, startup founders, and tech enthusiasts for two days of learning, networking, and innovation.\n\nFeaturing 50+ speakers from top tech companies including Google, Meta, Grab, and local unicorn startups.\n\nTopics covered:\n• AI & Machine Learning\n• Cloud Architecture\n• Cybersecurity\n• Product Management\n• Fintech & Blockchain",
                'short_description' => "Philippines' biggest tech conference with 50+ speakers from Google, Meta, Grab and more.",
                'max_attendees'     => 2000,
                'is_featured'       => true,
                'tickets'           => [
                    ['name' => 'General Admission', 'type' => 'paid', 'price' => 1500, 'quantity' => 1500],
                    ['name' => 'VIP Pass',          'type' => 'vip',  'price' => 5000, 'quantity' => 200],
                    ['name' => 'Student Rate',      'type' => 'paid', 'price' => 500,  'quantity' => 300],
                ],
            ],
            [
                'title'             => 'Laravel Philippines Meetup',
                'category'          => 'Tech Conference',
                'status'            => 'upcoming',
                'start_date'        => Carbon::now()->addDays(14),
                'end_date'          => Carbon::now()->addDays(14)->addHours(4),
                'description'       => "Monthly meetup for Laravel developers in the Philippines. Join us for talks, demos, and networking.\n\nTopics this month:\n• Laravel 11 New Features\n• Building REST APIs with Sanctum\n• Performance Optimization Tips",
                'short_description' => 'Monthly meetup for Laravel developers in the Philippines.',
                'max_attendees'     => 150,
                'is_featured'       => false,
                'tickets'           => [
                    ['name' => 'Free Seat', 'type' => 'free', 'price' => 0, 'quantity' => 150],
                ],
            ],
            [
                'title'             => 'OPM Music Festival 2025',
                'category'          => 'Concert & Music',
                'status'            => 'upcoming',
                'start_date'        => Carbon::now()->addDays(45),
                'end_date'          => Carbon::now()->addDays(45)->addHours(8),
                'description'       => "A celebration of Original Pilipino Music featuring 20+ OPM artists performing live! Experience the best of Filipino music from classic hits to today's hottest tracks.\n\nLine-up includes both veteran OPM icons and the newest generation of Filipino artists.",
                'short_description' => 'A celebration of Original Pilipino Music with 20+ artists performing live.',
                'max_attendees'     => 5000,
                'is_featured'       => true,
                'tickets'           => [
                    ['name' => 'General Area',  'type' => 'paid', 'price' => 800,  'quantity' => 3000],
                    ['name' => 'VIP Standing',  'type' => 'vip',  'price' => 2500, 'quantity' => 1000],
                    ['name' => 'VIP Seated',    'type' => 'vip',  'price' => 4000, 'quantity' => 500],
                    ['name' => 'VVIP Backstage','type' => 'vip',  'price' => 10000,'quantity' => 50],
                ],
            ],
            [
                'title'             => 'Digital Marketing Masterclass',
                'category'          => 'Workshop',
                'status'            => 'upcoming',
                'start_date'        => Carbon::now()->addDays(7),
                'end_date'          => Carbon::now()->addDays(7)->addHours(6),
                'description'       => "A full-day intensive workshop on digital marketing strategies that actually work in 2025.\n\nCover:\n• SEO & Content Marketing\n• Social Media Marketing\n• Email Campaigns\n• Google & Meta Ads\n• Analytics & Data",
                'short_description' => 'Full-day intensive workshop on digital marketing strategies that work in 2025.',
                'max_attendees'     => 50,
                'is_featured'       => false,
                'tickets'           => [
                    ['name' => 'Standard', 'type' => 'paid', 'price' => 2500, 'quantity' => 40],
                    ['name' => 'Premium',  'type' => 'paid', 'price' => 3500, 'quantity' => 10],
                ],
            ],
            [
                'title'             => 'Online: Python for Data Science Bootcamp',
                'category'          => 'Online Event',
                'status'            => 'upcoming',
                'is_online'         => true,
                'start_date'        => Carbon::now()->addDays(3),
                'end_date'          => Carbon::now()->addDays(3)->addHours(8),
                'description'       => "A comprehensive online bootcamp covering Python programming for data science and machine learning.\n\nRequirements: Basic programming knowledge\nPlatform: Zoom + Jupyter Notebooks",
                'short_description' => 'Comprehensive online bootcamp: Python for data science and machine learning.',
                'max_attendees'     => 100,
                'is_featured'       => false,
                'tickets'           => [
                    ['name' => 'Bootcamp Pass', 'type' => 'paid', 'price' => 1200, 'quantity' => 100],
                ],
            ],
        ];

        foreach ($events as $i => $data) {
            $categoryModel = $categories->firstWhere('name', $data['category']);
            $organizer     = $organizers->get($i % $organizers->count());
            $venue         = isset($data['is_online']) && $data['is_online'] ? null : $venues->get($i % $venues->count());

            $event = Event::create([
                'organizer_id'      => $organizer->id,
                'category_id'       => $categoryModel->id,
                'venue_id'          => $venue?->id,
                'title'             => $data['title'],
                'slug'              => Str::slug($data['title']) . '-' . Str::random(5),
                'description'       => $data['description'],
                'short_description' => $data['short_description'],
                'start_date'        => $data['start_date'],
                'end_date'          => $data['end_date'],
                'status'            => $data['status'],
                'max_attendees'     => $data['max_attendees'],
                'is_featured'       => $data['is_featured'],
                'is_online'         => $data['is_online'] ?? false,
                'tags'              => [],
            ]);

            // Create ticket types
            $ticketTypeModels = [];
            foreach ($data['tickets'] as $tt) {
                $ticketTypeModels[] = TicketType::create([
                    'event_id'           => $event->id,
                    'name'               => $tt['name'],
                    'type'               => $tt['type'],
                    'price'              => $tt['price'],
                    'quantity_available' => $tt['quantity'],
                    'quantity_sold'      => 0,
                    'max_per_person'     => 5,
                    'is_active'          => true,
                ]);
            }

            // Create some sample registrations
            $sampleCount = min(5, $attendees->count());
            for ($j = 0; $j < $sampleCount; $j++) {
                $attendee   = $attendees->get($j);
                $ticketType = $ticketTypeModels[0];
                $qty        = rand(1, 2);
                $amount     = $ticketType->price * $qty;

                $reg = Registration::create([
                    'event_id'        => $event->id,
                    'user_id'         => $attendee->id,
                    'ticket_type_id'  => $ticketType->id,
                    'quantity'        => $qty,
                    'unit_price'      => $ticketType->price,
                    'total_amount'    => $amount,
                    'discount_amount' => 0,
                    'final_amount'    => $amount,
                    'status'          => 'confirmed',
                    'payment_status'  => $ticketType->price == 0 ? 'free' : 'paid',
                ]);

                $ticketType->increment('quantity_sold', $qty);

                if ($ticketType->price > 0) {
                    Payment::create([
                        'registration_id' => $reg->id,
                        'user_id'         => $attendee->id,
                        'amount'          => $amount,
                        'currency'        => 'PHP',
                        'gateway'         => ['stripe', 'gcash', 'paypal'][rand(0, 2)],
                        'status'          => 'completed',
                        'paid_at'         => now()->subDays(rand(1, 5)),
                    ]);
                }
            }
        }

        // Create a completed event with feedback
        $completedEvent = Event::create([
            'organizer_id'  => $organizers->first()->id,
            'category_id'   => $categories->firstWhere('name', 'Seminar')->id,
            'venue_id'      => $venues->first()->id,
            'title'         => 'Entrepreneurship Summit 2024',
            'slug'          => 'entrepreneurship-summit-2024',
            'description'   => 'Annual gathering of entrepreneurs, investors, and business leaders.',
            'start_date'    => Carbon::now()->subDays(30),
            'end_date'      => Carbon::now()->subDays(30)->addHours(6),
            'status'        => 'completed',
            'max_attendees' => 300,
            'avg_rating'    => 4.3,
            'total_reviews' => 3,
            'tags'          => ['business', 'startup', 'networking'],
        ]);

        TicketType::create(['event_id' => $completedEvent->id, 'name' => 'Standard', 'type' => 'paid', 'price' => 1800, 'quantity_available' => 200, 'quantity_sold' => 120, 'max_per_person' => 3, 'is_active' => true]);

        // Add feedback
        $ratings = [5, 4, 4];
        foreach ($attendees->take(3) as $k => $att) {
            $reg = Registration::create([
                'event_id' => $completedEvent->id, 'user_id' => $att->id,
                'ticket_type_id' => TicketType::where('event_id', $completedEvent->id)->first()->id,
                'quantity' => 1, 'unit_price' => 1800, 'total_amount' => 1800,
                'discount_amount' => 0, 'final_amount' => 1800,
                'status' => 'attended', 'payment_status' => 'paid',
                'checked_in_at' => Carbon::now()->subDays(30),
            ]);

            Feedback::create([
                'event_id' => $completedEvent->id, 'user_id' => $att->id,
                'overall_rating' => $ratings[$k], 'venue_rating' => $ratings[$k],
                'organization_rating' => $ratings[$k], 'content_rating' => $ratings[$k],
                'comment' => ['Excellent event! Very well organized.', 'Great speakers and content. Highly recommend!', 'Good event, learned a lot. Would attend again.'][$k],
                'would_recommend' => true, 'is_public' => true, 'is_approved' => true,
            ]);
        }
    }
}
