<main id="installer-content">
    <section class="installer-container">
        <h3 class="text-center">Bienvenue sur Munkee</h3>
        <article id="info-box" class="info-primary center">
            <span id="info-description"></span>
        </article>
        <p class="info-text">Veuillez remplir ci-dessous les détails de connexion à votre base de données.</p>
        <form class="form-default" method="POST" action="<?= $url_form_action ?>">
            <div class="form-field-inline required">
                <label for="db_name">Nom de la base de données</label>
                <input type="text" class="form-control" id="db_name" name="db_name" placeholder="Super_BDD" value="<?= $config['db_name'] ?? ''?>">
    <!--            <span>Le nom de la base de données avec laquelle vous souhaitez utilisez Go Travel</span>-->
            </div>
            <div class="form-field-inline required">
                <label for="db_user">Utilisateur</label>
                <input type="text" class="form-control" id="db_user" name="db_user" placeholder="root" value=<?= $config['db_user'] ?? ''?>>
    <!--            <span>Nom d'utilisateur MySQL</span>-->
            </div>
            <div class="form-field-inline required">
                <label for="db_password">Mot de passe de l'utilisateur</label>
                <input type="password" class="form-control" id="db_password" name="db_password" placeholder="root" value=<?= $config['db_password'] ?? ''?>>
    <!--            <span>Votre mot de passe de base de données</span>-->
            </div>
            <div class="form-field-inline required">
                <label for="db_host">Nom d'hôte</label>
                <input type="text" class="form-control" id="db_host" name="db_host" placeholder="localhost" value=<?= $config['db_host'] ?? ''?>>
    <!--            <span>Si localhost ne fonctionne pas, demandez cette information à l'hébergeur de votre site</span>-->
            </div>
            <div class="form-field-inline required">
                <label for="db_prefix">Préfix des tables</label>
                <input type="text" class="form-control" id="db_prefix" name="db_prefix" placeholder="ex: abc" value=<?= $config['db_prefix'] ?? ''?>>
    <!--            <span>Préfix des tables</span>-->
            </div>
            <div class="form-action">
                <button class="btn-primary" data-role="submitDefault" data-options='<?= json_encode($opts_try_connection) ?>'>Tester la connexion</button>
                <input type="submit" class="btn-success" value="Installer" data-role="submitDefault">
    <!--            <a href="/installer-register" class="btn-success">Installer Go Travel</a>-->
            </div>
        </form>
    </section>
</main>