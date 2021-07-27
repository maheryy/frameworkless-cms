<main id="register-content">
    <section class="register-container">
        <h2 class="text-center">Inscription</h2>
        <article class="login-box card">
            <article id="info-box" class="info-primary">
                <span id="info-description"></span>
            </article>
            <form class="form-default" method="POST" action="<?= $url_form ?>">
                <div class="form-field required">
                    <input class="form-control" type="text" id="username" name="username" required>
                    <label for="username">Nom d'utilisateur</label>
                </div>
                <div class="form-field required">
                    <input class="form-control" type="email" id="email" name="email" required>
                    <label for="email">Adresse email</label>
                </div>
                <div class="form-field required">
                    <input class="form-control" type="password" id="password" name="password" required>
                    <label for="password">Mot de passe</label>
                </div>
                <div class="form-field required">
                    <input class="form-control" type="password" id="password_confirm" name="password_confirm" required>
                    <label for="password_confirm">Confirmez le mot de passe</label>
                </div>
                <div class="form-action center">
                    <button class="submit-btn btn-primary" data-role="submitDefault">S'inscrire</button>
                </div>
            </form>
        </article>
    </section>
</main>