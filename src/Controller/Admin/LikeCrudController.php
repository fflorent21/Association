<?php

namespace App\Controller\Admin;

use App\Entity\Like;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LikeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Like::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Article'),
            AssociationField::new('article')->setRequired(true),
            FormField::addPanel('Auteur'),
            AssociationField::new('user')->setRequired(true),
            IdField::new('id')->onlyOnIndex(),
            DateTimeField::new('create_at', 'Date de création')->hideOnForm()
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
            ->setPageTitle('index', 'Gestion des Favoris - Le Collectif Taliesin')
            ->setEntityLabelInSingular('un commentaire')
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
