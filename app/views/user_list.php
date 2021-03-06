<article class="content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <table data-role="initDataTable">
        <thead>
        <tr>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Role</th>
            <?php if ($can_read) : ?>
                <th></th>
            <?php endif; ?>
            <?php if ($can_delete) : ?>
                <th></th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><a class="link-default" <?= $can_read ? 'href="' . $user['url_detail'] . '"' : '' ?>><?= $user['username'] ?></a></td>
                <td><?= $user['email'] ?></td>
                <td><?= $user['role_name'] ?></td>
                <?php if ($can_read) : ?>
                    <td class="w-1/12 text-center">
                        <a class="link-default" href="<?= $user['url_detail'] ?>"><i class="fas fa-edit"></i></a>
                    </td>
                <?php endif; ?>
                <?php if ($can_delete) : ?>
                    <td>
                        <a href="<?= $user['url_delete'] ?>" data-role="actionItem" data-options='<?= json_encode(['confirm' => true]) ?>'>
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</article>