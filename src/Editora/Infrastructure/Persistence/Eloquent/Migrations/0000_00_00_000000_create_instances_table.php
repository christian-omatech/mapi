<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mage_instances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->nullable()->unique();
            $table->string('class_key')->index();
            $table->string('key')->unique();
            $table->string('status')->index();
            $table->dateTime('start_publishing_date')->index();
            $table->dateTime('end_publishing_date')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mage_instances');
    }
}
