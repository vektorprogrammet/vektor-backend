<?php

namespace App\Controller\Admin;

use App\Entity\Department;
use App\Entity\FieldOfStudy;
use App\Entity\Receipt;
use App\Entity\School;
use App\Entity\Semester;
use App\Entity\Sponsor;
use App\Entity\Team;
use App\Entity\TeamApplication;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(FieldOfStudyCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Vektor Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('Back to the website', 'fas fa-home', 'home');
        yield MenuItem::linkToCrud('FieldOfStudy', 'fas fa-list', FieldOfStudy::class);
        yield MenuItem::linkToCrud('School', 'fas fa-school', School::class);
        yield MenuItem::linkToCrud('Department', 'fas fa-building', Department::class);
        yield MenuItem::linkToCrud('Semester', 'fas fa-calendar-days', Semester::class);
        yield MenuItem::linkToCrud('Sponsor', 'fas fa-money-bill', Sponsor::class);
        yield MenuItem::linkToCrud('Team', 'fas fa-people-group', Team::class);
        yield MenuItem::linkToCrud('Receipt', 'fas fa-dollar-sign', Receipt::class);
        yield MenuItem::linkToCrud('User', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('TeamApplication', 'fas fa-envelope', TeamApplication::class);
    }
}
