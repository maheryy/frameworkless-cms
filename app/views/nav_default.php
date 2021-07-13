<article class="content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <div class="w-full flex justify-between mb-2">
        <div class="w-4/12">
            <select id="tab_view" class="form-control w-full text-base" name="tab_view" data-role="initSelectTabs"
                    data-options=<?= json_encode($tab_options) ?>>
                <option value="-1">Ajouter une navigation</option>
                <?php $last_type = null; ?>
                <?php foreach ($navs as $nav) : ?>
                    <?php if (is_null($last_type)) : ?>
                         <optgroup label="<?= $nav_types[$nav['type']] ?>">
                    <?php elseif ($nav['type'] != $last_type) : ?>
                        </optgroup>
                        <optgroup label="<?= $nav_types[$nav['type']] ?>">
                    <?php endif; ?>
                    <option <?= $default_tab == $nav['id'] ? 'selected=selected' : '' ?> value="<?= $nav['id'] ?>">
                        <?= $nav['title'] . ($nav['status'] == \App\Core\Utils\Constants::STATUS_ACTIVE ? ' - Active' : '') ?></option>
                    <?php $last_type = $nav['type']; ?>
                <?php endforeach; ?>
                <?= $last_type ? '</optgroup>' : ''?>
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