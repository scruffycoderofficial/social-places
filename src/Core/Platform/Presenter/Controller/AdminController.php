<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Presenter\Controller
{
    use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    /**
     * Class AdminController
     *
     * @package BeyondCapable\Core\Platform\Presenter\Controller
     */
    class AdminController extends AbstractDashboardController
    {
        #[Route('/admin', name: 'admin')]
        public function index(): Response
        {
            return $this->render('admin/index.html.twig');
        }

        public function configureDashboard(): Dashboard
        {
            return Dashboard::new()
                ->setTitle('Capable Platform')
                ->disableDarkMode()
                ->generateRelativeUrls();
        }

        /**
         * {@inheritDoc}
         */
        public function configureMenuItems(): iterable
        {
            return [];
        }
    }
}
