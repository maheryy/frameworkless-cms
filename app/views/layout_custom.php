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
                <div class="form-field w-5/6 mb-2">
                    <label style="order: 0; margin-bottom: 0.75rem">En-tête secondaire</label>
                    <div class="w-3/5 flex justify-between">
                        <span>
                            <input type="radio" class="" id="hero_status1" name="hero_status" value="0" <?= !empty($hero_data) && $hero_data['status'] == 0 ? 'checked' : '' ?>>
                            <label for="hero_status1">Désactivé</label>
                        </span>
                        <span>
                            <input type="radio" class="" id="hero_status2" name="hero_status" value="1" <?= !empty($hero_data) && $hero_data['status'] == 1 ? 'checked' : '' ?>>
                            <label for="hero_status2">Page principale</label>
                        </span>
                        <span>
                            <input type="radio" class="" id="hero_status3" name="hero_status" value="2" <?= !empty($hero_data) && $hero_data['status'] == 2 ? 'checked' : '' ?>>
                            <label for="hero_status3">Toutes les pages</label>
                        </span>
                    </div>
                </div>
                <div class="form-field-inline w-5/6 mb-2">
                    <input class="form-control" id="hero_title" name="hero_title" value="<?= $hero_data['title'] ?? '' ?>" placeholder="Mon super site">
                    <label for="hero_title">Titre en-tête secondaire</label>
                </div>
                <div class="form-field-inline w-5/6 mb-2">
                    <textarea class="form-control" id="hero_description" name="hero_description" placeholder="Entrez une description"><?= $hero_data['description'] ?? '' ?></textarea>
                    <label for="hero_description">Description en-tête secondaire</label>
                </div>
                <div class="form-field-inline w-5/6 mb-2">
                    <input type="url" class="form-control" id="hero_image" name="hero_image" value="<?= $hero_data['image'] ?? '' ?>" placeholder="Lien de votre image">
                    <label for="hero_image">Lien image en-tête secondaire</label>
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
        <div class="form-action">
            <button class="btn-primary <?= !$can_update ? 'hidden' : '' ?>" data-role="submitPermissions">Enregistrer</button>
        </div>
    </form>
</div>