<?php

namespace Railroad\Points\ViewComposers;

use Illuminate\Contracts\View\View;
use Railroad\Points\Services\UserPointsService;

class CurrentUserPointsComposer
{
    public function compose(View $view)
    {
        if (!empty(auth()->id())) {
            $view->with(
                'currentUsersPoints',
                UserPointsService::fetchPoints(auth()->id())
            );
        }
        else {
            $view->with(
                'currentUsersPoints',
                0
            );
        }
    }
}