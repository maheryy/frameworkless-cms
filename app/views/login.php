<main id="login-content">
    <section class="login-container">
        <h2 class="text-center">Connexion</h2>
        <article class="login-box card">
            <article id="info-box" class="<?= !empty($active_error) ? 'info-danger active' : 'info-primary' ?>">
                <span id="info-description"> <?= $active_error ?? '' ?></span>
            </article>
            <form class="form-default" method="POST" action="<?= $url_form ?>">
                <div class="form-field required">
                    <input class="form-control" type="text" id="login" name="login" required>
                    <label for="login">Nom d'utilisateur ou email</label>
                </div>
                <div class="form-field required">
                    <input class="form-control" type="password" id="password" name="password" required>
                    <label for="password">Mot de passe</label>
                </div>
                <div class="form-info center">
                    <label class="link-forgotten-password">
                        <a href="<?= $url_forgotten_password ?>">Mot de passe oublié ?</a>
                    </label>
                </div>
                <div class="form-action center">
                    <input type="submit" class="submit-btn btn-primary" data-role="submitDefault" value="Se connecter">
                </div>
            </form>
        </article>
    </section>
</main>