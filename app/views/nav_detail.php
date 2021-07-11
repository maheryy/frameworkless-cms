<article class="content transparent flex-col">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <div class="flex items-start">
        <article class="card w-4/12 px-1.5 py-1 mr-1.75 flex-col">
            <p class="text-lg font-bold pb-1">Pages</p>
            <ul class="">
                <?php foreach ($pages as $page) : ?>
                    <li> <?= $page['title'] ?></li>
                <?php endforeach; ?>
            </ul>
        </article>
        <article class="card w-8/12 px-1.5 py-1">
            <p class="text-lg font-bold pb-1">Navigation</p>
            <form class="w-full flex-col" method="POST" action="<?= $url_form ?>">
                <div class="form-field py-1 w-2/5 px-0">
                    <select class="form-control w-full" id="navigation_type" name="navigation_type">
                        <?php foreach ($nav_types as $value => $label) : ?>
                            <option value="<?= $value ?>"> <?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="navigation_type">Type de navigation</label>
                </div>
                <ul class="">
                    <?php foreach ($pages as $page) : ?>
                        <li> <?= $page['title'] ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="form-action px-0 right">
                    <input type="submit" class="btn-success text-base" value="Valider" data-role="submitDefault">
                </div>
            </form>
        </article>
    </div>
</article>