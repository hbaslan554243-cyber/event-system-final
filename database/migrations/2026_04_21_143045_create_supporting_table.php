<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('registration_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PHP');
            $table->enum('gateway', ['stripe', 'paypal', 'gcash', 'bank_transfer', 'cash', 'free']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        // Resources/Equipment
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // furniture, av_equipment, catering, etc.
            $table->text('description')->nullable();
            $table->integer('quantity_total');
            $table->integer('quantity_available');
            $table->string('unit')->default('piece'); // piece, set, etc.
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Event Resources
        Schema::create('event_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_needed');
            $table->integer('quantity_assigned')->default(0);
            $table->enum('status', ['requested', 'approved', 'assigned', 'returned'])->default('requested');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Feedbacks & Reviews
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('overall_rating')->unsigned(); // 1–5
            $table->tinyInteger('venue_rating')->unsigned()->nullable();
            $table->tinyInteger('organization_rating')->unsigned()->nullable();
            $table->tinyInteger('content_rating')->unsigned()->nullable();
            $table->text('comment')->nullable();
            $table->text('suggestions')->nullable();
            $table->boolean('would_recommend')->default(true);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);
        });

        // Announcements
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'urgent', 'update'])->default('info');
            $table->boolean('send_email')->default(false);
            $table->boolean('send_sms')->default(false);
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });

        // Event Media
        Schema::create('event_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // image, video, document
            $table->string('mime_type');
            $table->bigInteger('file_size'); // bytes
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('category', ['banner', 'gallery', 'promotional', 'document', 'video'])->default('gallery');
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 8, 2);
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->decimal('min_purchase', 10, 2)->default(0);
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Notifications log
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // email, sms, push
            $table->string('subject');
            $table->text('body');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('event_media');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('feedbacks');
        Schema::dropIfExists('event_resources');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('payments');
    }
};
