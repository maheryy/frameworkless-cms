<div class="card rounded">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <table data-role="initDataTable">
        <thead>
        <tr>
            <th>Sujet</th>
            <th>Statut</th>
            <th>Créé le</th>
            <?php if ($can_read) : ?>
                <th></th>
            <?php endif; ?>
            <?php if ($can_delete) : ?>
                <th></th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($newsletters as $newsletter) : ?>
            <tr>
                <td>
                    <span class="font-light text-base"><?= $newsletter['title'] ?></span>
                </td>
                <td><?= $statuses[$newsletter['status']] ?></td>
                <td><?= $newsletter['created_at'] ?></td>
                <?php if ($can_read) : ?>
                    <td class="w-1/12 text-center">
                        <a class="link-default"
                           href="<?= \App\Core\Utils\UrlBuilder::makeUrl('Newsletter', 'newsletterView', ['id' => $newsletter['id']]) ?>">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                <?php endif; ?>
                <?php if ($can_delete) : ?>
                    <td>
                        <a href="<?= \App\Core\Utils\UrlBuilder::makeUrl('Newsletter', 'deleteAction', ['id' => $newsletter['id']]) ?>"
                           data-role="actionItem">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>