<?php use App\Core\Helpers; ?>

<div class="grid-article">

    <div class="cover">
        <img alt="imge de couverture de l'article" src="<?= $article->getMedia()->getPath() ?>"></img>
    </div>

    <section class="article card">
        <h1 class="article__title"><?= $article->getTitle() ?></h1>
        <small class="article__author">Ecrit par <?= $article->getPerson()->getPseudo() ?> le <?= $article->getCleanPublicationDate() ?></small>
        <div class="article__tags">
            <?php foreach($article->getCategories() as $category) : ?>
                <div class="article__tags__category tag-item"><?= $category->getName() ?></div>
            <?php endforeach; ?>
        </div>
        <article>
            <?= $article->getContent() ?>
        </article>
    </section>
    

    <section id="production-card" class="production card">
        <img class="production__image" src="<?= $production->getProductionPosterPath() ?>"></img>
        <h2 class="production__title"><?= $production->getTitle() ?></h2>
        <p class="production__type tag-item"><?= $production->getType() ?></p>
        <p class="production__release-date">Date de sortie : <?= $production->getReleaseDate() ?></p>
        <p></p>
    </section>


    <section class="comments card">

        <h2 class="title-section">Section commentaire</h2>

        <div id="add-btn" class="title-btn">
            <button class="btn title-btn">Commenter
                <div class="add-btn"></div>
            </button>
        </div>
        

        <div id="test-comment" class="test-comment">
            <?php if (isset($form)) App\Core\FormBuilder::render($form); ?>
        </div>

        <?php foreach($comments as $comment) : ?>
        <div class="comment">
            <img class="comment__profile-picture" src="<?=PATH_TO_IMG?>default_user.jpg"></img>
            <h3 class="comment__title">Ecrit par <?= $comment->getPerson()->getPseudo() ?> le <?= $comment->getCleanCreationDate() ?></h3>
            <p class="comment__content"><?= $comment->getContent() ?></p>
        </div>
        <?php endforeach; ?>


    </section>

</div>

<div id="details-modal" class="background-modal-production">
    <div class="clickable-bg"></div>
    <div class="modal-production-details prod">

        <img class="prod__cover" src="<?= $production->getProductionPosterPath() ?>"></img>
        <article class="prod__details">
            <h1 class="prod__details_title"><?= $production->getTitle()?></h1>
            <p class="prod__details_type tag-item"><?= $production->getType() ?></p>
            <p class="prod__details_date"><?= $production->getReleaseDate() ?></p>
            <small class="prod__details_resume"><?= $production->getOverview() ?></small>
        </article>

        <article class="prod__actors">
            <?php if(!empty($actors)) echo '<h1 class="prod-modal-title" >Casting</h1>'; ?>
            <?php foreach ($actors as $actor) { ?>
                <div class="person-card" >
                    <img class="little-thumbnail" src="<?= $actor["photo"] ?>" alt="portrait de <?= $actor["fullName"] ?>">
                    <small><?= $actor["fullName"] ?></small>
                    <small>alias : <?= $actor["role"] ?></small>
                </div>
            <?php } ?>
        </article>

        <article class="prod__directors">
            <?php if(!empty($directors)) echo '<h1 class="prod-modal-title" >Réalisation</h1>'; ?>
            <?php foreach ($directors as $director) { ?>
                <div class="person-card">
                    <img class="little-thumbnail" src="<?= $director["photo"] ?>" alt="portrait de <?= $director["fullName"] ?>">
                    <small><?= $director["fullName"] ?></small>
                </div>
            <?php } ?>
        </article>

        <article class="prod__writers">
            <?php if(!empty($writers)) echo '<h1 class="prod-modal-title" >Scénario</h1>'; ?>
            <?php foreach ($writers as $writer) { ?>
                <div class="person-card">
                    <img class="little-thumbnail" src="<?= $writer["photo"] ?>" alt="">
                    <small><?= $writer["fullName"] ?></small>
                </div>
            <?php } ?>
        </article>

        <article class="prod__creators">
            <?php if(!empty($creators)) echo '<h1 class="prod-modal-title" >Realisation</h1>'; ?> 
            <?php foreach ($creators as $creator) { ?>
                <div class="person-card">
                    <img class="little-thumbnail" src="<?= $creator["photo"] ?>" alt="">
                    <small><?= $creator["fullName"] ?></small>
                </div>
            <?php } ?>
        </article>
        
    </div>
</div>