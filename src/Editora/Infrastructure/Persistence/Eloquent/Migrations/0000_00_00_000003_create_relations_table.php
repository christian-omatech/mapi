<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mage_relations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key')->index();
            $table->unsignedBigInteger('parent_instance_id');
            $table->unsignedBigInteger('child_instance_id');
            $table->smallInteger('order');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_instance_id')
                ->references('id')
                ->on('mage_instances')
                ->onDelete('cascade');

            $table->foreign('child_instance_id')
                ->references('id')
                ->on('mage_instances')
                ->onDelete('cascade');

            $table->unique(['key', 'parent_instance_id', 'child_instance_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mage_relations');
    }
}
