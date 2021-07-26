<section id="page-content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <form class="form" method="POST" action="<?= $url_form ?>">
        <div class="container">
            <div class="row">
                <div class="col-12 flex justify-end">
                    <article>
                        <button class="btn-primary <?= !$can_update ? 'hidden' : '' ?>" data-role="submitTextEditor">Enregistrer</button>
                        <button class="btn-danger <?= !$can_delete ? 'hidden' : '' ?>" data-url="<?= $url_delete ?>" data-role="actionItem">Supprimer</button>
                    </article>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <article class="card transparent p-0">
                        <input type="text" class="form-control w-full my-0.75" id="title" name="title" placeholder="Super titre" value="<?= $newsletter['title'] ?? ''?>">
                        <textarea id="post-text-editor" name="post_content" data-role="initTinyMCE"><?= $newsletter['content'] ?></textarea>
                    </article>
                </div>
            </div>
        </div>
    </form>
</section>