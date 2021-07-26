<section id="page-content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <form class="form" method="POST" action="<?= $url_form ?>">
        <div class="container">
            <div class="row">
                <div class="col-12 flex justify-end">
                    <article>
                        <button class="btn-primary" data-role="submitTextEditor">Enregistrer</button>
                        <button class="btn-success" data-role="submitTextEditor" data-options=<?= json_encode(['add_data' => ['action_publish' => 1]]) ?>>Publier</button>
                    </article>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <article class="card transparent p-0">
                        <input type="text" class="form-control w-full my-0.75" id="title" name="title" placeholder="Super titre">
                        <textarea id="post-text-editor" name="post_content" data-role="initTinyMCE"></textarea>
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
                                    <option value="<?= $user['id'] ?>" <?= $current_user_id == $user['id']  ? 'selected=selected' : ''?>>
                                        <?= $user['username'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="author">Auteur</label>
                        </div>
                        <div class="form-field">
                            <select class="form-control" name="visibility" id="visibility">
                                <?php foreach ($visibility_types as $key => $value) :?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="visibility">Visibilité</label>
                        </div>
                    </article>
                </div>
                <div class="col-6">
                        <h3 class="py-0.5">Paramètres de référencement</h3>
                    <article class="card rounded">
                        <div class="form-field">
                            <input type="text" class="form-control" name="meta_title" id="meta_title">
                            <label for="meta_title">Titre</label>
                        </div>
                        <div class="form-field">
                            <textarea class="form-control" name="meta_description" id="meta_description"></textarea>
                            <label for="meta_description">Description</label>
                        </div>
                        <div class="form-field">
                            <input type="text" class="form-control" name="slug" id="slug">
                            <label for="slug">Slug</label>
                        </div>
                        <div class="form-field-inline">
                            <input type="checkbox" class="form-control-check" name="display_search_engine" id="display_search_engine">
                            <label for="display_search_engine">Afficher dans les résultats de recherches</label>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </form>
</section>