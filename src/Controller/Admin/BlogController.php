<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/', name: 'app_')]
class BlogController extends AbstractController
{

    private $em;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->security = $security;
        $this->em = $entityManager;
    }



    #[Route('manage', name: 'manage_posts')]
    public function index(PostRepository $repository): Response
    {
        $posts = $repository->findAll();
        return $this->render('Admin/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    #[Route('post{id}', name: 'showPost')]
    public function showPost(Post $post): Response
    {
        return $this->render('Admin/showPost.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('delete{id}', name: 'delete')]
    public function delete(Post $post, Request $request): Response
    {
        $submittedToken = $request->getPayload()->get('token');

        // 'delete-item' is the same value used in the template to generate the token
        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $this->em->remove($post);
            $this->em->flush();
        }
        return $this->redirectToRoute("app_manage_posts");
    }


    #[Route('create', name: 'create')]
    public function create(Request $request): Response
    {

        $post = new Post();
        $user = $this->security->getUser();
        $post->setAuthor($user);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute("app_showPost", [
                "id" => $post->getId()
            ]);
        }

        return $this->render("Admin/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('edit{id}', name: 'edit')]
    public function edit(Post $post, Request $request): Response
    {

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTimeImmutable());
            $this->em->flush();
            return $this->redirectToRoute("app_showPost", [
                "id" => $post->getId()
            ]);
        }

        return $this->render("Admin/edit.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
