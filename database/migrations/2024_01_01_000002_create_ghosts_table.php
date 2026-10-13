<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ghosts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('anxiety_level');
            $table->enum('favorite_temperature', ['cold', 'neutral', 'warm']);
            $table->date('deadline');
            $table->json('special_conditions')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->integer('match_score')->nullable();
            $table->text('match_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ghosts'); }
};
