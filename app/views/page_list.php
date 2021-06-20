<div class="card rounded">
    <table data-role="initDataTable">
        <thead>
        <tr>
            <th>Titre</th>
            <th>Autheur</th>
            <th>Statut</th>
            <th>Créé le</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pages as $page) : ?>
            <tr>
                <td><a href="<?= $page['url_detail'] ?>"><?= $page['title'] ?></a></td>
                <td><?= $page['author'] ?></td>
                <td><?= $page['status'] ?></td>
                <td><?= $page['created_at'] ?></td>
                <td>
                    <a href="<?= $page['url_delete'] ?>" data-role="deleteItem">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>