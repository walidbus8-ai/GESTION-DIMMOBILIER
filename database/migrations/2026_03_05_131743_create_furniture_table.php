<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('furniture', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('type'); // chaise, table...
            $table->string('couleur')->nullable();
            $table->float('prix')->nullable();
            $table->string('imageURL')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('furniture');
    }
};