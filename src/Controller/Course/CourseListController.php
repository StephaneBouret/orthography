<?php

namespace App\Controller\Course;

use App\Entity\User;
use App\Repository\LessonRepository;
use App\Repository\CoursesRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CourseListController extends AbstractController
{
    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas le droit d\'accéder à cette page')]
    #[Route('/courses', name: 'app_courses_list')]
    public function list(ProgramRepository $programRepository, CoursesRepository $coursesRepository, LessonRepository $lessonRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $programs = $programRepository->findBy([], ['name' => 'ASC']);

        // Total des cours en BDD
        $nbrCourses = $coursesRepository->countAll();
        // Nombre de leçons effectuées par l'utilisateur connecté
        $nbrLessonsDone = $lessonRepository->countLessonsDoneByUser($user);

        return $this->render('courses/list.html.twig', [
            'programs' => $programs,
            'nbrCourses' => $nbrCourses,
            'nbrLessonsDone' => $nbrLessonsDone
        ]);
    }
}
