<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdBrandPointsIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('points.database_connection_name'))->table(
            config('points.tables.user_points'),
            function (Blueprint $table) {
                $table->index(['user_id', 'brand', 'points'], 'points_user_points_user_id_brand_points_index');
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
        Schema::connection(config('points.database_connection_name'))->table(
            config('points.tables.user_points'),
            function (Blueprint $table) {
                $table->dropIndex('points_user_points_user_id_brand_points_index');
            }
        );
    }
}
