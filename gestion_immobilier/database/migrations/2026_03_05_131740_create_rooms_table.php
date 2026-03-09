<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->float('largeur');
            $table->float('hauteur');
            $table->string('type'); // salon, chambre...
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('rooms');
    }
};