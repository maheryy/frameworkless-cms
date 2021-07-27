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
                        <?php if ($page['status'] != \App\Core\Utils\Constants::STATUS_PUBLISHED && $can_publish) :?>
                            <button class="btn-success" data-role="submitTextEditor" data-options=<?= json_encode(['add_data' => ['action_publish' => 1]]) ?>>Publier</button>
                        <?php endif;?>
                        <button class="btn-danger <?= !$can_delete ? 'hidden' : '' ?>" data-url="<?= $url_delete ?>" data-role="actionItem" data-options='<?= json_encode(['confirm' => true]) ?>'>Supprimer</button>
                    </article>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <article class="card transparent p-0">
                        <input type="text" class="form-control w-full my-0.75" id="title" name="title" placeholder="Super titre" value="<?= $page['title'] ?? ''?>">
                        <textarea id="post-text-editor" name="post_content" data-role="initTinyMCE"><?= $page['content'] ?></textarea>
                    </article>
                </div>
            </div>
            <div class="row align-start">
                <div class="col-6 flex-col justify-evenly">
                    <h3 class="py-0.5">Paramètres de la page</h3>
                    <article class="page-option card rounded">
                        <div class="form-field">
                            <select class="form-control" name="author" id="author">
                                <?php foreach ($users as $user) :?>
                                    <option value="<?= $user['id'] ?>" <?= $user['id'] == $page['author_id'] ? 'selected=selected' : ''?>>
                                        <?= $user['username'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="author">Auteur</label>
                        </div>
                        <div class="form-field">
                            <input type="text" class="form-control" name="slug" id="slug" value="<?= $page['slug'] ?? ''?>">
                            <label for="slug">Slug</label>
                        </div>
                        <?php if ($page['status'] == \App\Core\Utils\Constants::STATUS_PUBLISHED) :?>
                            <div class="form-field">
                                <input type="date" class="form-control" name="published_at" id="published_at" value="<?= $page['published_at'] ?? ''?>">
                                <label for="published_at">Publié le</label>
                            </div>
                        <?php endif; ?>
                    </article>
                </div>
                <div class="col-6">
                    <h3 class="py-0.5">Paramètres de référencement</h3>
                    <article class="card rounded">
                        <div class="form-field">
                            <input type="text" class="form-control" name="meta_title" id="meta_title" value="<?= $page['meta_title'] ?? ''?>">
                            <label for="meta_title">Titre</label>
                        </div>
                        <div class="form-field">
                            <textarea class="form-control" name="meta_description" id="meta_description"><?= $page['meta_description'] ?? ''?></textarea>
                            <label for="meta_description">Description</label>
                        </div>
                        <div class="form-field-inline">
                            <input type="checkbox" class="form-control-check" name="display_search_engine" id="display_search_engine"
                                <?= $page['meta_indexable'] ? 'checked=checked' : ''?>>
                            <label for="display_search_engine">Afficher dans les résultats de recherches</label>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </form>
</section>