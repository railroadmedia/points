<?php

namespace Railroad\Points\ViewComposers;

use Illuminate\Contracts\View\View;
use Railroad\Points\Services\UserPointsService;

class CurrentUserPointsComposer
{
    public function compose(View $view)
    {
        $view->with(
            'currentUsersPoints',
            UserPointsService::fetchPoints(auth()->id())
        );
    }
}