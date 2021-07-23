<div class="content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <div class="w-full flex justify-between mb-2">
        <div class="w-4/12">
            <select id="tab_view" class="form-control w-full text-base" name="tab_view" data-role="initSelectTabs"
                    data-options=<?= json_encode($tab_options) ?>>
                <?php if ($can_create) : ?>
                    <option value="-1">Ajouter un menu</option>
                <?php endif; ?>
                <?php $last_type = null; ?>
                <?php foreach ($menus as $menu) : ?>
                    <?php if (is_null($last_type)) : ?>
                         <optgroup label="<?= $menu_types[$menu['type']] ?>">
                    <?php elseif ($menu['type'] != $last_type) : ?>
                        </optgroup>
                        <optgroup label="<?= $menu_types[$menu['type']] ?>">
                    <?php endif; ?>
                    <option <?= $default_tab == $menu['id'] ? 'selected=selected' : '' ?> value="<?= $menu['id'] ?>">
                        <?= $menu['title'] ?></option>
                    <?php $last_type = $menu['type']; ?>
                <?php endforeach; ?>
                <?= $last_type ? '</optgroup>' : ''?>
            </select>
        </div>
        <?php if ($can_create) : ?>
            <div>
                <a href="#" class="text-3xl px-0.5" style="color: darkolivegreen" data-role="addRoleTabView">
                    <i class="fas fa-plus-circle"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div id="tab-content">
        <?php include $default_tab_view ?>
    </div>
</div>