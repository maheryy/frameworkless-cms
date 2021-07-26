<main id="login-content">
    <section class="login-container">
        <?php if (!$is_token_valid) : ?>
            <article class="reset-box card flex-col justify-between items-center">
                <h3 class="text-center">Ce lien n'est plus valide</h3>
                <label class="link-back">
                    <a class="link-default" href="<?= $url_login ?>">Retour</a>
                </label>
            </article>
        <?php elseif ($has_expired) : ?>
            <article class="reset-box card flex-col justify-between items-center">
                <h3 class="text-center">Ce lien a expiré</h3>
                <label class="link-back">
                    <a class="link-default" href="<?= $url_login ?>">Retour</a>
                </label>
            </article>
        <?php else : ?>
            <article class="reset-box card flex-col justify-between items-center">
                <h3 class="text-center">Votre compte est confirmé ! Vous pouvez maintenant vous connecter</h3>
                <label class="link-back">
                    <a class="link-default" href="<?= $url_login ?>">Se connecter</a>
                </label>
            </article>
        <?php endif; ?>
    </section>
</main>