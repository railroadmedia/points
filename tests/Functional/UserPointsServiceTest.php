<?php

namespace Railroad\Points\Functional;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Railroad\Points\Services\UserPointsService;
use Railroad\Points\Tests\PointsTestCase;

class UserPointsServiceTest extends PointsTestCase
{
    /**
     * @var UserPointsService
     */
    private $userPointsService;

    protected function setUp():void
    {
        parent::setUp();

        $this->userPointsService = app()->make(UserPointsService::class);
    }

    public function test_count_points_zero()
    {
        $this->assertEquals(0, $this->userPointsService->countPoints(1));
    }

    public function test_count_points_high()
    {
        $this->userPointsService->setPoints(1, 'hash', 'trigger', 1000000000);

        $this->assertEquals(1000000000, $this->userPointsService->countPoints(1));
    }

    public function test_count_points_brands()
    {
        $this->userPointsService->setPoints(1, 'hash1', 'trigger1', 10, 'description', 'brand1');
        $this->userPointsService->setPoints(1, 'hash2', 'trigger2', 20, 'description', 'brand2');

        $this->assertEquals(10, $this->userPointsService->countPoints(1, 'brand1'));
        $this->assertEquals(20, $this->userPointsService->countPoints(1, 'brand2'));
    }

    public function test_count_points_for_many()
    {
        $this->userPointsService->setPoints(1, ['hash_data_1', 'hash_data_2'], 'trigger', 5);

        $this->assertEquals([1 => 5, 2 => 0], $this->userPointsService->countPointsForMany([1, 2]));
    }

    public function test_count_points_for_many_brands()
    {
        $this->userPointsService->setPoints(1, 'hash1', 'trigger1', 5, 'description', 'brand1');
        $this->userPointsService->setPoints(1, 'hash2', 'trigger2', 10, 'description', 'brand2');
        $this->userPointsService->setPoints(2, 'hash1', 'trigger1', 15, 'description', 'brand1');
        $this->userPointsService->setPoints(2, 'hash2', 'trigger2', 20, 'description', 'brand2');

        $this->assertEquals([1 => 5, 2 => 15], $this->userPointsService->countPointsForMany([1, 2], 'brand1'));
    }

    public function test_count_points_for_many_many()
    {
        $this->userPointsService->setPoints(1, 'hash1', 'trigger1', 5);
        $this->userPointsService->setPoints(2, 'hash2', 'trigger2', 500);
        $this->userPointsService->setPoints(3, 'hash3', 'trigger3', 5000);

        $this->assertEquals(
            [1 => 5, 2 => 500, 3 => 5000, 4 => 0],
            $this->userPointsService->countPointsForMany([1, 2, 3, 4])
        );
    }

    public function test_set_points_new()
    {
        $this->userPointsService->setPoints(1, (object)['data' => 5], 'name', 5, 'description', 'brand');

        $this->assertDatabaseHas(
            config('points.tables.user_points'),
            [
                'user_id' => 1,
                'trigger_hash' => $this->userPointsService->hash((object)['data' => 5]),
                'trigger_name' => 'name',
                'trigger_hash_data' => serialize((object)['data' => 5]),
                'points' => 5,
                'points_description' => 'description',
                'brand' => 'brand',
                'created_at' => Carbon::now()
                    ->toDateTimeString(),
                'updated_at' => null,
            ],
            config('points.database_connection_name')
        );
    }

    public function test_set_points_existing()
    {
        $this->userPointsService->setPoints(1, 'hash', 'name', 5, 'description', 'brand');
        $this->userPointsService->setPoints(1, 'hash', 'name', 25, 'description', 'brand');

        $this->assertDatabaseHas(
            config('points.tables.user_points'),
            [
                'user_id' => 1,
                'trigger_hash' => $this->userPointsService->hash('hash'),
                'trigger_name' => 'name',
                'trigger_hash_data' => serialize('hash'),
                'points' => 25,
                'points_description' => 'description',
                'brand' => 'brand',
                'created_at' => Carbon::now()
                    ->toDateTimeString(),
                'updated_at' => Carbon::now()
                    ->toDateTimeString(),
            ],
            config('points.database_connection_name')
        );
    }

    public function test_delete_points_non_exist()
    {
        $deleted = $this->userPointsService->deletePoints(1, 'hash');

        $this->assertFalse($deleted);
    }

    public function test_delete_points_existing()
    {
        $this->userPointsService->setPoints(1, 'hash', 'name', 5, 'description', 'brand');

        $this->assertEquals(5, $this->userPointsService->countPoints(1, 'brand'));

        $deleted = $this->userPointsService->deletePoints(1, 'hash', 'brand');

        $this->assertTrue($deleted);

        $this->assertDatabaseMissing(
            config('points.tables.user_points'),
            [
                'user_id' => 1,
                'trigger_hash' => $this->userPointsService->hash('hash'),
                'trigger_name' => 'name',
                'points' => 5,
            ],
            config('points.database_connection_name')
        );

        $this->assertEquals(0, $this->userPointsService->countPoints(1, 'brand'));
    }

    public function test_hash()
    {
        $hash1 = $this->userPointsService->hash(['content_id' => 1]);
        $hash2 = $this->userPointsService->hash(['content_id' => 1]);

        $this->assertEquals($hash1, $hash2);

        $hash1 = $this->userPointsService->hash(new Collection(['content_id' => 1]));
        $hash2 = $this->userPointsService->hash(new Collection(['content_id' => 1]));

        $this->assertEquals($hash1, $hash2);
    }
}