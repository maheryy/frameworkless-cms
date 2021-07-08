<main id="installer-content">
    <section class="installer-container">
        <h3 class="text-center">Bienvenue sur Munkee</h3>
        <article id="info-box" class="info-primary center">
            <span id="info-description"></span>
        </article>
        <p>Veuillez remplir le formulaire ci-dessous pour utiliser toutes les fonctionnalités de Munkee</p>
        <form class="form-default" method="POST" action="<?= $url_form ?>">
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
            <div class="form-action center btn">
                <input type="submit" class="btn-success" data-role="submitDefault" value="Valider">
            </div>
        </form>
    </section>
</main>