<?php

namespace App\Validator;

use App\Entity\Courses;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class WordCountConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof WordCountConstraint) {
            throw new UnexpectedTypeException($constraint, WordCountConstraint::class);
        }

        // Vérification que l'option 'courses' est présente
        if ($constraint->courses === null) {
            throw new UnexpectedValueException($constraint->courses, Courses::class);
        }

        $courses = $constraint->courses;  // Récupère l'entité Courses passée dans l'option
        $correctText = $courses->getCorrectionText();

        // Calcul du nombre de mots
        $userWordCount = str_word_count($value);
        $correctWordCount = str_word_count($correctText);

        if ($userWordCount < $correctWordCount) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ userCount }}', $userWordCount)
                ->setParameter('{{ correctCount }}', $correctWordCount)
                ->addViolation();
        }
    }
}