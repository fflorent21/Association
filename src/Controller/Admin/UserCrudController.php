<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * Class Constructor
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    // la méthode pour personnaliser les champs à afficher
    public function configureFields(string $pageName): iterable
    {
    
        return [
            IdField::new('id')->OnlyOnIndex(),
            BooleanField::new('isVerified', 'Activé'),
            TextField::new('author', 'Nom d\'utilisateur'),
            TextField::new('first_name', 'Prénom')
            ->hideOnIndex(),
            TextField::new('last_name', 'Nom')
            ->hideOnIndex(),
            EmailField::new('email', 'Adresse électronique'),
            TelephoneField::new('Phone', 'Numéro de téléphone')
            ->hideOnIndex(),
            BooleanField::new('rgpd', 'RGPD'),
            TextField::new('password')->setFormType(RepeatedType::class)->setFormTypeOptions([
                'required' => true,
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
            ])->hideOnDetail()->hideOnIndex(),
            ChoiceField::new('roles')->setChoices(['Administrateur' => 'ROLE_ADMIN', 'Modérateur' => 'ROLE_MODO', 'Utilisateur' => 'ROLE_USER' ])->allowMultipleChoices(),
            AssociationField::new('articles')->hideOnForm(),
            AssociationField::new('comments')->hideOnForm(),
            AssociationField::new('likes')->hideOnForm()
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
            ->setPageTitle('index', 'Gestion des utilisateurs - Le Collectif Taliesin')
            ->setEntityLabelInSingular('un utilisateur')
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

    // Je redéfinie la méthode persist de 'AbstractCrudController'
    public function persistEntity(EntityManagerInterface $em, $user): void
    {
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        parent::persistEntity($em, $user);
    }

    // Je redéfinie la méthode update de 'AbstractCrudController'
    public function updateEntity(EntityManagerInterface $em, $user): void
    {
        if ($user->getPassword() != null) {
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);
        }

        parent::updateEntity($em, $user);
    }
}