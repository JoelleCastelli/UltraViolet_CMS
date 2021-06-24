<?php

if(isset($errors)) {
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
}?>

<div id="bannerToManual">
    <div>
        Vous ne trouvez pas votre bonheur sur TMDB ?
        Créez une fiche production vous-même !
    </div>

    <a href="<?= \App\Core\Helpers::callRoute('productions_creation') ?>">
        <button class="btn">Ajouter une nouvelle production manuellement</button>
    </a>
</div>


<div id="tmdbForm">
    <?php App\Core\FormBuilder::render($form); ?>
    <div id="production-preview" class="card">
        <p>Cliquez sur le bouton "Preview" pour afficher un aperçu de la production à ajouter.</p>
    </div>
</div>