<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('host', 32);
            $table->string('database', 32);
            $table->string('collation', 32)->default('utf8mb4');
            $table->string('charset', 32)->default('utf8mb4_unicode_ci');
            $table->string('username', 32);
            $table->string('password', 32);
            $table->string('port', 8)->default('3306');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connections');
    }
};
