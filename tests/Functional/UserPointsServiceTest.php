<?php

namespace Railroad\Points\Functional;

use Carbon\Carbon;
use Railroad\Points\Services\UserPointsService;
use Railroad\Points\Tests\PointsTestCase;

class UserPointsServiceTest extends PointsTestCase
{
    /**
     * @var UserPointsService
     */
    private $userPointsService;

    protected function setUp()
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
        $this->userPointsService->setPoints(1, 'hash', 'trigger', 5);

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
}