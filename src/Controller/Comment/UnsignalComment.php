<?php

namespace App\Controller\Comment;

use App\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UnsignalComment
{
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    public function __invoke(Comment $comment)
    {
        $comment->setSignaled(false);

        return $comment;
    }

}