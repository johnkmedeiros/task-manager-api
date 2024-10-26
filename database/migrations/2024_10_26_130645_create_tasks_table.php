<?php

use App\Enums\Tasks\TaskPriorityEnum;
use App\Enums\Tasks\TaskStatusEnum;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_date');
            $table->enum('status', TaskStatusEnum::getValues())->default(TaskStatusEnum::PENDING);
            $table->enum('priority', TaskPriorityEnum::getValues())->default(TaskPriorityEnum::MEDIUM);
            $table->boolean('reminder_sent')->default(false);
            $table->boolean('auto_complete_on_due_date')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
