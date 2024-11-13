<?php

namespace App\Service;

use App\Entity\Courses;
use App\Form\DictationFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class DictationService
{
    public function __construct(protected FormFactoryInterface $formFactory) {}

    public function createAndHandleDictationForm(Courses $courses, Request $request): array
    {
        // Initialisation du formulaire et des erreurs
        $form = null;
        $errors = null;
        $successMessage = null;

        if ($courses->getContentType() === Courses::TYPE_AUDIO) {
            // Passer l'entité Courses comme option ici
            $form = $this->formFactory->create(DictationFormType::class, null, [
                'courses' => $courses,  // L'option 'courses' est passée ici
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $userText = $form->get('dictationText')->getData();
                $errors = $this->compareText($userText, $courses->getCorrectionText());

                if (empty($errors)) {
                    $successMessage = 'Félicitations, vous n\'avez fait aucune faute !';
                }
            }
        }

        return [
            'form' => $form,
            'errors' => $errors,
            'successMessage' => $successMessage
        ];
    }

    private function compareText(string $userText, string $correctText): array
    {
        $userSentences = preg_split('/(?<=[.!?])\s+/', trim($userText));
        $correctSentences = preg_split('/(?<=[.!?])\s+/', trim($correctText));
        
        $errors = [];
        foreach ($correctSentences as $sentenceIndex => $correctSentence) {
            $correctWords = explode(' ', $correctSentence);
            $userWords = explode(' ', $userSentences[$sentenceIndex] ?? '');
            
            foreach ($correctWords as $i => $expectedWord) {
                $userWord = $userWords[$i] ?? '...';
                if ($userWord !== $expectedWord) {
                    $errors[] = [
                        'userWord' => $userWord,
                        'expectedWord' => $expectedWord,
                        'sentence' => $sentenceIndex + 1
                    ];
                }
            }
        }
        
        return $errors;
    }
}
