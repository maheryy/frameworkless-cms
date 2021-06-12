<section class="">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <form class="form" method="POST" action="<?= $url_form ?>">
        <input type="hidden" name="page_id" value="<?= $page['id'] ?>">
        <textarea id="post-text-editor" name="post_content" data-role="initTinyMCE"><?= $page['content'] ?></textarea>
        <div class="form-action">
            <input type="submit" class="btn-success" data-role="submitTextEditor" value="Enregistrer">
        </div>
    </form>
</section>
<button class="btn-danger" data-url="<?= $url_delete ?>" data-role="deleteItem">Supprimer</button>
