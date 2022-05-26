<?php

namespace BeyondCapable\Application\Controller\Admin;

use BeyondCapable\Entity\Blog\Tag;
use BeyondCapable\Entity\Blog\Post;
use BeyondCapable\Entity\Admin\User;
use BeyondCapable\Entity\Admin\Contact;
use BeyondCapable\Entity\Admin\Meeting\Meeting;
use BeyondCapable\Entity\Admin\Meeting\Request;
use BeyondCapable\Entity\Admin\Meeting\Schedule;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
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
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Management');
        yield MenuItem::subMenu('People', 'fa fa-users')->setSubItems([
            MenuItem::linkToCrud('List', 'fas fa-list', User::class),

        ]);
        yield MenuItem::subMenu('Clients', 'fa-solid fa-address-book')->setSubItems([
            MenuItem::linkToCrud('List', 'fas fa-list', Contact::class),
        ]);

        yield MenuItem::section('Administration');
        yield MenuItem::subMenu('Meetings', 'fa-solid fa-handshake')->setSubItems([
            MenuItem::linkToCrud('Requests', 'fas fa-list', Request::class),
            MenuItem::linkToCrud('Schedules', 'fas fa-list', Schedule::class),
            MenuItem::linkToCrud('Meetings', 'fas fa-list', Meeting::class),
        ]);

        yield MenuItem::section('Platform Builder');

        yield MenuItem::section('Resources');
        yield MenuItem::subMenu('Vacancies', 'fas fa-list')->setSubItems([
            MenuItem::linkToCrud('Jobs', 'fas fa-list', Post::class),
            MenuItem::linkToCrud('Tags', 'fas fa-list', Tag::class),
        ]);
    }
}
