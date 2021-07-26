<div class="">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <section class="card rounded mb-1.5">
        <h3 class="font-normal mb-0.5">Envoyer une newsletter</h3>
        <article class="card-content">
            <form class="flex justify-between" method="POST" action="<?= $url_form ?>">
                <div class="col1 w-2/5 p-1">
                    <div class="form-field">
                        <select class="form-control" id="newsletter" name="newsletter">
                            <?php foreach ($newsletters as $newsletter) : ?>
                                <option value="<?= $newsletter['id'] ?>"><?= $newsletter['title'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="newsletter">Sélectionner la newsletter</label>
                    </div>
                    <div class="form-action mt-1">
                        <button class="btn-success" data-role="submitPermissions"
                                data-options='<?= json_encode(['add_data' => ['send_all' => true]]) ?>'>Envoyer à tous
                        </button>
                        <button class="btn-primary" data-role="submitPermissions">Envoyer à la sélection</button>
                    </div>
                </div>
                <div class="col2 w-3/5 p-1">
                    <ul class="w-8/12 m-auto flex flex-col" data-role="listCheckAll">
                        <li>
                            <div class="form-field-inline">
                                <input type="checkbox" id="ref_check_all">
                                <label for="ref_check_all" class="style-italic text-sm">Tout sélectionner</label>
                            </div>
                        </li>
                        <?php foreach ($subscribers as $key => $subscriber) : ?>
                            <li class="list-element">
                                <div class="form-field-inline">
                                    <input type="checkbox" id="subscriber-<?= $key ?>" name="subscribers[]"
                                           value="<?= $subscriber['id'] ?>">
                                    <label for="subscriber-<?= $key ?>"><?= $subscriber['email'] ?></label>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </form>
        </article>
    </section>
    <section class="card rounded">
        <table data-role="initDataTable">
            <thead>
            <tr>
                <th>Sujet</th>
                <th>Statut</th>
                <th>Créée le</th>
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
    </section>
</div>