<div class="content transparent customization-content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <form method="POST" action="<?= $url_form ?>">
        <article class="card custom-header rounded mb-1">
            <h3 class="font-normal mb-1">En-tête</h3>
            <div class="custom-content">
                <div class="form-field-inline w-5/6 mb-2">
                    <select id="main_header" name="main_header" class="form-control">
                        <?php foreach ($link_menus as $item) : ?>
                            <option value="<?= $item['id'] ?>" <?= $item['id'] == $header_menu ? 'selected=selected' : '' ?>><?= $item['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="main_header">Navigation principale</label>
                </div>
            </div>
        </article>
        <article class="card custom-footer rounded mb-1">
            <h3 class="font-normal mb-1">Pied de page</h3>
            <div class="custom-content" data-role="initFooterCustomization">
                <div class="form-field-inline w-5/6 mb-2">
                    <select id="socials_footer" name="socials_footer" class="form-control">
                        <option value=""></option>
                        <?php foreach ($link_socials as $item) : ?>
                            <option value="<?= $item['id'] ?>" <?= $item['id'] == $socials_menu ? 'selected=selected' : '' ?>><?= $item['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="socials_footer">Réseaux sociaux</label>
                </div>
                <div class="transferable-source w-10/12 m-auto mb-1 flex justify-around items-center flex-wrap">
                    <button class="transferable-element btn-secondary py-0.25" type="button"
                            data-options='<?= json_encode(['element' => \App\Core\Utils\Constants::LS_FOOTER_TEXT]) ?>'>
                        Ajouter texte
                    </button>
                    <button class="transferable-element btn-secondary py-0.25" type="button"
                            data-options='<?= json_encode(['element' => \App\Core\Utils\Constants::LS_FOOTER_LINKS, 'data' => $link_menus]) ?>'>
                        Ajouter liens
                    </button>
                    <button class="transferable-element btn-secondary py-0.25" type="button"
                            data-options='<?= json_encode(['element' => \App\Core\Utils\Constants::LS_FOOTER_CONTACT]) ?>'>
                        Ajouter contact
                    </button>
                    <button class="transferable-element btn-secondary py-0.25" type="button"
                            data-options='<?= json_encode(['element' => \App\Core\Utils\Constants::LS_FOOTER_NEWSLETTER]) ?>'>
                        Ajouter newsletter
                    </button>
                </div>
                <div id="transferable-target">
                    <ul class="list-elements">
                        <?php foreach ($footer_sections as $item) : ?>
                        <li class="transferable-element">
                            <div class="element-content">
                                <input type="hidden" name="footer_items[types][]" value="<?= $item['type'] ?>">
                                <?php if ($item['type'] == \App\Core\Utils\Constants::LS_FOOTER_TEXT) : ?>
                                    <input type="hidden" name="footer_items[menus][]" value="">
                                    <input class="label" type="text" name="footer_items[labels][]" placeholder="A propos" value="<?= $item['label'] ?>">
                                    <textarea class="form-control" name="footer_items[data][]" rows="4" style="resize: none" placeholder="Entrez votre texte..."><?= $item['data'] ?></textarea>
                                <?php elseif ($item['type'] == \App\Core\Utils\Constants::LS_FOOTER_LINKS) : ?>
                                    <input type="hidden" name="footer_items[data][]">
                                    <input class="label" type="text" name="footer_items[labels][]" value="<?= $item['label'] ?>" placeholder="Liens utiles">
                                    <select class="form-control" name="footer_items[menus][]">
                                        <?php foreach ($link_menus as $option) : ?>
                                            <option value="<?= $option['id'] ?>" <?= $option['id'] == $item['menu_id'] ? 'selected=selected' : '' ?>><?= $option['title'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif ($item['type'] == \App\Core\Utils\Constants::LS_FOOTER_CONTACT) : ?>
                                    <input type="hidden" name="footer_items[data][]" value="">
                                    <input type="hidden" name="footer_items[menus][]" value="">
                                    <input class="label" type="text" name="footer_items[labels][]" value="<?= $item['label'] ?>" placeholder="Contactez-nous">
                                    <span class="description">Formulaire rapide - Contact</span>
                                <?php elseif ($item['type'] == \App\Core\Utils\Constants::LS_FOOTER_NEWSLETTER) : ?>
                                    <input type="hidden" name="footer_items[data][]" value="">
                                    <input type="hidden" name="footer_items[menus][]" value="">
                                    <input class="label" type="text" name="footer_items[labels][]" value="<?= $item['label'] ?>" placeholder="Newsletter">
                                    <span class="description">Formulaire rapide - Newsletter</span>
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
            </div>
        </article>
        <article class="card custom-main rounded mb-1">
            <h3 class="font-normal mb-1">Contenu principale</h3>
            CONTENT
        </article>
        <div class="form-action">
            <input type="submit" class="btn-primary" data-role="submitPermissions" value="Enregistrer">
        </div>
    </form>
</div>