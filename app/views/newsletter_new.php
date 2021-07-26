<section id="page-content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <form class="form" method="POST" action="<?= $url_form ?>">
        <div class="container">
            <div class="row">
                <div class="col-12 flex justify-end">
                    <article>
                        <button class="btn-success" data-role="submitTextEditor">Créer</button>
                    </article>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <article class="card transparent p-0">
                        <input type="text" class="form-control w-full my-0.75" id="title" name="title" placeholder="Super news">
                        <textarea id="post-text-editor" name="post_content" data-role="initTinyMCE"></textarea>
                    </article>
                </div>
            </div>
        </div>
    </form>
</section>