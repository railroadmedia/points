<?php

namespace Railroad\Points\Listeners;


use Railroad\Points\Events\UserPointsUpdated;
use Railroad\Points\Services\UserPointsService;

class PointsEventListener
{
    /**
     * @var UserPointsService
     */
    private $userPointsService;

    /**
     * PointsEventListener constructor.
     * @param UserPointsService $userPointsService
     */
    public function __construct(UserPointsService $userPointsService)
    {
        $this->userPointsService = $userPointsService;
    }

    public function handleUserPointsUpdated(UserPointsUpdated $userPointsUpdated)
    {
        $this->userPointsService->clearUserPointsCache($userPointsUpdated->userId, $userPointsUpdated->brand);
    }
}