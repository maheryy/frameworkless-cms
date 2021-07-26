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
            <th>Créée le</th>
            <th></th>
            <?php if ($can_read) : ?>
                <th></th>
            <?php endif; ?>
            <?php if ($can_delete) : ?>
                <th></th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pages as $page) : ?>
            <tr>
                <td>
                    <a class="link-default" <?= $can_read ? 'href="' . $page['url_detail'] . '"' : '' ?>><?= $page['title'] ?></a>
                </td>
                <td><?= $page['author'] ?></td>
                <td><?= $page['status_label'] ?></td>
                <td><?= $page['created_at'] ?></td>
                <?php if ($page['status'] == \App\Core\Utils\Constants::STATUS_PUBLISHED) : ?>
                    <td class="w-1/12 text-center">
                        <a class="link-default" href="<?= $page['slug'] ?>"><i class="far fa-eye"></i></a>
                    </td>
                <?php else: ?>
                    <td></td>
                <?php endif; ?>
                <?php if ($can_read) : ?>
                    <td class="w-1/12 text-center">
                        <a class="link-default" href="<?= $page['url_detail'] ?>"><i class="fas fa-edit"></i></a>
                    </td>
                <?php endif; ?>
                <?php if ($can_delete) : ?>
                    <td>
                        <a href="<?= $page['url_delete'] ?>" data-role="actionItem">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>