<?php

namespace Railroad\Points\ViewComposers;

use Illuminate\Contracts\View\View;
use Railroad\Points\Services\UserPointsService;

class CurrentUserPointsComposer
{
    /**
     * @var UserPointsService
     */
    private $userPointsService;

    public function __construct(UserPointsService $userPointsService)
    {
        $this->userPointsService = $userPointsService;
    }

    public function compose(View $view)
    {
        $view->with(
            'currentUsersPoints',
            $this->userPointsService->countPoints(auth()->id())
        );
    }
}