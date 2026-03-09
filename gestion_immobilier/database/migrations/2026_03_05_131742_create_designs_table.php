<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('style'); // minimaliste, etc.
            $table->string('imageGenere')->nullable(); // path to generated image
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('designs');
    }
};