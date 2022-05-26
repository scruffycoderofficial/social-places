<?php

namespace BeyondCapable\Controller\Site\Portfolio;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/portfolio")
     */
    public function index(): Response
    {
        return $this->render('site/portfolio/index.html.twig');
    }
}
