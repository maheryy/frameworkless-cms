<article class="tab-content">
    <div id="transposable" class="flex items-start" data-role="initTransposable">
        <article class="card w-4/12 px-1.5 py-1 mr-1.75">
            <p class="text-lg font-bold pb-1 py-0.5">Toutes les permissions</p>
            <div id="transpose-source" class="w-full">
                <ul class="list-elements py-1">
                    <?php foreach ($permissions as $permission) : ?>
                        <li class="transpose-element">
                            <span><?= $permission['name'] ?></span>
                            <input type="hidden" name="permissions[]" value="<?= $permission['id'] ?>">
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </article>
        <article class="card w-8/12 px-1.5 py-1">
            <p class="text-lg font-bold pb-1">
                <input type="text" class="text-lg font-bold py-0.5 w-full border-none border-bottom-default"
                       name="role_name" value="<?= $role_name ?>">
            </p>
            <form class="w-full flex-col" method="POST" action="<?= $url_form ?>">
                <div id="transpose-target" class="w-full">
                    <ul class="list-elements py-1">
                        <?php foreach ($role_permissions as $role_permmission) : ?>
                            <li class="transpose-element">
                                <span><?= $role_permmission['name'] ?></span>
                                <input type="hidden" name="permissions[]" value="<?= $role_permmission['id'] ?>">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="form-action px-0 right">
                    <input type="submit" class="btn-success text-base"
                           value="<?= $referer == -1 ? 'Ajouter' : 'Sauvegarder' ?>" data-role="submitDefault"
                           data-options=<?= json_encode(['add_data' => ['ref' => $referer]]) ?>>
                </div>
            </form>
        </article>
    </div>
</article>