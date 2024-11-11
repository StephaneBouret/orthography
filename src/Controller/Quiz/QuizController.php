<?php

namespace App\Controller\Quiz;

use DateTimeImmutable;
use App\Entity\QuizResult;
use App\Service\QuizResultService;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\SectionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizController extends AbstractController
{
    #[Route('/quiz/submit', name: 'quiz_submit', methods: ['POST'])]
    public function submitQuiz(Request $request, QuestionRepository $questionRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if ($data === null) {
                return new JsonResponse(['error' => 'Donnée JSON invalide'], 400);
            }

            // Récupérer la question et la réponse envoyées
            $questionId = $data['questionId'] ?? null;
            $answerId = $data['answerId'] ?? null;

            if (!$questionId || !$answerId) {
                return new JsonResponse(['error' => 'QuestionId ou answerId manquants'], 400);
            }

            // Récupérer la question depuis la base de données
            $question = $questionRepository->find($questionId);

            if (!$question) {
                return new JsonResponse(['error' => 'Question non trouvée'], 404);
            }

            // Trouver la réponse correcte de la question
            $correctAnswer = $question->getCorrectAnswer();

            if (!$correctAnswer) {
                return new JsonResponse(['error' => 'Il n\'y a pas de réponse correcte pour cette question'], 400);
            }

            // Vérifier si la réponse de l'utilisateur est correcte
            $isCorrect = ($answerId == $correctAnswer->getId());

            // Retourner la réponse avec le résultat de la validation
            return new JsonResponse([
                'correct' => $isCorrect,
                'explanation' => $question->getExplanation() // Renvoyer l'explication
            ]);
        } catch (\Exception $e) {
            // Retourner une réponse JSON avec l'erreur
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/quiz/finalize', name: 'quiz_finalize', methods: ['GET', 'POST'])]
    public function finalizeQuiz(Request $request, EntityManagerInterface $em, AnswerRepository $answerRepository, SectionsRepository $sectionsRepository, QuizResultService $quizResultService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if ($data === null || empty($data['answers']) || empty($data['sectionId'])) {
            return new JsonResponse(['error' => 'Données non valides, pas de réponse ou section disponible'], 400);
        }
    
        $user = $this->getUser();
        $score = 0;
        $now = new DateTimeImmutable();
    
        // Récupérer la section du quiz
        $section = $sectionsRepository->find($data['sectionId']);
    
        if (!$section) {
            return new JsonResponse(['error' => 'Section non trouvée'], 404);
        }
    
        // Récupération des slugs pour l'URL
        $course = $section->getCourses()->last();
        $programSlug = $section->getProgram()->getSlug();
        $sectionSlug = $section->getSlug();
        $courseSlug = $course ? $course->getSlug() : null;
    
        // Vérifier s'il existe une tentative avec score 0 ou une tentative complète
        $existingAttempt = $quizResultService->getLastAttemptId($user, $sectionSlug);
    
        // Calcul du score
        foreach ($data['answers'] as $answerData) {
            if (empty($answerData['answerId'])) {
                continue; // Si answerId est null ou vide, ignorer cette réponse
            }
    
            $selectedAnswer = $answerRepository->find($answerData['answerId']);
            if ($selectedAnswer && $selectedAnswer->getIsCorrect()) {
                $score++;
            }
        }
    
        if ($existingAttempt) {
            if ($existingAttempt['score'] === 0) {
                // Mise à jour de la tentative existante avec le score final
                $quizResultService->updateAttemptScore($existingAttempt['id'], $score);
                $attemptId = $existingAttempt['id'];
            } else {
                // Créer une nouvelle tentative car la dernière est déjà complète
                $quizAttempt = new QuizResult();
                $quizAttempt->setUser($user)
                    ->setCompletedAt($now)
                    ->setScore($score)
                    ->setSection($section);
                $em->persist($quizAttempt);
                $em->flush();
                $attemptId = $quizAttempt->getId();
            }
        } else {
            // Créer une nouvelle tentative si aucune tentative existante n'a été trouvée
            $quizAttempt = new QuizResult();
            $quizAttempt->setUser($user)
                ->setCompletedAt($now)
                ->setScore($score)
                ->setSection($section);
            $em->persist($quizAttempt);
            $em->flush();
            $attemptId = $quizAttempt->getId();
        }
    
        // Génération de l'URL de redirection vers la page de résultats
        $redirectUrl = $this->generateUrl('courses_quiz_attempt', [
            'program_slug' => $programSlug,
            'section_slug' => $sectionSlug,
            'slug' => $courseSlug,
            'attemptId' => $attemptId
        ]);
    
        return new JsonResponse([
            'attemptId' => $attemptId,
            'score' => $score,
            'totalQuestions' => count($data['answers']),
            'redirectUrl' => $redirectUrl,
        ]);
    }    
}
