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
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('attribute_id');
            $table->string('language')->index();
            $table->longText('value')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('attribute_id')
                ->references('id')
                ->on('mage_attributes')
                ->onDelete('cascade');

            $table->unique(['attribute_id', 'language']);
            $table->index(['attribute_id', 'deleted_at']);
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
