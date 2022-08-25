<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_schedules', function (Blueprint $table) {
           $table->id();
            $table->foreignId('loan_id')
              ->constrained()
              ->onUpdate('cascade')
              ->onDelete('cascade');
            $table->date('due_date');
            $table->decimal('term_amount', 8, 5);
            $table->decimal('paid_amount', 8, 5)->default(0);
            $table->integer('status')->comment('0-pending,1-paid')->default(0);
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
        Schema::dropIfExists('loan_schedules');
    }
}
