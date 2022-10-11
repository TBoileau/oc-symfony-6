<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Doctrine\Entity\Comment;

interface CommentTrickInterface
{
    public function __invoke(Comment $comment): void;
}
