<?php

namespace Railroad\Points\Functional;

use Carbon\Carbon;
use Railroad\Points\Tests\PointsTestCase;

class UserProductToUserContentPermissionTest extends PointsTestCase
{
    public function test_case()
    {
        $this->databaseManager->connection(config('points.database_connection_name'))
            ->table(config('points.table_prefix') . config('points.tables.user_points'))
            ->insert(
                [
                    'user_id' => '1',
                    'trigger_hash' => 'th',
                    'trigger_description' => 'td',
                    'points' => '1',
                    'points_description' => 'asdf',
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        $this->assertTrue(true);
    }
}