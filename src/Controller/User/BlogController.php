<?php

namespace App\Controller\User;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

#[Route('/User/', name: 'app_user_')]
class BlogController extends AbstractController
{

    private $em;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->security = $security;
        $this->em = $entityManager;
    }

    #[Route('', name: 'index')]
    public function index(PostRepository $repository): Response
    {
        $posts = $repository->findby([], ["published_at" => "DESC"]);
        return $this->render('User/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    #[Route('post{id}', name: 'showPost')]
    public function showPost($id, Post $post, Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $comments = $commentRepository->findby(["post" => $id], ["published_at" => "DESC"]);
        // dd($post->getTags());
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user = $this->security->getUser()) {
                $comment->setAuthor($user);
                $comment->setPost($post);
                $this->em->persist($comment);
                $this->em->flush();
                return $this->redirectToRoute("app_user_showPost", [
                    'id' => $post->getId(),
                    'post' => $post,
                    'comments' => $comments,
                    'form' => $form,
                ]);
            }
            return $this->redirectToRoute("app_login");
        }
        return $this->render('User/showPost.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }
}
