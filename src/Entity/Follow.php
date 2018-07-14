<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource()
 */
class Follow
{
    /**
     * @var User $follow
     */
    private $follow;

    /**
     * @return mixed
     */
    public function getFollow()
    {
        return $this->follow;
    }

    /**
     * @param mixed $follow
     * @return Follow
     */
    public function setFollow($follow)
    {
        $this->follow = $follow;
        return $this;
    }
}
