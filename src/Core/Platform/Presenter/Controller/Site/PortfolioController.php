<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Presenter\Controller\Site
{
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    /**
     * Class PortfolioController
     *
     * @package BeyondCapable\Core\Platform\Presenter\Controller\Site
     */
    class PortfolioController extends AbstractController
    {
        #[Route('/portfolio', name: 'portfolio')]
        public function index(): Response
        {
            return $this->render('site/portfolio/index.html.twig');
        }
    }
}