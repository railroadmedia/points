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

    public static $userPointsCache = [];

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
     * @return integer
     */
    public static function fetchPoints($userId)
    {
        $idsToPull = [];

        foreach (self::$userPointsCache as $userId => $points) {
            if (is_null($points)) {
                $idsToPull[] = $userId;
            }
        }

        if (!empty($idsToPull)) {
            self::$userPointsCache =
                array_replace(
                    self::$userPointsCache,
                    app()
                        ->make(self::class)
                        ->countPointsForMany($idsToPull)
                );
        }

        if (!is_null(self::$userPointsCache[$userId])) {
            return self::$userPointsCache[$userId];
        }

        return app()
            ->make(self::class)
            ->countPoints($userId);
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
     * $triggerHashData must be serializable.
     *
     * @param $userId
     * @param $triggerHashData
     * @param $triggerName
     * @param $points
     * @param null $pointsDescription
     * @param null $brand
     * @return null|\Railroad\Resora\Entities\Entity
     */
    public function setPoints(
        $userId,
        $triggerHashData,
        $triggerName,
        $points,
        $pointsDescription = null,
        $brand = null
    ) {
        $existing =
            $this->userPointsRepository->query()
                ->where(
                    [
                        'user_id' => $userId,
                        'trigger_hash' => $this->hash($triggerHashData),
                        'trigger_hash_data' => serialize($triggerHashData),
                        'brand' => $brand ?? config('points.brand'),
                    ]
                )
                ->first();

        if (!empty($existing)) {
            return $this->userPointsRepository->update(
                $existing['id'],
                [
                    'trigger_name' => $triggerName,
                    'points' => $points,
                    'points_description' => $pointsDescription,
                    'updated_at' => Carbon::now()
                        ->toDateTimeString(),
                ]
            );
        }

        return $this->userPointsRepository->create(
            [
                'user_id' => $userId,
                'trigger_hash' => $this->hash($triggerHashData),
                'trigger_hash_data' => serialize($triggerHashData),
                'brand' => $brand ?? config('points.brand'),
                'trigger_name' => $triggerName,
                'points' => $points,
                'points_description' => $pointsDescription,
                'created_at' => Carbon::now()
                    ->toDateTimeString(),
            ]
        );
    }

    /**
     * @param $userId
     * @param $triggerHashData
     * @param null $brand
     * @return bool
     */
    public function deletePoints(
        $userId,
        $triggerHashData,
        $brand = null
    ) {
        return $this->userPointsRepository->query()
                ->where(
                    [
                        'user_id' => $userId,
                        'trigger_hash' => $this->hash($triggerHashData),
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

    public static function tierMap()
    {
        $tierMap = config('points.tier_map');

        // Ensures sorted by points ascending so that getRankFromXp functions properly.
        usort(
            $tierMap,
            function ($a, $b) {
                $a = $a['start'];
                $b = $b['start'];

                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 : 1;
            }
        );

        return $tierMap;
    }

    public static function tierDefault()
    {
        return config('points.tier_default');
    }

    public static function getRankFromXp(int $xp)
    {
        $membersTier = null;
        $tierMap = self::tierMap();

        foreach ($tierMap as $tier) {
            /*
             * Sets on each loop until the highest tier with a 'points-base' greater than (or equal to) the xp is
             * reached. Then will continue to loop, but will not set because xp is not greater than (or equal to)!
             * This relies on the list of levels being ordered by minimum-points **ASCENDING**. This is why we
             * order it in the tierMap function above
             */
            if ($xp >= $tier['start']) {
                $membersTier = $tier['name'];
            }
        }
        if (!$membersTier) {
            $membersTier = self::tierDefault();
        }

        return $membersTier;
    }
}