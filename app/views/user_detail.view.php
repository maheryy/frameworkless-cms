<section class="">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <form class="form-default" method="POST" action="<?= $url_form ?>">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
        <div class="form-field required">
            <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>">
            <label for="username">Nom d'utilisateur</label>
        </div>
        <div class="form-field required">
            <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>">
            <label for="email">Adresse email</label>
        </div>
        <div class="form-field required">
            <select class="form-control" id="role" name="role">
                <?php foreach ($roles as $key => $role) : ?>
                    <option <?= $user['role'] == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $role ?></option>
                <?php endforeach; ?>
            </select>
            <label for="role">Role</label>
        </div>
        <div class="form-field">
            <button class="btn-secondary" data-role="switchPasswordUpdate">Changer de mot de passe</button>
            <div id="change-password" class="form-field-inline required hidden">
                <input type="password" class="form-control" name="password" id="password" disabled>
                <label for="password">Mot de passe</label>
            </div>
        </div>
        <div class="form-action">
            <input type="submit" class="btn-success" data-role="submitDefault" value="Enregistrer">
            <button class="btn-danger" data-url="<?= $url_delete ?>" data-role="deleteItem">Supprimer</button>
        </div>
    </form>
</section>