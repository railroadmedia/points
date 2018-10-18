<?php

namespace Railroad\Points\Repositories;

use Railroad\Resora\Queries\BaseQuery;
use Railroad\Resora\Repositories\RepositoryBase;

class UserPointsRepository extends RepositoryBase
{
    /**
     * @return mixed
     */
    protected function connection()
    {
        return app('db')->connection(config('points.database_connection_name'));
    }

    /**
     * @return BaseQuery|RepositoryBase
     */
    protected function newQuery()
    {
        return (new BaseQuery($this->connection()))->from(config('points.tables.user_points'));
    }

}