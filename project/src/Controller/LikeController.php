<?php

namespace App\Controller;

use App\Entity\Post\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LikeController extends AbstractController
{
    #[Route('/like/post/{id}', name: 'like.post', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function like(Post $post, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        if ($post->isLikedByUser($user)) {
            $post->removeLike($user);
            $manager->flush();

            return $this->json([
                'message' => 'The like has been removed.',
            'nbLike'=>$post->howManyLikes()]);
        }
 
        $post->addLike($user);
        $manager->flush();

        return $this->json(['message' => 'The like has been added.','nbLike'=>$post->howManyLikes() ]);
    }
}
