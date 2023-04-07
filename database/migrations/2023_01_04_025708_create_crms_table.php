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
        Schema::create('crms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('name', 50)->default('');
            $table->string('label', 50)->default('');
            $table->string('user_crm_name', 50)->default('');
            $table->integer('type_id')->default(1);
            $table->string('server', 32)->default('oracle4');
            $table->string('plan', 32)->default(1);
            $table->integer('status_id')->default(1);
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
        Schema::dropIfExists('crms');
    }
};
