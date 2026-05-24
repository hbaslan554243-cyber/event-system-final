<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\{
    EventBrowseController,
    RegistrationController,
    PaymentController,
    AttendeeTicketController,
    AttendeeDashboardController,
    FeedbackController
};
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboard,
    UserController as AdminUserController,
    AdminEventController,
    ReportController,
    CategoryController,
    VenueController,
    ResourceController
};
use App\Http\Controllers\Organizer\{
    DashboardController as OrganizerDashboard,
    EventController as OrganizerEventController,
    TicketTypeController,
    AttendeeController as OrganizerAttendeeController,
    MediaController,
    AnnouncementController,
    CouponController
};

// ── Public Routes ─────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/events', [EventBrowseController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventBrowseController::class, 'show'])->name('events.show');

// ── Auth Routes ───────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AuthController::class, 'changePassword'])->name('profile.password');
});

// ── Admin Routes ──────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', AdminUserController::class);
    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Events
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [AdminEventController::class, 'show'])->name('events.show');
    Route::patch('/events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::patch('/events/{event}/cancel', [AdminEventController::class, 'cancel'])->name('events.cancel');
    Route::patch('/events/{event}/feature', [AdminEventController::class, 'feature'])->name('events.feature');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/attendees', [ReportController::class, 'attendees'])->name('reports.attendees');
    Route::get('/reports/feedback', [ReportController::class, 'feedback'])->name('reports.feedback');
    Route::get('/reports/export/{type}', [ReportController::class, 'exportCsv'])->name('reports.export');

    // Categories
    Route::resource('categories', CategoryController::class)->except('show');

    // Venues
    Route::resource('venues', VenueController::class);

    // Resources
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::put('/resources/{resource}', [ResourceController::class, 'update'])->name('resources.update');
});

// ── Organizer Routes ──────────────────────────────────────────
Route::middleware(['auth', 'role:organizer,admin'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [OrganizerDashboard::class, 'index'])->name('dashboard');

    // Events CRUD
    Route::resource('events', OrganizerEventController::class);
Route::patch('/events/{event}/submit-review', [OrganizerEventController::class, 'submitForReview'])->name('events.submitReview');
    // Ticket Types
    Route::post('/events/{event}/ticket-types', [TicketTypeController::class, 'store'])->name('ticket-types.store');
    Route::put('/ticket-types/{ticketType}', [TicketTypeController::class, 'update'])->name('ticket-types.update');
    Route::delete('/ticket-types/{ticketType}', [TicketTypeController::class, 'destroy'])->name('ticket-types.destroy');

    // Attendee Management
    Route::get('/events/{event}/attendees', [OrganizerAttendeeController::class, 'index'])->name('attendees.index');
    Route::patch('/registrations/{registration}/check-in', [OrganizerAttendeeController::class, 'checkIn'])->name('attendees.check-in');
    Route::post('/scan-qr', [OrganizerAttendeeController::class, 'scanQr'])->name('attendees.scan-qr');

    // Media
    Route::get('/events/{event}/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/events/{event}/media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Announcements
    Route::post('/events/{event}/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');

    // Coupons
    Route::get('/events/{event}/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/events/{event}/coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');
});

// ── Attendee Routes ───────────────────────────────────────────
Route::middleware(['auth', 'role:attendee,organizer,admin'])->prefix('attendee')->name('attendee.')->group(function () {
    Route::get('/dashboard', [AttendeeDashboardController::class, 'index'])->name('dashboard');

    // Registration
    Route::get('/events/{event}/checkout', [RegistrationController::class, 'showCheckout'])->name('events.checkout');
    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('events.register');
    Route::get('/registrations', function() {
    $registrations = \App\Models\Registration::with('event', 'ticketType', 'tickets', 'payment')
        ->where('user_id', auth()->id())
        ->latest()
        ->paginate(10);
    return view('attendee.registrations.index', compact('registrations'));
})->name('attendee.registrations.index');
    Route::patch('/registrations/{registration}/cancel', [RegistrationController::class, 'cancel'])->name('registrations.cancel');

    // Payments
    Route::get('/payment/{registration}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{registration}', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payment.receipt');
    Route::get('/payment/{payment}/download', [PaymentController::class, 'downloadReceipt'])->name('payment.download');

    // Tickets
    Route::get('/tickets', [AttendeeTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{registration}', [AttendeeTicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/download', [AttendeeTicketController::class, 'download'])->name('tickets.download');

    // Feedback
    Route::get('/events/{event}/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/events/{event}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

// ── API Routes (AJAX) ─────────────────────────────────────────
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::post('/validate-coupon', function (\Illuminate\Http\Request $request) {
        $coupon = \App\Models\Coupon::where(['event_id' => $request->event_id, 'code' => $request->code])->first();
        if (!$coupon || !$coupon->isValid($request->amount)) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon.']);
        }
        return response()->json(['valid' => true, 'discount' => $coupon->calculateDiscount($request->amount), 'type' => $coupon->type, 'value' => $coupon->value]);
    })->name('validate-coupon');

    Route::get('/venue-availability', function (\Illuminate\Http\Request $request) {
        $venue = \App\Models\Venue::findOrFail($request->venue_id);
        $available = !$venue->hasConflict(new \DateTime($request->start), new \DateTime($request->end), $request->exclude_event_id);
        return response()->json(['available' => $available]);
    })->name('venue-availability');
});
