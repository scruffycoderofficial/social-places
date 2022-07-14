<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Presenter\Controller
{

    use BeyondCapable\Core\Platform\Presenter\Controller\Admin\PeopleCrudController;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
    use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

    use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
    use Symfony\Component\HttpFoundation\Response;

    /**
     * Class AdminController
     *
     * @package BeyondCapable\Core\Platform\Presenter\Controller
     */
    class DashboardController extends AbstractDashboardController
        implements DashboardControllerInterface
    {
        public function index(): Response
        {
            return $this->render('admin/index.html.twig');
        }

        public function configureDashboard(): Dashboard
        {
            return Dashboard::new()
                ->setTitle('Capable Platform')
                ->disableDarkMode();
        }

        public function setDashboard(){}

        /**
         * {@inheritDoc}
         */
        public function configureMenuItems(): iterable
        {
            return [];
        }
    }
}
