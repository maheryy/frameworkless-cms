<nav id="sidebar">
   <ul class="sidebar-list">
      <?php foreach ($sidebar_links as $link) : ?>
         <li class="sidebar-link <?= !isset($link['sub-links']) && $current_route === $link['route']? 'selected' : '' ?>">
            <a href="<?= $link['route'] ?>">
               <i class="link-icon <?= $link['icon'] ?>"></i>
               <span class="label-item"><?= $link['label'] ?></span>
            </a>
            <?php if (isset($link['sub-links'])) : ?>
               <ul class="sidebar-sub-list">
                  <?php foreach ($link['sub-links'] as $sub_link) : ?>
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
      <li>
         <a href=" <?= $link_settings['route'] ?>">
            <i class="link-icon  <?= $link_settings['icon'] ?>"></i>
            <span class="label-item"> <?= $link_settings['label'] ?></span>
         </a>
      </li>
   </ul>
</nav>