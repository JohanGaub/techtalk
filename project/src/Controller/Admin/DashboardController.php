<?php

namespace App\Controller\Admin;

use App\Entity\Agency;
use App\Entity\Meetup;
use App\Entity\Topic;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    public $container;

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Option 1. You can make your dashboard redirect to some common page of your backend
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Techtalk');
    }

    public function configureMenuItems(): iterable
    {
        //        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Administration');
        yield MenuItem::linkToCrud('User', 'fa-solid fa-users-gear', User::class);
        yield MenuItem::linkToCrud('Meetup', 'fa-brands fa-meetup', Meetup::class);
        yield MenuItem::linkToCrud('Topic', 'fa-solid fa-message', Topic::class);
        yield MenuItem::linkToCrud('Agency', 'fa-solid fa-location-dot', Agency::class);
        //        yield MenuItem::linkToCrud('Vote', 'fa-solid fa-star-half-stroke', ::class);
        //        yield MenuItem::linkToCrud('Participant', 'fa-solid fa-people-line', ::class);

        yield MenuItem::section('Statistics');
    }
}
