<header id="toolbar">
    <a id="logo" href="/">
        <span class="logo">Munkee</span>
    </a>
    <div id="toolbar__links">
        <nav id="toolbar-links">
            <ul>
                <!-- <li>
                  <a href="#">
                     <img alt="" src="assets/home.png">
                     <span class="label-item">item 1</span>
                  </a>
               </li> -->
            </ul>
        </nav>
        <nav>
            <a id="profile" href="<?= $link_user ?>">
                <i class="link-icon fas fa-user"></i>
                <span class="label-item"></span>
            </a>
            <a id="logout" href="<?= $link_logout ?>">
                <i class="link-icon fas fa-sign-out-alt"></i>
            </a>
        </nav>
    </div>
</header>