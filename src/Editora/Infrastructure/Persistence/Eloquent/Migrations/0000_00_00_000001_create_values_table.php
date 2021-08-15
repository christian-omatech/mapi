<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mage_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('instance_id');
            $table->string('attribute_key')->index();
            $table->string('language')->index();
            $table->text('value')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('instance_id')
                ->references('id')
                ->on('mage_instances')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mage_values');
    }
}
