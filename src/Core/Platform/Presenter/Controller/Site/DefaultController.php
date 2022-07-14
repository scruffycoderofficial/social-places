<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Presenter\Controller\Site
{
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    /**
     * Class DefaultController
     *
     * @package BeyondCapable\Core\Platform\Presenter\Controller\Site
     */
    class DefaultController extends AbstractController
    {
        public function index(): Response
        {
            return $this->render('site/default/index.html.twig');
        }
    }
}
