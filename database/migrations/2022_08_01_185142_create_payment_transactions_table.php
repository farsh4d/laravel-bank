<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Farsh4d\Bank\Costants\PaymentStatus;

class CreatePaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('psp', 32);
            $table->bigInteger('price');
            $table->tinyInteger('status')->default(PaymentStatus::PENDING);
            $table->string('ref_id', 255)->nullable();
            $table->text('pay_res')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('payment_transactions');
    }
}
