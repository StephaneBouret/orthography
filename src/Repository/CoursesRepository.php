<?php

namespace App\Repository;

use App\Data\SearchCourseData;
use App\Entity\Courses;
use App\Entity\Sections;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Courses>
 */
class CoursesRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Courses::class);
        $this->paginator = $paginator;
    }

    public function save(Courses $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Courses $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countNumberCoursesBySection(Sections $sections): int
    {
        $result = $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->andWhere('c.section = :sections')
            ->setParameter('sections', $sections)
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function countAll(): int
    {
        $result = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function countCoursesBySections(): array
    {
        return $this->createQueryBuilder('c')
            ->select('s.name AS section_name, COUNT(c.id) AS course_count')
            ->join('c.section', 's')
            ->groupBy('s.id')
            ->getQuery()
            ->getResult();
    }

    public function findAllNames(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.slug')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Récupère les cours en lien avec une recherche
     *
     * @param PaginationInterface
     */
    public function findSearch(SearchCourseData $search): PaginationInterface
    {
        $query = $this->getSearchQuery($search)->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            6
        );
    }

    private function getSearchQuery(SearchCourseData $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('c')
            ->select('c', 's')
            ->join('c.section', 's');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('c.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->sections)) {
            $query = $query
                ->andWhere('s.id IN (:sections)')
                ->setParameter('sections', $search->sections);
        }

        $query->orderBy('c.id', 'ASC');

        return $query;
    }

    public function countItems(SearchCourseData $search): int
    {
        $query = $this->getSearchQuery($search)->getQuery();
        return count($query->getResult());
    }


    //    /**
    //     * @return Courses[] Returns an array of Courses objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Courses
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
