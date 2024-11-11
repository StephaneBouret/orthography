<?php

namespace App\Controller\Quiz;

use App\Service\LessonService;
use App\Service\QuizResultService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizResultsController extends AbstractController
{
    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas le droit d\'accéder à cette page')]
    #[Route('/courses/{program_slug}/{section_slug}/{slug}/attempts/{attemptId}', name: 'courses_quiz_attempt')]
    public function showQuizAttemptResults($attemptId, $slug, QuizResultService $quizResultService, LessonService $lessonService): Response
    {
        $user = $this->getUser();
        $quizData = $quizResultService->getQuizAttemptResults($user, $attemptId);
        $lessonService->handleLessonUpdate($slug, $user);

        return $this->redirectToRoute('courses_show', [
            'quizResults' => $quizData['quizResults'],
            'program_slug' => $quizData['program_slug'],
            'section_slug' => $quizData['section_slug'],
            'slug' => $quizData['slug'],
            'attemptId' => $attemptId,
        ]);
    }

    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas le droit d\'accéder à cette page')]
    #[Route('/courses/{program_slug}/{section_slug}/{slug}/retry', name: 'courses_quiz_retry')]
    public function retryQuiz($program_slug, $section_slug, $slug, QuizResultService $quizResultService): Response
    {
        $user = $this->getUser();

        // Créer une nouvelle tentative
        $newAttemptId = $quizResultService->createNewAttempt($user, $section_slug, true);

        // Redirige vers la page de quiz avec le nouvel `attemptId`
        return $this->redirectToRoute('courses_show', [
            'program_slug' => $program_slug,
            'section_slug' => $section_slug,
            'slug' => $slug,
            'attemptId' => $newAttemptId,
        ]);
    }
}
