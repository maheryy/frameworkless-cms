<main id="login-content">
    <section class="login-container">
        <?php if (!$is_token_valid) : ?>
            <article class="reset-box card flex-col justify-between items-center">
                <h3 class="text-center">Ce lien n'est plus valide</h3>
                <label class="link-back">
                    <a href="<?= $url_back ?>">Retour</a>
                </label>
            </article>
        <?php elseif ($has_expired) : ?>
            <article class="reset-box card flex-col justify-between items-center">
                <h3 class="text-center">Ce lien a expiré</h3>
                <label class="link-back">
                    <a href="<?= $url_back ?>">Retour</a>
                </label>
            </article>
        <?php else : ?>
            <h2 class="text-center"><?= $title ?></h2>
            <article class="login-box card">
                <article id="info-box" class="info-primary">
                    <span id="info-description"></span>
                </article>
                <form class="form-default" method="POST" action="<?= $url_form ?>">
                    <div class="form-field required">
                        <input class="form-control" type="password" id="password" name="password" required>
                        <label for="password">Nouveau mot de passe</label>
                    </div>
                    <div class="form-field required">
                        <input class="form-control" type="password" id="password_confirm" name="password_confirm" required>
                        <label for="password">Confirmation mot de passe</label>
                    </div>
                    <div class="form-field hidden">
                        <input type="hidden" name="reference" value="<?= $reference ?>">
                        <input type="hidden" name="token" value="<?= $token ?>">
                    </div>
                    <div class="form-info center">
                        <label class="link-back">
                            <a href="<?= $url_back ?>">Retour</a>
                        </label>
                    </div>
                    <div class="form-action center">
                        <button class="submit-btn btn-success" data-role="submitDefault">Valider</button>
                    </div>
                </form>
            </article>
        <?php endif; ?>
    </section>
</main>