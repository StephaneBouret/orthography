<?php

namespace App\Controller\Admin;

use App\Entity\Question;
use App\Entity\Sections;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class QuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setEntityLabelInSingular('Question')
            ->setEntityLabelInPlural('Questions')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des Questions')
            ->setPageTitle(Crud::PAGE_EDIT, 'Éditer la Question')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer une Question')
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(10);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title', 'Titre de la Question')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Tapez le titre']]),
            TextField::new('text', 'Texte de la Question')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Tapez le texte']]),
            TextEditorField::new('explanation', 'Explication de la réponse'),
            AssociationField::new('section', 'Section associée')
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Sections::class)->createQueryBuilder('s')->orderBy('s.name')
                )
                ->autocomplete(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('title')
            ->add('section');
    }
}