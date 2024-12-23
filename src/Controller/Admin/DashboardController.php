<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
use App\Entity\Courses;
use App\Entity\Invitation;
use App\Entity\Navigation;
use App\Entity\NewsLetter;
use App\Entity\Program;
use App\Entity\Question;
use App\Entity\Sections;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Orthography');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Cours', 'fas fa-book-open', Courses::class);
        yield MenuItem::linkToCrud('Invitations', 'fas fa-envelope', Invitation::class);
        yield MenuItem::linkToCrud('Navigation', 'fa-solid fa-route', Navigation::class);
        yield MenuItem::linkToCrud('Newsletter', 'fa-solid fa-envelope-open-text', NewsLetter::class);
        yield MenuItem::linkToCrud('Questions', 'fa-regular fa-circle-question', Question::class);
        yield MenuItem::linkToCrud('Réponses', 'fa-regular fa-comment', Answer::class);
        yield MenuItem::linkToCrud('Sections', 'fa-fw fas fa-section', Sections::class);
        yield MenuItem::linkToCrud('Programmes', 'fas fa-list-check', Program::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-home', 'homepage');
    }
}
