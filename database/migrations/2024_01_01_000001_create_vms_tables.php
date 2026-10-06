<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Core visitors table (pre-registered & walk-in)
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // VIS-20261006-00001
            $table->string('full_name');
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('position', 100)->nullable();
            $table->string('id_type', 30)->default('KTP'); // KTP, Paspor, SIM, Lainnya
            $table->string('id_number', 60)->nullable();
            $table->string('photo')->nullable();
            $table->integer('person_count')->default(1);
            $table->string('visit_type', 20)->default('pre_registered'); // pre_registered, walk_in, vendor, delivery
            $table->string('purpose')->nullable();
            $table->date('visit_date')->index();
            $table->time('visit_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('vehicle_number', 30)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('host_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('meeting_room', 100)->nullable();
            $table->string('access_area', 150)->nullable();
            $table->string('status', 20)->default('pending') // pending, approved, rejected, checked_in, checked_out, cancelled
                ->index();
            $table->text('reject_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_out_at')->nullable();
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('badge_code', 40)->nullable()->index();
            $table->timestamps();
        });

        // Approval trail (supports multi level later)
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users');
            $table->string('level', 20)->default('host'); // host, manager, security
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->text('note')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
        });

        // Blacklist / watchlist
        Schema::create('blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('id_number', 60)->nullable();
            $table->string('company')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('photo')->nullable();
            $table->text('reason');
            $table->string('type', 20)->default('blacklist'); // blacklist, watchlist
            $table->foreignId('created_by')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Key-value application settings (logo, SMTP, etc.)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // In-app notifications
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('icon', 20)->default('bell'); // bell, check, x, user, shield, qr
            $table->string('url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Audit trail
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 60); // login, logout, create, update, delete, check_in...
            $table->string('module', 60)->nullable();
            $table->string('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('blacklists');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('departments');
    }
};
