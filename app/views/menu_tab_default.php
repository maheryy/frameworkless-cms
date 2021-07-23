<article class="tab-content">
    <div id="transferable" class="flex items-start" data-role="initMenuTransferable">
        <article class="card transparent w-4/12 px-1.5 pt-0 pb-1 mr-1.75">
            <?php if ($referer == -1) : ?>
                <div class="menu-config w-full">
                    <div class="form-field p-0 mb-1 w-full">
                        <select class="form-control w-full" id="menu_type" name="menu_type"
                                data-role="refreshMenuSource">
                            <?php foreach ($menu_types as $value => $label) : ?>
                                <option value="<?= $value ?>" <?= isset($menu_data['menu_type']) && $menu_data['menu_type'] == $value ? 'selected=selected' : '' ?>> <?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="menu_type">Type de menu</label>
                    </div>
                </div>
            <?php endif; ?>
            <div class="transferable-source source-links w-full <?= isset($menu_data['menu_type']) && $menu_data['menu_type'] != \App\Core\Utils\Constants::MENU_LINKS ? 'hidden' : '' ?>">
                <p class="text-lg font-bold py-0.5">Mes pages</p>
                <ul class="list-elements py-1">
                    <?php foreach ($pages as $page) : ?>
                        <li class="transferable-element">
                            <input type="hidden" class="element-data"
                                   data-options='<?= json_encode(['type' => 1, 'page_id' => $page['id'], 'page_link' => $page['slug'], 'page_title' => $page['title']]) ?>'>
                            <span class="label"><?= $page['title'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="text-lg font-bold py-0.5">Autres</p>
                <ul class="list-elements py-1">
                    <li class="transferable-element">
                        <input type="hidden" class="element-data" data-options='<?= json_encode(['type' => 2]) ?>'>
                        <span class="label">Lien personnalisé</span>
                    </li>
                    <li class="transferable-element">
                        <input type="hidden" class="element-data" data-options='<?= json_encode(['type' => 2, 'page_title' => 'Nos avis', 'page_link' => '/reviews', 'link_readonly' => true]) ?>'>
                        <span class="label">Page avis</span>
                    </li>
                    <li class="transferable-element">
                        <input type="hidden" class="element-data" data-options='<?= json_encode(['type' => 2, 'page_title' => 'Votre avis', 'page_link' => '/review', 'link_readonly' => true]) ?>'>
                        <span class="label">Formulaire avis</span>
                    </li>
                </ul>
            </div>
            <div class="transferable-source source-socials w-full <?= isset($menu_data['menu_type']) && $menu_data['menu_type'] == \App\Core\Utils\Constants::MENU_SOCIALS ? '' : 'hidden' ?>">
                <p class="text-lg font-bold py-0.5">Réseaux sociaux</p>
                <ul class="list-elements py-1">
                    <?php foreach ($social_medias as $item) : ?>
                        <li class="transferable-element">
                            <input type="hidden" class="element-data"
                                   data-options='<?= json_encode(['type' => 2, 'icon' => $item['icon'], 'page_title' => $item['label']]) ?>'>
                            <span class="label"><i class="<?= $item['icon'] ?> p-0.25"></i><?= $item['label'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </article>
        <article class="card w-8/12 px-1.5 py-1">
            <form class="w-full flex-col" method="POST" action="<?= $url_form ?>">
                <p class="">
                    <input type="text" id="menu_name"
                           class="text-xl font-extralight py-0.5 px-0.25 w-full border-none border-bottom-default"
                           name="menu_name" placeholder="Nom du menu"
                           value="<?= $menu_data['menu_title'] ?? 'Nouveau menu' ?>">
                </p>
                <div id="transferable-target" class="w-full">
                    <ul class="list-elements py-1">
                        <?php foreach ($menu_items as $item) : ?>
                            <li class="transferable-element">
                                <div class="element-content">
                                    <?php if ($item['menu_type'] == \App\Core\Utils\Constants::MENU_LINKS) : ?>
                                        <input type="hidden" name="menu_items[icons][]" value="">
                                        <input type="hidden" name="menu_items[pages][]"
                                               value="<?= $item['page_id'] ?? '' ?>">
                                        <input class="label" type="text" name="menu_items[labels][]"
                                               value="<?= $item['label'] ?>" placeholder="Nom du lien">
                                        <span class="description">
                                            <label><?= !empty($item['page_id']) ? 'Page ' . $item['page_title'] . ' :' : 'Lien personnalisé :' ?></label>
                                            <input type="text" class="link" name="menu_items[links][]"
                                                   value="<?= !empty($item['page_id']) ? $item['page_link'] : $item['url'] ?>"
                                                   readonly>
                                        </span>
                                    <?php else : ?>
                                        <input type="hidden" name="menu_items[icons][]" value="<?= $item['icon'] ?>">
                                        <input type="hidden" name="menu_items[pages][]" value="">
                                        <input class="label" type="text" name="menu_items[labels][]"
                                               value="<?= $item['label'] ?>" placeholder="Nom du lien" readonly>
                                        <span class="description">
                                            <i class="<?= $item['icon'] ?> px-0.25"></i>
                                            <input type="text" class="link" name="menu_items[links][]"
                                                   placeholder="www.example.com" value=<?= $item['url'] ?>>
                                        </span>
                                    <?php endif ?>
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
                    <input type="submit" class="<?= isset($menu_data) ? 'btn-primary' : 'btn-success' ?> text-base <?= !$can_update ? 'hidden' : '' ?>"
                           value="<?= isset($menu_data) ? 'Sauvegarder' : 'Ajouter' ?>" data-role="submitPermissions"
                           data-options=<?= json_encode(['add_data' => ['ref' => $referer]]) ?>>
                    <?php if ($can_delete && $url_delete) : ?>
                        <button class="btn-danger text-base" data-url="<?= $url_delete ?>" data-role="deleteItem">
                            Supprimer
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </article>
    </div>
</article>