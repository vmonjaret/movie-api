<?php

namespace App\Controller\Comment;


use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class GetSignaledComments
{
    private $commentRepository;

    /**
     * GetSignaledComments constructor.
     * @param $commentRepository
     */
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    public function __invoke()
    {
        $comments = $this->commentRepository->findBy(array('signaled' => true));

        return $comments;
    }

}