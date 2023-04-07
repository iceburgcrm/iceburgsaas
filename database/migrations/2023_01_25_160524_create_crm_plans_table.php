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
        Schema::create('crm_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->default('');
            $table->string('description', 200)->default('');
            $table->integer('price')->default(0);
            $table->string('stripe_id', 64)->default('');
            $table->string('logo', 64)->default('');
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
        Schema::dropIfExists('crm_plans');
    }
};
