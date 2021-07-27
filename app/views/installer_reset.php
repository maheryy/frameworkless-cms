<main style="height: 100vh">
    <section class="w-full h-full flex flex-col items-center">
        <div class="mb-10">
            <article id="info-box" class="info-primary center">
                <span id="info-description"></span>
            </article>
        </div>
        <article>
            <h2 class="font-light text-3xl py-1.5 text-center">Munkee est bien à jour !</h2>
            <h3 class="font-light text-xl py-1 text-center">Voulez-vous réinitialiser la base de données ?</h3>
            <p class="text-center pb-0.25">
                <a href="<?= $url_drop ?>" class="link-default font-light text-base" data-role="actionItem" data-options='<?= json_encode(['confirm' => true]) ?>'> Cliquer ici pour réinitialiser l'application</a>
            </p>
            <p class="text-center">
                <a href="<?= $url_back ?>" class="link-default font-light text-base"> Retourner à l'accueil</a>
            </p>
        </article>
    </section>
</main>
