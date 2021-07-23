<div class="content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <form class="form-default" method="POST" action="<?= $url_form ?>">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
        <div class="form-field required">
            <input type="text" class="form-control" id="username" name="username"
                   value="<?= $user['username'] ?>" <?= $hold_confirmation ? 'disabled' : '' ?>>
            <label for="username">Nom d'utilisateur</label>
        </div>
        <div class="form-field required">
            <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>">
            <label for="email">Adresse email</label>
        </div>
        <div class="form-field required">
            <select class="form-control" id="role" name="role" <?= $hold_confirmation ? 'disabled' : '' ?>>
                <?php foreach ($roles as $role) : ?>
                    <option <?= $user['role'] == $role['id'] ? 'selected' : '' ?>
                            value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <label for="role">Role</label>
        </div>
        <?php if ($is_current_user) : ?>
            <div class="form-field">
                <button class="btn-secondary" data-role="switchPasswordUpdate">Changer de mot de passe</button>
                <div id="change-password" class="form-field-inline required hidden">
                    <input type="password" class="form-control" name="password" id="password" disabled>
                    <label for="password">Mot de passe</label>
                </div>
            </div>
        <?php endif; ?>
        <div class="form-action">
            <?php if ($hold_confirmation) : ?>
                <input class="btn-primary <?= !$can_update ? 'hidden' : '' ?>" data-role="submitDefault" value="Renvoyer une confirmation" >
            <?php else : ?>
                <input type="submit" class="btn-success <?= !$can_update ? 'hidden' : '' ?>" data-role="submitDefault" value="Enregistrer">
            <?php endif; ?>
            <button class="btn-danger <?= !$can_delete ? 'hidden' : '' ?>" data-url="<?= $url_delete ?>" data-role="deleteItem">Supprimer</button>
        </div>
    </form>
</div>