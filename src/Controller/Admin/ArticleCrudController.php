<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Auteur'),
            IdField::new('id')->onlyOnIndex()
            ->hideOnForm(),
            AssociationField::new('user')->setRequired(true),
            FormField::addPanel("Status (P = Published, NP = Not Published)"),
            TextField::new('status', 'Status'),
            FormField::addPanel('Articles'),
            TextField::new('title', 'Titre'),
            TextField::new('subTitle', 'Résumé'),
            ImageField::new('image', 'Image')
            ->setBasePath('/uploads/images/')
            ->hideOnForm(),
            ImageField::new('image', 'Image')
            ->setUploadDir('public/uploads/images/')
            ->hideOnIndex(),
            DateTimeField::new('create_at', 'Date de création')
            ->onlyOnIndex(),
            TextEditorField::new('content', 'Contenu')->onlyOnForms(), 
            FormField::addPanel('Catégories'),
            AssociationField::new('category')->setRequired(true),
            AssociationField::new('comments')->onlyOnIndex(),   
        ];
    }
    
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

    // fonction pour changer le titre de la page
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des articles - Le Collectif Taliesin')
            ->setEntityLabelInSingular('Article')
            ->setPaginatorPageSize(10);
    }


    public function configureActions(Actions $actions): Actions
    {

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fa fa-plus');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit')->setLabel(false)->setCssClass('action-edit text-warning');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash')->setLabel(false);
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye')->setLabel(false)->setCssClass('action-detail text-success');
            });
    }
}