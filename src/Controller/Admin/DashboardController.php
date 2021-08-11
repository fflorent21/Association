<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Like;
use App\Entity\NewsLetter;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration du site');
    }


    public function configureMenuItems(): iterable
    {
        yield MenuItem::section("ACCUEIL");
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        
        // Retour sur la page accueil
        yield MenuItem::linkToRoute("Retour au site", 'fas fa-sign-out-alt', 'app_home');

        // Menu
        yield MenuItem::section('GESTION DES DONNEES');

        // gestion des entitées
        yield MenuItem::linkToCrud('Articles', 'fas fa-file-alt', Article::class)->setCssClass('nav-link');
        yield MenuItem::linkToCrud('Catégories', 'fas fa-tags', Category::class)->setCssClass('nav-link');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class)->setCssClass('nav-link');
        yield MenuItem::linkToCrud('Commentaires', 'fas fa-comments', Comment::class)->setCssClass('nav-link');
        yield MenuItem::linkToCrud('Favories', 'fas fa-bookmark', Like::class)->setCssClass('nav-link');
        yield MenuItem::linkToCrud('NewsLetter', 'fas fa-newspaper', NewsLetter::class)->setCssClass('nav-link');
        yield MenuItem::linkToCrud('Contacts', 'fas fa-id-card', Contact::class)->setCssClass('nav-link');

        // la deconnexion
        yield MenuItem::section('DECONNEXION');
        yield MenuItem::linkToLogout('Logout', 'fas fa-times');

        //yield MenuItem::linkToCrud('The Label', 'icon class', EntityClass::class);




    }
}
