<main style="padding: 3em 0;width: 100%">
    <section class="py-5 w-full h-full flex flex-col items-center justify-center">
        <h2 class="font-medium text-5xl pb-1">404</h2>
        <h3 class="font-light text-3xl text-uppercase">Page introuvable</h3>
        <?php if(!isset($site_title)) : ?>
            <p class="text-center py-1.5">
                <a href="/admin" class="link-default font-light text-base"> Revenir à l'accueil</a>
            </p>
        <?php endif; ?>
    </section>
</main>