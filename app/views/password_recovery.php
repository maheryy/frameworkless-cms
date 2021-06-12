<main id="login-content">
    <section class="login-container">
        <h2 class="text-center">Demande de réinitialisation</h2>
        <article class="login-box card">
            <article id="info-box" class="info-primary">
                <span id="info-description"></span>
            </article>
            <form class="form-default" method="POST" action="<?= $url_form ?>">
                <div class="form-field required">
                    <input class="form-control" type="email" id="login" name="login" required>
                    <label for="login">Nom d'utilisateur ou email</label>
                </div>
                <div class="form-info center">
                    <label class="link-back">
                        <a href="<?= $url_back ?>">Retour</a>
                    </label>
                </div>
                <div class="form-action center">
                    <input type="submit" class="submit-btn btn-success" data-role="submitDefault" value="Envoyer">
                </div>
            </form>
        </article>
    </section>
</main>