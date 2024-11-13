<?php

namespace App\Validator;

use App\Entity\Courses;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class WordCountConstraint extends Constraint
{
    public string $message = 'Le texte contient {{ userCount }} mots, mais il devrait en contenir {{ correctCount }}.';
    public ?Courses $courses = null;  // Propriété pour recevoir l'option courses

    // Constructeur modifié pour accepter l'option 'courses'
    public function __construct(?array $options = null)
    {
        parent::__construct($options);

        // Si 'courses' est passé, l'affecter à la propriété
        if (isset($options['courses'])) {
            $this->courses = $options['courses'];
        }
    }
}