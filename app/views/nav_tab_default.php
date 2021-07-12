<article class="tab-content">
    <div id="transposable" class="flex items-start" data-role="initTransposable">
        <article class="card w-4/12 px-1.5 py-1 mr-1.75">
            <p class="text-lg font-bold pb-1 py-0.5">Toutes les pages</p>
            <div id="transpose-source" class="w-full">
                <ul class="list-elements py-1">
                    <?php foreach ($pages as $page) : ?>
                        <li class="transpose-element">
                            <span><?= $page['title'] ?></span>
                            <input type="hidden" name="nav_items[]" value="<?= $page['id'] ?>">
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </article>
        <article class="card w-8/12 px-1.5 py-1">
            <form class="w-full flex-col" method="POST" action="<?= $url_form ?>">
                <p class="text-lg font-bold pb-1">
                    <input type="text" class="text-xl font-extralight py-0.5 w-full border-none border-bottom-default"
                           name="nav_name" placeholder="Nom de la navigation" value="<?= $nav_name ?>">
                </p>
                <div id="transpose-target" class="w-full">
                    <ul class="list-elements py-1">
                        <?php foreach ($navigation_items as $nav_item) : ?>
                            <li class="transpose-element">
                                <span><?= $nav_item['label'] ?></span>
                                <input type="hidden" name="nav_items[]" value="<?= $nav_item['id'] ?>">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="form-action px-0 right">
                    <input type="submit" class="<?= $referer == -1 ? 'btn-success' : 'btn-primary' ?> text-base"
                           value="<?= $referer == -1 ? 'Ajouter' : 'Sauvegarder' ?>" data-role="submitPermissions"
                           data-options=<?= json_encode(['add_data' => ['ref' => $referer]]) ?>>
                </div>
            </form>
        </article>
    </div>
</article>