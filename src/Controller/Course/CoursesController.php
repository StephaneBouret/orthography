<?php

namespace App\Controller\Course;

use App\Entity\User;
use App\Form\ButtonFormType;
use App\Service\QuizService;
use App\Service\UrlCookieService;
use App\Service\CourseFileService;
use App\Service\QuizResultService;
use App\Repository\LessonRepository;
use App\Repository\CoursesRepository;
use App\Repository\SectionsRepository;
use App\Repository\NavigationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoursesController extends AbstractController
{
    protected $coursesRepository;
    protected $sectionsRepository;
    protected $lessonRepository;
    protected $em;

    public function __construct(CoursesRepository $coursesRepository, SectionsRepository $sectionsRepository, EntityManagerInterface $em, LessonRepository $lessonRepository)
    {
        $this->coursesRepository = $coursesRepository;
        $this->sectionsRepository = $sectionsRepository;
        $this->em = $em;
        $this->lessonRepository = $lessonRepository;
    }

    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas le droit d\'accéder à cette page')]
    #[Route('/courses/{program_slug}/{slug}', name: 'courses_section', priority: -1)]
    public function section($slug): Response
    {
        /** @var User */
        $user = $this->getUser();
        $sections = $this->sectionsRepository->findAll();
        $section = $this->sectionsRepository->findOneBy([
            'slug' => $slug
        ]);
        $count = $this->coursesRepository->countNumberCoursesBySection($section);
        $nbrCourses = $this->coursesRepository->countAll();
        $nbrLessonsDone = $this->lessonRepository->countLessonsDoneByUser($user);

        if (!$section) {
            throw $this->createNotFoundException("La section demandée n'existe pas");
        }

        return $this->render('courses/section.html.twig', [
            'section' => $section,
            'sections' => $sections,
            'count' => $count,
            'nbrCourses' => $nbrCourses,
            'nbrLessonsDone' => $nbrLessonsDone,
            'lessons' => $this->lessonRepository->findBy(['user' => $user->getId()])
        ]);
    }

    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas le droit d\'accéder à cette page')]
    #[Route('/courses/{program_slug}/{section_slug}/{slug}', name: 'courses_show', priority: -1)]
    public function show($slug, $section_slug, Request $request, NavigationRepository $navigationRepository, QuizService $quizService, QuizResultService $quizResultService, CourseFileService $courseFileService): Response
    {
        $currentUrl = $request->getUri();
        $response = new Response();
        $response->headers->setCookie(new Cookie('url_visited', $currentUrl, strtotime('+1 month')));


        /** @var User */
        $user = $this->getUser();

        $course = $this->coursesRepository->findOneBy([
            'slug' => $slug
        ]);

        $navigation = $navigationRepository->findAll();

        $content = $courseFileService->getFileContent($course);

        // Total des cours en BDD
        $nbrCourses = $this->coursesRepository->countAll();
        // Nombre de leçons effectuées par l'utilisateur connecté
        $nbrLessonsDone = $this->lessonRepository->countLessonsDoneByUser($user);

        $sections = $this->sectionsRepository->findAll();

        if (!$course) {
            throw $this->createNotFoundException("Le cours demandé n'existe pas");
        }

        // On veut récupérer la leçon en-cours par l'utilisateur connecté
        $lesson = $this->lessonRepository->getLessonByUserByCourse($user, $course);

        // Récupération des questions du quiz pour la section du cours
        $section = $this->sectionsRepository->findOneBy(['slug' => $section_slug]);
        $quizData = $quizService->getQuizData($section);

        $attemptId = $request->get('attemptId');

        // On récupère la dernière tentative de l'utilisateur pour la section en cours
        $lastAttempt = $quizResultService->getLastAttemptId($user, $section_slug);
        $lastAttemptId = $lastAttempt ? $lastAttempt['id'] : null;
        $lastAttemptScore = $lastAttempt ? $lastAttempt['score'] : 0;

        // Condition pour afficher les résultats
        $displayResults = (!$attemptId && $lastAttemptScore > 0) || ($attemptId && $attemptId == $lastAttemptId && $lastAttemptScore > 0);

        // Si on doit afficher les résultats, on récupère les résultats de la dernière tentative
        $quizAttemptResults = $displayResults
            ? $quizResultService->getQuizAttemptResults($user, $lastAttemptId)
            : ['quizResults' => [], 'totalQuestions' => count($section->getQuestions())];

        $form = $this->createForm(ButtonFormType::class);

        return $this->render('courses/show.html.twig', [
            'course' => $course,
            'sections' => $sections,
            'lesson' => $lesson,
            'form' => $form,
            'nbrCourses' => $nbrCourses,
            'nbrLessonsDone' => $nbrLessonsDone,
            'lessons' => $this->lessonRepository->findBy(['user' => $user->getId()]),
            'fileContent' => $content,
            'navigation' => $navigation,
            'questions' => $quizData['questions'],
            'count' => $quizData['count'],
            'csrf_token' => $quizData['csrf_token'],
            'section' => $section,
            'quizResults' => $quizAttemptResults['quizResults'],
            'totalQuestions' => $quizAttemptResults['totalQuestions'],
        ], $response);
    }
}
