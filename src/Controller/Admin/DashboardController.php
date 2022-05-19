<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            ->setTitle('Beyond Capable Admin')
            ->disableDarkMode()
            ->generateRelativeUrls();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::subMenu('Users', 'fa fa-users')->setSubItems([
            MenuItem::linkToCrud('List', 'fas fa-list', User::class),

        ]);

        yield MenuItem::subMenu('Contacts', 'fa-solid fa-address-book')->setSubItems([
            MenuItem::linkToCrud('List', 'fas fa-list', Contact::class),
        ]);
    }
}
