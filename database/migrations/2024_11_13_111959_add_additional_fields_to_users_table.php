<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->ulid('id')->primary();
            $table->string('name')->unique()->change();
            $table->enumAlterColumn('role', 'role', Role::class, default: Role::USER->value);
            $table->string('avatar', 2048)->nullable();
            $table->string('backdrop', 2048)->nullable();
            $table->enumAlterColumn('gender', 'gender', Gender::class, nullable: true);
            $table->string('description', 248)->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('allow_adult')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_auto_next')->default(false);
            $table->boolean('is_auto_play')->default(false);
            $table->boolean('is_auto_skip_intro')->default(false);
            $table->boolean('is_private_favorites')->default(false);

            // User ban status
            $table->boolean('is_banned')->default(false);

            // Notification preferences - Episodes
            $table->boolean('notify_new_episodes')->default(true);
            $table->boolean('notify_episode_date_changes')->default(true);
            $table->boolean('notify_announcement_to_ongoing')->default(true);

            // Notification preferences - Comments
            $table->boolean('notify_comment_replies')->default(true);
            $table->boolean('notify_comment_likes')->default(true);

            // Notification preferences - Ratings
            $table->boolean('notify_review_replies')->default(true);

            // Notification preferences - UserList
            $table->boolean('notify_planned_reminders')->default(true);

            // Notification preferences - Selections
            $table->boolean('notify_new_selections')->default(true);

            // Notification preferences - Movies
            $table->boolean('notify_status_changes')->default(true);
            $table->boolean('notify_new_seasons')->default(true);

            // Notification preferences - Subscription
            $table->boolean('notify_subscription_expiration')->default(true);
            $table->boolean('notify_subscription_renewal')->default(true);
            $table->boolean('notify_payment_issues')->default(true);
            $table->boolean('notify_tariff_changes')->default(true);

            // Notification preferences - System
            $table->boolean('notify_site_updates')->default(true);
            $table->boolean('notify_maintenance')->default(true);
            $table->boolean('notify_security_changes')->default(true);
            $table->boolean('notify_new_features')->default(true);
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignUlid('user_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->id();

            $table->dropColumn('role');
            $table->dropColumn('avatar');
            $table->dropColumn('backdrop');
            $table->dropColumn('gender');
            $table->dropColumn('description');
            $table->dropColumn('birthday');
            $table->dropColumn('allow_adult');
            $table->dropColumn('last_seen_at');
            $table->dropColumn('is_auto_next');
            $table->dropColumn('is_auto_play');
            $table->dropColumn('is_auto_skip_intro');
            $table->dropColumn('is_private_favorites');

            // User ban status
            $table->dropColumn('is_banned');

            // Notification preferences - Episodes
            $table->dropColumn('notify_new_episodes');
            $table->dropColumn('notify_episode_date_changes');
            $table->dropColumn('notify_announcement_to_ongoing');

            // Notification preferences - Comments
            $table->dropColumn('notify_comment_replies');
            $table->dropColumn('notify_comment_likes');

            // Notification preferences - Ratings
            $table->dropColumn('notify_review_replies');

            // Notification preferences - UserList
            $table->dropColumn('notify_planned_reminders');

            // Notification preferences - Selections
            $table->dropColumn('notify_new_selections');

            // Notification preferences - Movies
            $table->dropColumn('notify_status_changes');
            $table->dropColumn('notify_new_seasons');

            // Notification preferences - Subscription
            $table->dropColumn('notify_subscription_expiration');
            $table->dropColumn('notify_subscription_renewal');
            $table->dropColumn('notify_payment_issues');
            $table->dropColumn('notify_tariff_changes');

            // Notification preferences - System
            $table->dropColumn('notify_site_updates');
            $table->dropColumn('notify_maintenance');
            $table->dropColumn('notify_security_changes');
            $table->dropColumn('notify_new_features');
        });

        DB::unprepared('DROP TYPE role');
        DB::unprepared('DROP TYPE gender');

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignId('user_id')->nullable()->index();
        });
    }
};
