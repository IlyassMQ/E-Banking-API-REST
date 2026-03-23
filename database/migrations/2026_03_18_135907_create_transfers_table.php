<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();

            $table->integer('sender_account_id');
            $table->integer('receiver_account_id');

            $table->decimal('amount', 10, 2);

            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED'])->default('PENDING');

            $table->text('reason_failed')->nullable();
            $table->integer('initiated_by_user_id')->nullable();

            $table->timestamps();

            $table->foreign('sender_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('receiver_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('initiated_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
