<?php

namespace App\Controller\Blog;

use App\Form\SearchType;
use App\Entity\Post\Post;
use App\Model\SearchData;
use App\Entity\Post\Comment;
use App\Form\CommentType;
use App\Repository\Post\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/', name: 'post.index', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        Request $request
    ): Response {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $postRepository->findBySearch($searchData);

            return $this->render('pages/post/index.html.twig', [
                'form' => $form->createView(),
                'posts' => $posts
            ]);
        }

        return $this->render('pages/post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $postRepository->findPublished($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/post/{slug}', name: 'post.show', methods: ['GET', 'POST'])]
    public function show(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $comment->setPost($post);
        if ($this->getUser()) {
            $comment->setAuthor($this->getUser());
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Your comment has been registered. It will be moderated as soon as possible.');

            return $this->redirectToRoute('post.show', ['slug' => $post->getSlug()]);
        }

        return $this->render('pages/post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }
}
