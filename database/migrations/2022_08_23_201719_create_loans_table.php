<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->string('loan_id')->default(0);
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('user_id')
              ->constrained()
              ->onUpdate('cascade')
              ->onDelete('cascade');
            $table->decimal('loan_amount', 8, 5);
            $table->decimal('paid_amount', 8, 5)->default(0);
            $table->integer('term_nos');
            $table->integer('status')->comment('0-pending,1-approved,2-completed')->default(0);
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
        Schema::dropIfExists('loans');
    }
}
