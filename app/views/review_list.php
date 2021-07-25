<div class="card rounded">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <table data-role="initDataTable">
        <thead>
        <tr>
            <th>Auteur</th>
            <th>Commentaire</th>
            <th>Statut</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reviews as $review) : ?>
            <tr>
                <td>
                    <p><?= $review['author'] ?></p>
                    <p"><a href="mailto:<?= $review['email'] ?>"><?= $review['email'] ?></a></em>
                    </p>
                </td>
                <td>
                    <p class="text-left text-sm font-light style-italic">Note : <?= $review['rate'] ?>/5</p>
                    <p class="text-left py-0.5"><?= nl2br($review['review']) ?></p>
                </td>
                <td> <?= $review_statuses[$review['status']] ?></td>
                <?php if ($review['status'] != \App\Core\Utils\Constants::REVIEW_PENDING) : ?>
                    <td>
                        <a href="<?= "{$url_action}?action=hold&id={$review['id']}" ?>" data-role="actionItem">
                            <i class="fas fa-ellipsis-h"></i>
                        </a>
                    </td>
                <?php endif; ?>
                <?php if ($review['status'] != \App\Core\Utils\Constants::REVIEW_VALID) : ?>
                    <td>
                        <a href="<?= "{$url_action}?action=approve&id={$review['id']}" ?>" data-role="actionItem">
                            <i class="fas fa-check"></i>
                        </a>
                    </td>
                <?php endif; ?>
                <?php if ($review['status'] != \App\Core\Utils\Constants::REVIEW_INVALID) : ?>
                    <td>
                        <a href="<?= "{$url_action}?action=reject&id={$review['id']}" ?>" data-role="actionItem">
                            <i class="fas fa-times"></i>
                        </a>
                    </td>
                <?php endif; ?>
                <td>
                    <a href="<?= "{$url_action}?action=delete&id={$review['id']}" ?>" data-role="actionItem">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>