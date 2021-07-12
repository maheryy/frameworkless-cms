<article class="content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <div class="w-full flex justify-between mb-2">
        <div class="w-4/12">
            <select id="tab_view" class="form-control w-full text-base" name="tab_view" data-role="initSelectTabs"
                    data-options=<?= json_encode($tab_options) ?>>
                <option value="-1">Ajouter une navigation</option>
                <?php foreach ($navigations as $nav) : ?>
                    <option <?= $default_tab == $nav['id'] ? 'selected=selected' : '' ?>
                            value="<?= $nav['id'] ?>"><?= $nav['title'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <a href="#" class="text-3xl px-0.5" style="color: darkolivegreen" data-role="addRoleTabView">
                <i class="fas fa-plus-circle"></i>
            </a>
        </div>
    </div>
    <div id="tab-content">
        <?php include $default_tab_view ?>
    </div>
</article>