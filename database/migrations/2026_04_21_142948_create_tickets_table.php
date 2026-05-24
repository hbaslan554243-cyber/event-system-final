<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name'); // General, VIP, Early Bird, etc.
            $table->text('description')->nullable();
            $table->enum('type', ['free', 'paid', 'vip', 'donation'])->default('paid');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('quantity_available');
            $table->integer('quantity_sold')->default(0);
            $table->integer('max_per_person')->default(5);
            $table->dateTime('sale_start')->nullable();
            $table->dateTime('sale_end')->nullable();
            $table->json('perks')->nullable(); // ['Backstage access', 'Free merchandise']
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'attended', 'no_show'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded', 'free'])->default('pending');
            $table->string('coupon_code')->nullable();
            $table->json('attendee_info')->nullable(); // Additional attendee details
            $table->string('qr_code')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('registration_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained()->onDelete('cascade');
            $table->string('qr_code_path')->nullable();
            $table->string('qr_code_data')->nullable();
            $table->enum('status', ['valid', 'used', 'cancelled', 'expired'])->default('valid');
            $table->dateTime('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('ticket_types');
    }
};
