<nav id="sidebar">
    <p class="top-content">
        <a id="logo" href="<?= $link_home ?>">
            <span class="logo">Munkee</span>
        </a>
        <span class="user-action">
            <a href="<?= $link_user ?>">
                <i class="link-icon fas fa-user"></i>
            </a>
            <a href="<?= $link_website ?>">
                <i class="link-icon fas fa-globe"></i>
            </a>
            <a href="<?= $link_logout ?>">
                <i class="link-icon fas fa-sign-out-alt"></i>
            </a>
        </span>
    </p>
    <ul class="sidebar-list">
        <?php foreach ($sidebar_list as $link) : ?>
            <li class="sidebar-link <?= !isset($link['sublinks']) && $current_route === $link['route'] ? 'selected' : '' ?>">
                <a href="<?= $link['route'] ?>">
                    <i class="link-icon <?= $link['icon'] ?>"></i>
                    <span class="label-item"><?= $link['label'] ?></span>
                </a>
                <?php if (isset($link['sublinks'])) : ?>
                    <ul class="sidebar-sub-list">
                        <?php foreach ($link['sublinks'] as $sub_link) : ?>
                            <?php if (!$sub_link['is_visible']) continue; ?>
                            <li <?= $current_route === $sub_link['route']
                                ? "class='sidebar-link selected' data-role='setActiveLink'"
                                : "class='sidebar-link'" ?>>
                                <a href="<?= $sub_link['route'] ?>">
                                    <span class="label-sub-item"><?= $sub_link['label'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <ul class="sidebar-list bottom">
        <?php if ($sidebar_settings['is_visible']) : ?>
            <li class="sidebar-link <?= $current_route === $sidebar_settings['route'] ? 'selected' : '' ?>">
                <a href=" <?= $sidebar_settings['route'] ?>">
                    <i class="link-icon  <?= $sidebar_settings['icon'] ?>"></i>
                    <span class="label-item"> <?= $sidebar_settings['label'] ?></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>