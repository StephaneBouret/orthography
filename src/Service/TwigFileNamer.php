<?php

namespace App\Service;

use Vich\UploaderBundle\Util\Transliterator;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TwigFileNamer implements NamerInterface
{
    public function __construct(private readonly Transliterator $transliterator)
    {
    }
    
    public function name(object $object, PropertyMapping $mapping): string
    {
        // Récupère le fichier uploadé
        /** @var UploadedFile $file */
        $file = $mapping->getFile($object);

        // Récupère le nom original sans l'extension
        $originalName = $file->getClientOriginalName();
        $originalName = $this->transliterator->transliterate($originalName);
        
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $baseName = preg_replace('/[^a-z0-9]+/i', '-', $baseName);  // Sécurise le nom
        $baseName = rtrim(strtolower($baseName), '-');
        
        // Génère un ID unique
        $uniqueId = ltrim(uniqid('', true), '-');
        
        // Ajoute le format final avec `-uniqueId.html.twig`
        return sprintf('%s-%s.html.twig', $baseName, $uniqueId);
    }
}
