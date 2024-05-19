<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route("", name: "app_index")]
    public function home(): Response
    {
        return $this->redirectToRoute("app_user_index");
    }
}
