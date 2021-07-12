<article class="content">
    <table data-role="initDataTable">
        <thead>
        <tr>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Role</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><a href="<?= $user['url_detail'] ?>"><?= $user['username'] ?></a></td>
                <td><?= $user['email'] ?></td>
                <td><?= $user['role_name'] ?></td>
                <td>
                    <a href="<?= $user['url_delete'] ?>" data-role="deleteItem">
                        <i class="fas fa-trash-alt"></i>
                    </a>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</article>