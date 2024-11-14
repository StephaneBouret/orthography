<?php

namespace App\Controller\Course;

use App\Entity\User;
use App\Form\SearchFormType;
use App\Data\SearchCourseData;
use App\Repository\LessonRepository;
use App\Repository\CoursesRepository;
use App\Repository\ProgramRepository;
use App\Repository\SectionsRepository;
use Symfony\Component\HttpFoundation\Request;
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

    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas le droit d\'accéder à cette page')]
    #[Route('/courses/search', name: 'courses_search')]
    public function display(Request $request, CoursesRepository $coursesRepository, SectionsRepository $sectionsRepository)
    {
        $data = new SearchCourseData;
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        $courses = $coursesRepository->findSearch($data);
        $totalItems = $coursesRepository->countItems($data);

        return $this->render('courses/display.html.twig', [
            'form' => $form,
            'courses' => $courses,
            'totalItems' => $totalItems,
        ]);
    }
}
