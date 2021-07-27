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
            <form class="w-full flex-col" method="POST" action="<?= $url_form ?>">
                <p class="text-lg font-bold pb-1">
                    <input type="text" class="text-xl font-extralight py-0.5 w-full border-none border-bottom-default"
                           name="role_name" placeholder="Nom du rôle" value="<?= $role_name ?>">
                </p>
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
                    <?php if($referer == -1) : ?>
                        <button class="btn-success text-base <?= !$can_create ? 'hidden' : '' ?>" data-role="submitPermissions"
                                data-options='<?= json_encode(['add_data' => ['ref' => $referer]]) ?>'>Ajouter</button>
                    <?php else : ?>
                        <button class="btn-primary text-base <?= !$can_update ? 'hidden' : '' ?>" data-role="submitPermissions"
                                data-options='<?= json_encode(['add_data' => ['ref' => $referer]]) ?>'>Sauvegarder</button>
                    <?php endif; ?>
                </div>
            </form>
        </article>
    </div>
</article>