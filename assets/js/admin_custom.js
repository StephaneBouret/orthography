document.addEventListener('DOMContentLoaded', function () {
    const contentTypeSelect = document.querySelector('select[name="Courses[contentType]"]');
    const audioFields = document.querySelectorAll('.field-audioFile, .field-audioFileName'); 
    const dictationFields = document.querySelectorAll('.field-correctionText');

    // Fonction pour afficher ou masquer les champs
    function toggleAudioFields() {
        if (contentTypeSelect.value === 'Audio') {
            audioFields.forEach(field => field.style.display = 'block');
            dictationFields.forEach(field => field.style.display = 'block');
        } else {
            audioFields.forEach(field => field.style.display = 'none');
            dictationFields.forEach(field => field.style.display = 'none');
        }
    }

    // Affiche les champs au chargement de la page
    toggleAudioFields();

    // Ajoute un écouteur pour détecter les changements de sélection
    contentTypeSelect.addEventListener('change', toggleAudioFields);
});
