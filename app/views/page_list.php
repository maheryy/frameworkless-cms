<div class="card rounded">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <table data-role="initDataTable">
        <thead>
        <tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Statut</th>
            <th>Créé le</th>
            <?php if ($can_delete) : ?>
                <th></th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pages as $page) : ?>
            <tr>
                <td>
                    <p class="flex-col justify-between items-center">
                        <a <?= $can_read ? 'href="' . $page['url_detail'] . '"' : '' ?>><?= $page['title'] ?></a>
                        <?php if ($page['status'] == \App\Core\Utils\Constants::STATUS_PUBLISHED) : ?>
                            <em style="font-size:.75em"><?= $page['slug'] ?></em>
                        <?php endif; ?>
                    </p>
                </td>
                <td><?= $page['author'] ?></td>
                <td><?= $page['status_label'] ?></td>
                <td><?= $page['created_at'] ?></td>
                <?php if ($can_delete) : ?>
                    <td>
                        <a href="<?= $page['url_delete'] ?>" data-role="deleteItem">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>