<div class="content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <article id="tabs-container" data-role="initClassicalTabs">
        <ul id="tab-list" class="w-full">
            <li class="tab-item">
                <button class="tab-btn <?= $active_tab === 1 ? 'active' : '' ?>" data-id="1">Général</button>
            </li>
            <li class="tab-item">
                <button class="tab-btn <?= $active_tab === 2 ? 'active' : '' ?>" data-id="2">Messagerie</button>
            </li>
<!--            <li class="tab-item">-->
<!--                <button class="tab-btn --><?//= $active_tab === 3 ? 'active' : '' ?><!--" data-id="3">Page</button>-->
<!--            </li>-->
        </ul>
        <div id="content-container" class="w-full">
            <article id="content-1" class="tab-content <?= $active_tab === 1 ? 'active' : 'hidden' ?>">
                <form id="form-general" class="form-default" method="POST" action="<?= $url_form_general ?>">
                    <div class="form-field required">
                        <input type="text" class="form-control" id="site_title" name="site_title" value="<?= $settings['site_title'] ?>">
                        <label for="site_title">Titre du site</label>
                    </div>
                    <div class="form-field">
                        <textarea class="form-control" id="site_description" name="site_description"><?= $settings['site_description'] ?></textarea>
                        <label for="site_description">Description du site</label>
                    </div>
                    <div class="form-field required">
                        <input type="email" class="form-control" id="email_admin" name="email_admin" value="<?= $settings['email_admin'] ?>">
                        <label for="email_admin">Adresse e-mail d'administration</label>
                    </div>
                    <div class="form-field">
                        <input type="email" class="form-control" id="email_contact" name="email_contact" value="<?= $settings['email_contact'] ?>">
                        <label for="email_contact">Adresse e-mail de contact</label>
                    </div>
                    <div class="form-field">
                        <select class="form-control" id="default_role" name="default_role">
                            <?php foreach ($roles as $role) : ?>
                                <option <?= $settings['default_role'] == $role['id'] ? 'selected=selected' : ''?> value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="default_role">Rôle par défaut</label>
                    </div>
                    <div class="form-field-inline">
                        <input type="checkbox" class="form-control-check" id="public_signup" name="public_signup" value="<?= $settings['public_signup'] ?>">
                        <label for="public_signup">Inscription public</label>
                    </div>
                    <div class="form-action">
                        <input type="submit" class="btn-primary text-base" value="Enregistrer" data-role="submitDefault">
                    </div>
                </form>
            </article>
            <article id="content-2" class="tab-content <?= $active_tab === 2 ? 'active' : 'hidden' ?>">
                <form id="form-mail" class="form-default" method="POST" action="<?= $url_form_mail ?>">
                    <div class="form-field required">
                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" placeholder="mail.example.com" value="<?= $smtp['host'] ?>">
                        <label for="smtp_host">Serveur de messagerie</label>
                    </div>
                    <div class="form-field required">
                        <input type="number" class="form-control" id="smtp_port" name="smtp_port" placeholder="ex: 587" value="<?= $smtp['port'] ?>">
                        <label for="smtp_port">Port</label>
                    </div>
                    <div class="form-field required">
                        <input type="text" class="form-control" id="smtp_user" name="smtp_user" placeholder="login@example.com" value="<?= $smtp['user'] ?>">
                        <label for="smtp_user">Identifiant</label>
                    </div>
                    <div class="form-field required">
                        <input type="password" class="form-control" id="smtp_password" name="smtp_password" placeholder="password">
                        <label for="smtp_password">Mot de passe</label>
                    </div>
                    <div class="form-action required">
                        <button class="btn-success text-base" data-role="submitDefault" data-options='<?= json_encode(['add_data' => ['try_connection' => 1]])?>'>Tester</button>
                        <input type="submit" class="btn-primary text-base" value="Enregistrer" data-role="submitDefault">
                    </div>
                </form>
            </article>
<!--            <article id="content-3" class="tab-content --><?//= $active_tab === 3 ? 'active' : 'hidden' ?><!--">-->
<!--                <form id="form-page" class="form-default" method="POST" action="--><?//= $url_form_mail ?><!--">-->
<!--                    <div class="form-field">-->
<!--                        <input type="text" class="form-control" id="page_indexable" name="page_indexable">-->
<!--                        <label for="page_indexable">Serveur de messagerie</label>-->
<!--                    </div>-->
<!--                    <div class="form-action">-->
<!--                        <input type="submit" class="btn-primary" value="Enregistrer" data-role="submitDefault">-->
<!--                    </div>-->
<!--                </form>-->
<!--            </article>-->
        </div>
    </article>
</div>