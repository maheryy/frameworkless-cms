<section class="">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <form class="form" method="POST" action="<?= $url_form ?>">
        <textarea id="post-text-editor" name="post_content" data-role="initTinyMCE"></textarea>
        <div class="form-action">
            <input type="submit" class="btn-success" data-role="submitTextEditor" value="Enregistrer">
        </div>
    </form>
</section>