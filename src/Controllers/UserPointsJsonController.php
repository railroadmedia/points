<?php

namespace Railroad\Points\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Points\Repositories\UserPointsRepository;

class UserPointsJsonController extends Controller
{
    /**
     * @var UserPointsRepository
     */
    private $userPointsRepository;
    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * UserPointsJsonController constructor.
     *
     * @param UserPointsRepository $userPointsRepository
     */
    public function __construct(UserPointsRepository $userPointsRepository, PermissionService $permissionService)
    {
        $this->userPointsRepository = $userPointsRepository;
        $this->permissionService = $permissionService;
    }

    /**
     * Params:
     *
     * user_id
     * brands[]
     * limit
     * page
     * order_by_column
     * order_by_direction
     *
     * @param Request $request
     * @return mixed
     * @throws \Railroad\Permissions\Exceptions\NotAllowedException
     */
    public function index(Request $request)
    {
        $this->permissionService->canOrThrow(auth()->id(), 'view-points');

        $query =
            $this->userPointsRepository->query()
                ->where('user_id', $request->get('user_id', auth()->id()))
                ->whereIn('brand', $request->get('brands', [config('points.brand')]));

        $count = $query->count();

        $results =
            $query->limit($request->get('limit', 25))
                ->skip(($request->get('page', 1) - 1) * $request->get('limit', 25))
                ->orderBy($request->get('order_by_column', 'created_at'), $request->get('order_by_direction', 'desc'))
                ->get();

        return reply()->json(
            $results,
            [
                'totalResults' => $count,
            ]
        );
    }

    public function store(Request $request)
    {
        // todo
    }

    public function destroy(Request $request)
    {
        // todo
    }
}