<main id="login-content">
    <section class="login-container">
        <article class="reset-box card flex-col justify-between items-center">
            <?php if ($error) : ?>
                <h3 class="text-center">Une erreur est survenue</h3>
            <?php elseif ($success) : ?>
                <h3 class="text-center">Votre désabonnement a bien été pris en compte</h3>
            <?php else : ?>
                <h3 class="text-center">Vous n'êtes plus abonné à notre newsletter</h3>
            <?php endif; ?>
            <label class="link-back">
                <a href="/">Retour au site</a>
            </label>
        </article>
    </section>
</main>