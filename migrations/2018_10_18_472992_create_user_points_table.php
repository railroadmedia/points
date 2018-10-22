<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('points.database_connection_name'))->create(
            config('points.tables.user_points'),
            function(Blueprint $table) {
                $table->increments('id');

                $table->string('user_id')->index();

                $table->string('trigger_hash', 32)->index();
                $table->string('trigger_name')->index();
                $table->text('trigger_hash_data');

                $table->bigInteger('points')->index()->default(0);
                $table->string('points_description')->nullable();

                $table->string('brand');

                $table->timestamp('created_at')->nullable()->index();
                $table->timestamp('updated_at')->nullable()->index();

                $table->unique(['user_id', 'trigger_hash', 'brand'], 'u_t_b');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('points.tables.user_points'));
    }
}
