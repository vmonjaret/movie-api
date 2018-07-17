<?php

namespace App\Controller\Comment;


use App\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SignalComment
{
    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke(Comment $comment)
    {
        $comment->setSignaled(true);

        return $comment;
    }

}