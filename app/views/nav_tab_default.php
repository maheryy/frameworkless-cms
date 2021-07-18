<article class="tab-content">
    <div id="transferable" class="flex items-start" data-role="initNavigationTransferable">
        <article class="card w-4/12 px-1.5 py-1 mr-1.75">
            <p class="text-lg font-bold pb-1 py-0.5">Toutes les pages</p>
            <div id="transferable-source" class="w-full">
                <ul class="list-elements py-1">
                    <?php foreach ($pages as $page) : ?>
                        <li class="transferable-element">
                            <input type="hidden" class="element-data"
                                   data-options='<?= json_encode(['page_id' => $page['id'], 'page_link' => $page['slug'], 'page_title' => $page['title']]) ?>'>
                            <span class="label"><?= $page['title'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </article>
        <article class="card w-8/12 px-1.5 py-1">
            <form class="w-full flex-col" method="POST" action="<?= $url_form ?>">
                <p class="">
                    <input type="text" id="nav-name"
                           class="text-xl font-extralight py-0.5 px-0.25 w-full border-none border-bottom-default"
                           name="nav_name" placeholder="Nom de la navigation"
                           value="<?= isset($nav_data['nav_title']) ? $nav_data['nav_title'] : 'Nouvelle navigation' ?>">
                </p>
                <div class="nav-config w-full py-1 px-0.5 flex justify-around">
                    <div class="form-field py-0.5 w-2/6">
                        <select class="form-control w-full" id="nav_type" name="nav_type">
                            <?php foreach ($nav_types as $value => $label) : ?>
                                <option value="<?= $value ?>" <?= isset($nav_data['nav_type']) && $nav_data['nav_type'] == $value ? 'selected=selected' : '' ?>> <?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="nav_type">Type de navigation</label>
                    </div>
                    <div class="form-field-inline py-0.5 w-1/6">
                        <input type="checkbox" name="nav_active" id="nav_active"
                               value="1" <?= isset($nav_data['nav_active']) && $nav_data['nav_active'] == \App\Core\Utils\Constants::STATUS_ACTIVE ? 'checked=checked' : '' ?>>
                        <label for="nav_active">Active</label>
                    </div>
                </div>
                <hr class="w-4/12 self-center mt-1 mb-0.25 border-bottom-default divider">
                <div id="transferable-target" class="w-full">
                    <ul class="list-elements py-1">
                        <?php foreach ($nav_items as $nav_item) : ?>
                            <li class="transferable-element">
                                <div class="element-content">
                                    <input type="hidden" name="nav_items[]" value="<?= $nav_item['page_id'] ?>">
                                    <input type="text" name="nav_labels[]" value="<?= $nav_item['label'] ?>">
                                    <span class="description">Page <?= $nav_item['page_title'] ?> |
                                        <a target="_blank"
                                           href="/<?= $nav_item['page_link'] ?>"><?= '/' . $nav_item['page_link'] ?></a>
                                    </span>
                                </div>
                                <div class="element-actions">
                                    <span class="element-up"><i class="fas fa-sort-up"></i></span>
                                    <span class="element-down"><i class="fas fa-sort-down"></i></span>
                                    <span class="element-delete"><i class="fas fa-times"></i></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="form-action px-0 right">
                    <input type="submit" class="<?= $referer == -1 ? 'btn-success' : 'btn-primary' ?> text-base"
                           value="<?= $referer == -1 ? 'Ajouter' : 'Sauvegarder' ?>" data-role="submitPermissions"
                           data-options=<?= json_encode(['add_data' => ['ref' => $referer]]) ?>>
                    <?php if ($url_delete) : ?>
                        <button class="btn-danger text-base" data-url="<?= $url_delete ?>" data-role="deleteItem">Supprimer</button>
                    <?php endif; ?>
                </div>
            </form>
        </article>
    </div>
</article>