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
            config('points.table_prefix') . config('points.tables.user_points'),
            function(Blueprint $table) {
                $table->increments('id');

                $table->string('user_id')->index();

                $table->string('trigger_hash');
                $table->string('trigger_description')->nullable();

                $table->integer('points')->default(0);
                $table->string('points_description')->nullable();

                $table->timestamp('created_at')->nullable()->index();
                $table->timestamp('updated_at')->nullable()->index();
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
