<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AvatarFormType;
use libphonenumber\PhoneNumberFormat;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberHelper;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public function __construct(protected PhoneNumberHelper $phoneNumberHelper) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'Utilisateurs :')
            ->setPageTitle('new', 'Créer un utilisateur')
            ->setPageTitle('edit', fn(User $user) => (string) $user->getFullname())
            ->setPageTitle('detail', fn(User $user) => (string) $user->getFullname())
            ->setEntityLabelInSingular('un utilisateur')
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(10);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $actions->remove(Crud::PAGE_INDEX, Action::NEW);
        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];

        // Vérifie le contexte et l'instance de l'utilisateur
        $user = $this->getContext()->getEntity()->getInstance();

        return [
            IdField::new('id')->onlyOnIndex(),
            FormField::addFieldset('Détails de l\'utilisateur'),
            TextField::new('firstname', 'Prénom :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Prénom de l\'utilisateur']])
                ->setColumns(6),
            TextField::new('lastname', 'Nom :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Nom de l\'utilisateur']])
                ->setColumns(6),
            EmailField::new('email', 'Email :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Email de l\'utilisateur']]),
            TextField::new('adress', 'Adresse :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Adresse de l\'utilisateur']])
                ->setColumns(6)
                ->hideOnIndex(),
            TextField::new('postalCode', 'Code postal :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Code postal de l\'utilisateur']])
                ->setColumns(6)
                ->hideOnIndex(),
            TextField::new('city', 'Ville :')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Ville de l\'utilisateur']])
                ->setColumns(6)
                ->hideOnIndex(),
            TextField::new('phone', 'Téléphone')
                ->setFormType(PhoneNumberType::class)
                ->setFormTypeOptions([
                    'default_region' => 'FR',
                    'format' => PhoneNumberFormat::NATIONAL,
                    'attr' => ['placeholder' => 'Téléphone de l\'utilisateur']
                ])
                ->setColumns(6)
                ->onlyOnForms(),
            TextField::new('phone', 'Téléphone')
                ->formatValue(function ($value, $entity) {
                    $value = $entity->getPhone();
                    $formattedValue = $this->phoneNumberHelper->format($value, 2);
                    return $formattedValue;
                })
                ->onlyOnIndex(),
            ImageField::new('avatar.imageName', 'Avatar')
                ->setBasePath('images/avatars')
                ->setUploadDir('public/images/avatars')
                ->onlyOnIndex()
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            TextField::new('avatar', 'Avatar :')
                ->setFormType(AvatarFormType::class)
                ->setTranslationParameters(['form.label.delete' => 'Supprimer l\'image'])
                ->hideOnIndex(),
            FormField::addFieldset('Rôles de l\'utilisateur'),
            ChoiceField::new('roles')
                ->setChoices(array_combine($roles, $roles))
                ->allowMultipleChoices()
                ->renderExpanded()
                ->renderAsBadges(),
            FormField::addPanel('Changer le mot de passe')->setIcon('fas fa-key')
                ->onlyWhenUpdating(),
            TextField::new('plainPassword', 'Nouveau mot de passe :')
                ->onlyWhenUpdating()
                ->setRequired(false)
                ->setFormTypeOptions([
                    'attr' => ['placeholder' => 'Nouveau mot de passe de l\'utilisateur'],
                ])
                ->setHelp('Pour modifier le mot de passe, vous devez saisir un nouveau mot de passe. Sinon, laissez le champ vide.')
                ->hideOnIndex()
        ];
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setFirstname(ucfirst($entityInstance->getFirstname()))
            ->setLastname(strtoupper($entityInstance->getLastname()));
        parent::updateEntity($entityManager, $entityInstance);
    }
}
