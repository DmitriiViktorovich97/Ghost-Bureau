<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->unsignedInteger('capacity')->default(1);
            $table->enum('lighting', ['dark', 'dim', 'bright']);
            $table->enum('noise_level', ['quiet', 'moderate', 'loud']);
            $table->enum('humidity', ['dry', 'normal', 'damp']);
            $table->boolean('has_people')->default(false);
            $table->boolean('has_attic')->default(false);
            $table->boolean('has_mirrors')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('locations'); }
};
