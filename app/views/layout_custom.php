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
                            <option value="<?= $item['id'] ?>"><?= $item['title'] ?></option>
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
                            <option value="<?= $item['id'] ?>"><?= $item['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="socials_footer">Réseaux sociaux</label>
                </div>
                <div class="transferable-source test w-10/12 m-auto mb-1 flex justify-around items-center flex-wrap">
                    <button class="transferable-element btn-secondary py-0.25" type="button"
                            data-options='<?= json_encode(['element' => \App\Core\Utils\Constants::FOOTER_TEXT]) ?>'>
                        Ajouter texte
                    </button>
                    <button class="transferable-element btn-secondary py-0.25" type="button"
                            data-options='<?= json_encode(['element' => \App\Core\Utils\Constants::FOOTER_LINKS, 'data' => $link_menus]) ?>'>
                        Ajouter liens
                    </button>
                    <button class="transferable-element btn-secondary py-0.25" type="button"
                            data-options='<?= json_encode(['element' => \App\Core\Utils\Constants::FOOTER_CONTACT]) ?>'>
                        Ajouter contact
                    </button>
                    <button class="transferable-element btn-secondary py-0.25" type="button"
                            data-options='<?= json_encode(['element' => \App\Core\Utils\Constants::FOOTER_NEWSLETTER]) ?>'>
                        Ajouter newsletter
                    </button>
                </div>
                <div id="transferable-target">
                    <ul class="list-elements">
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