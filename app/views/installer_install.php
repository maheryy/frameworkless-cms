<main class="flex items-center" style="width: 100%; height: 100vh">
    <section class="w-6/12 m-auto card rounded" style=" background-color: #fbfbfb">
        <h3 class="font-light text-2xl text-center">Une dernière chose...</h3>
        <article id="info-box" class="info-primary center">
            <span id="info-description"></span>
        </article>
        <form class="m-auto flex flex-col px-4 py-2 w-11/12" method="POST" action="<?= $url_form ?>">
            <div class="form-field required">
                <input type="text" class="form-control" id="website_title" name="website_title">
                <label for="website_title">Titre du site</label>
            </div>
            <div class="form-field required">
                <input type="text" class="form-control" id="username" name="username">
                <label for="username">Nom d'utilisateur</label>
            </div>
            <div class="form-field required">
                <input type="password" class="form-control" id="password" name="password">
                <label for="password">Mot de passe</label>
            </div>
            <div class="form-field required">
                <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                <label for="password">Confirmez le mot de passe</label>
            </div>
            <div class="form-field required">
                <input type="email" class="form-control" id="email" name="email">
                <label for="email">Adresse de messagerie</label>
            </div>
            <div class="form-field">
                <input type="email" class="form-control" id="email_contact" name="email_contact">
                <label for="email_contact">Adresse de contact</label>
            </div>
            <div class="form-action right pt-2">
                <button class="btn-success text-base" data-role="submitDefault">Installer et terminer</button>
            </div>
        </form>
    </section>
</main>