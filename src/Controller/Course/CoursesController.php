<?php

namespace App\Controller\Course;

use App\Entity\User;
use App\Form\ButtonFormType;
use App\Repository\LessonRepository;
use App\Repository\CoursesRepository;
use App\Repository\SectionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CoursesController extends AbstractController
{
    protected $coursesRepository;
    protected $sectionsRepository;
    protected $em;

    public function __construct(CoursesRepository $coursesRepository, SectionsRepository $sectionsRepository, EntityManagerInterface $em)
    {
        $this->coursesRepository = $coursesRepository;
        $this->sectionsRepository = $sectionsRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas le droit d\'accéder à cette page')]
    #[Route('/courses/{program_slug}/{slug}', name: 'courses_section', priority: -1)]
    public function section($slug, LessonRepository $lessonRepository): Response
    {
        /** @var User */
        $user = $this->getUser();
        $sections = $this->sectionsRepository->findAll();
        $section = $this->sectionsRepository->findOneBy([
            'slug' => $slug
        ]);
        $count = $this->coursesRepository->countNumberCoursesBySection($section);
        $nbrCourses = $this->coursesRepository->countAll();
        $nbrLessonsDone = $lessonRepository->countLessonsDoneByUser($user);

        if (!$section) {
            throw $this->createNotFoundException("La section demandée n'existe pas");
        }

        return $this->render('courses/section.html.twig', [
            'section' => $section,
            'sections' => $sections,
            'count' => $count,
            'nbrCourses' => $nbrCourses,
            'nbrLessonsDone' => $nbrLessonsDone,
            'lessons' => $lessonRepository->findBy(['user' => $user->getId()])
        ]);
    }

    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas le droit d\'accéder à cette page')]
    #[Route('/courses/{program_slug}/{section_slug}/{slug}', name: 'courses_show', priority: -1)]
    public function show($slug, LessonRepository $lessonRepository, Request $request, UploaderHelper $uploaderHelper): Response
    {
        $currentUrl = $request->getUri();
        $response = new Response();
        $response->headers->setCookie(new Cookie('url_visited', $currentUrl, strtotime('+1 month')));

        
        /** @var User */
        $user = $this->getUser();
        
        $course = $this->coursesRepository->findOneBy([
            'slug' => $slug
        ]);
        
        $filePath = $uploaderHelper->asset($course, 'partialFile');
        $content = null;
        if ($filePath) {
            $fullPath = $this->getParameter('kernel.project_dir') . '/public' . $filePath;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
            }
        }

        // Total des cours en BDD
        $nbrCourses = $this->coursesRepository->countAll();
        // Nombre de leçons effectuées par l'utilisateur connecté
        $nbrLessonsDone = $lessonRepository->countLessonsDoneByUser($user);

        $sections = $this->sectionsRepository->findAll();

        if (!$course) {
            throw $this->createNotFoundException("Le cours demandé n'existe pas");
        }

        // On veut récupérer la leçon en-cours par l'utilisateur connecté
        $lesson = $lessonRepository->getLessonByUserByCourse($user, $course);

        $form = $this->createForm(ButtonFormType::class);

        return $this->render('courses/show.html.twig', [
            'course' => $course,
            'sections' => $sections,
            'lesson' => $lesson,
            'form' => $form,
            'nbrCourses' => $nbrCourses,
            'nbrLessonsDone' => $nbrLessonsDone,
            'lessons' => $lessonRepository->findBy(['user' => $user->getId()]),
            'fileContent' => $content,
        ], $response);
    }
}
