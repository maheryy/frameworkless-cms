<section class="">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <form class="form-default" method="POST" action="<?= $url_form ?>">
        <div class="form-field required">
            <input type="text" class="form-control" id="username" name="username">
            <label for="username">Nom d'utilisateur</label>
        </div>
        <div class="form-field required">
            <input type="email" class="form-control" id="email" name="email">
            <label for="email">Adresse email</label>
        </div>
        <div class="form-field required">
            <input type="password" class="form-control" id="password" name="password">
            <label for="password">Mot de passe</label>
        </div>
        <div class="form-field required">
            <select class="form-control" id="role" name="role">
                <?php foreach ($roles as $role) : ?>
                    <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <label for="role">Role</label>
        </div>
        <div class="form-action">
            <input type="submit" class="btn-success" data-role="submitDefault" value="Valider">
        </div>
    </form>
</section>