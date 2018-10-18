<?php

namespace Railroad\Points\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Railroad\Points\Repositories\UserPointsRepository;
use Railroad\Resora\Repositories\RepositoryBase;

class UserPointsService extends RepositoryBase
{
    /**
     * @var UserPointsRepository
     */
    private $userPointsRepository;

    /**
     * UserPointsService constructor.
     *
     * @param UserPointsRepository $userPointsRepository
     */
    public function __construct(UserPointsRepository $userPointsRepository)
    {
        $this->userPointsRepository = $userPointsRepository;
    }

    /**
     * @param $userId
     * @param null $brand
     * @return mixed
     */
    public function countPoints($userId, $brand = null)
    {
        return $this->userPointsRepository->query()
            ->where(
                [
                    'user_id' => $userId,
                    'brand' => $brand ?? config('points.brand'),
                ]
            )
            ->sum('points');
    }

    /**
     * Returns array with user ids as keys and values as their total points.
     *
     * @param $userIds
     * @param null $brand
     * @return array
     */
    public function countPointsForMany($userIds, $brand = null)
    {
        $userCountRows =
            $this->userPointsRepository->query()
                ->select(
                    [
                        'user_id',
                        DB::raw('SUM(points) as total_points'),
                    ]
                )
                ->whereIn('user_id', $userIds)
                ->where('brand', $brand ?? config('points.brand'))
                ->groupBy('user_id')
                ->get();

        $userCounts = array_combine(
            $userCountRows->pluck('user_id')
                ->toArray(),
            $userCountRows->pluck('total_points')
                ->toArray()
        );

        $userCountsFromPassedInIds = [];

        foreach ($userIds as $userId) {
            $userCountsFromPassedInIds[$userId] = (integer)($userCounts[$userId] ?? 0);
        }

        return $userCountsFromPassedInIds;
    }

    /**
     * @param $userId
     * @param $triggerHash
     * @param $triggerName
     * @param $points
     * @param null $pointsDescription
     * @param null $brand
     * @return null|\Railroad\Resora\Entities\Entity
     */
    public function setPoints(
        $userId,
        $triggerHash,
        $triggerName,
        $points,
        $pointsDescription = null,
        $brand = null
    ) {
        return $this->userPointsRepository->updateOrCreate(
            [
                'user_id' => $userId,
                'trigger_hash' => $triggerHash,
                'brand' => $brand ?? config('points.brand'),
            ],
            [
                'trigger_name' => $triggerName,
                'points' => $points,
                'points_description' => $pointsDescription,
                'created_at' => Carbon::now()
                    ->toDateTimeString(),
                'updated_at' => Carbon::now()
                    ->toDateTimeString(),
            ]
        );
    }

    /**
     * @param $userId
     * @param $triggerHash
     * @param null $brand
     * @return bool
     */
    public function deletePoints($userId, $triggerHash, $brand = null)
    {
        return $this->userPointsRepository->query()
                ->where(
                    [
                        'user_id' => $userId,
                        'trigger_hash' => $triggerHash,
                        'brand' => $brand ?? config('points.brand'),
                    ]
                )
                ->delete() > 0;
    }

    /**
     * Can accept any serializable data.
     *
     * @param $data
     * @return string
     */
    public function hash($data)
    {
        return md5(serialize($data));
    }

    /**
     * @return UserPointsRepository
     */
    public function repository()
    {
        return $this->userPointsRepository;
    }
}