<article class="content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>
    <div class="w-full flex justify-between">
        <div>
            <select name="tab_view" data-role="initSelectTabs" data-options=<?= json_encode($tab_options) ?>>
                <option value="1">Admin</option>
                <option value="2">Super Admin</option>
            </select>
        </div>
        <div>
            <a class="text-3xl px-0.5" href="<?= "#" ?>" style="color: darkolivegreen">
                <i class="fas fa-plus-circle"></i>
            </a>
        </div>
    </div>
    <div id="tab-content">
        Content
    </div>
</article>