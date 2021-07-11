<article class="content flex-col justify-between">
    <div class="flex-col justify-between w-full py-1.5 items-end">
        <p>
            <a class="text-3xl px-0.5" href="<?= $new_navigation_link ?>" style="color: darkolivegreen">
                <i class="fas fa-plus-circle"></i>
            </a>
        </p>
    </div>
    <table data-role="initDataTable">
        <thead>
        <tr>
            <th>Titre</th>
            <th>Type</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($navigations as $nav) : ?>
            <tr>
                <td><a href="<?= $nav['url_detail'] ?>"><?= $nav['title'] ?></a></td>
                <td><?= $nav['type'] ?></td>
                <td>
                    <a href="<?= $nav['url_delete'] ?>" data-role="deleteItem">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</article>