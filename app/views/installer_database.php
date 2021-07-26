<main class="flex items-center" style="width: 100%; height: 100vh">
    <section class="w-6/12 m-auto card rounded" style=" background-color: #fbfbfb">
        <h3 class="font-light text-2xl text-center">Bienvenue sur Munkee</h3>
        <article id="info-box" class="info-primary center">
            <span id="info-description"></span>
        </article>
        <form class="m-auto flex flex-col px-4 py-2 w-11/12" method="POST" action="<?= $url_form ?>">
            <div class="form-field required">
                <label for="db_name">Nom de la base de données</label>
                <input type="text" class="form-control" id="db_name" name="db_name" placeholder="Super_BDD" value="<?= $config['db_name'] ?? ''?>">
            </div>
            <div class="form-field required">
                <label for="db_user">Utilisateur</label>
                <input type="text" class="form-control" id="db_user" name="db_user" placeholder="root" value=<?= $config['db_user'] ?? ''?>>
            </div>
            <div class="form-field required">
                <label for="db_password">Mot de passe de l'utilisateur</label>
                <input type="password" class="form-control" id="db_password" name="db_password" placeholder="root" value=<?= $config['db_password'] ?? ''?>>
            </div>
            <div class="form-field required">
                <label for="db_host">Nom d'hôte</label>
                <input type="text" class="form-control" id="db_host" name="db_host" placeholder="localhost" value=<?= $config['db_host'] ?? ''?>>
            </div>
            <div class="form-field required">
                <label for="db_prefix">Préfix des tables</label>
                <input type="text" class="form-control" id="db_prefix" name="db_prefix" placeholder="ex: abc" value=<?= $config['db_prefix'] ?? ''?>>
            </div>
            <div class="form-field required mt-2">
                <input type="text" class="form-control" id="smtp_host" name="smtp_host" placeholder="mail.example.com" value="<?= $config['smtp_host'] ?? ''?>">
                <label for="smtp_host">Serveur de messagerie</label>
            </div>
            <div class="form-field required">
                <input type="text" class="form-control" id="smtp_user" name="smtp_user" placeholder="login@example.com" value="<?= $config['smtp_user'] ?? ''?>">
                <label for="smtp_user">Identifiant de messagerie</label>
            </div>
            <div class="form-field required">
                <input type="password" class="form-control" id="smtp_password" name="smtp_password" placeholder="password" value="<?= $config['smtp_password'] ?? ''?>">
                <label for="smtp_password">Mot de passe de messagerie</label>
            </div>
            <div class="form-action right pt-2">
                <button class="btn-primary text-base" data-role="submitDefault" data-options='<?= json_encode($opts_try_connection) ?>'>Tester la connexion</button>
                <button class="btn-success text-base" data-role="submitDefault">Passer à l'étape suivante</button>
            </div>
        </form>
    </section>
</main>