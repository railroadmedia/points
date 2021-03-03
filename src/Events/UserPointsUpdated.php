<?php

namespace Railroad\Points\Events;

class UserPointsUpdated
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var string|null
     */
    public $brand;

    /**
     * UserPointsUpdated constructor.
     * @param integer $userId
     * @param string|null $brand
     */
    public function __construct(int $userId, $brand = null)
    {
        $this->userId = $userId;
        $this->brand = $brand;
    }
}