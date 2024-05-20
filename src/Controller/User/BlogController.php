<?php

namespace App\Controller\User;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PostRepository;

#[Route('/User/', name: 'app_user_')]
class BlogController extends AbstractController
{

    #[Route('', name: 'index')]
    public function index(PostRepository $repository): Response
    {
        $posts = $repository->findby([], ["published_at" => "DESC"]);
        return $this->render('User/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    #[Route('post{id}', name: 'showPost')]
    public function showPost(Post $post): Response
    {
        return $this->render('User/showPost.html.twig', [
            'post' => $post,
        ]);
    }
}
