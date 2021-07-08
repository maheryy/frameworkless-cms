<div class="card rounded">
    <table data-role="initDataTable">
        <thead>
        <tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Statut</th>
            <th>Créé le</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pages as $page) : ?>
            <tr>
                <td>
                    <p class="flex-col-center">
                        <a href="<?= $page['url_detail'] ?>"><?= $page['title'] ?></a>
                        <?php if ($page['status'] == \App\Core\Utils\Constants::STATUS_PUBLISHED) : ?>
                            <em style="font-size:.75em"><?= '/' . $page['slug'] ?></em>
                        <?php endif; ?>
                    </p>
                </td>
                <td><?= $page['author'] ?></td>
                <td><?= $page['status_label'] ?></td>
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