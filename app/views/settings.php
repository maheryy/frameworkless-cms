<article class="content">
    <article id="info-box" class="info-primary center">
        <span id="info-description"></span>
    </article>

    <article id="tabs-container" data-role="initClassicalTabs">
        <ul id="tab-list" class="w-full">
            <li class="tab-item">
                <button class="tab-btn <?= $active_view === 1 ? 'active' : ''?>" data-id="1">Général</button>
            </li>
            <li class="tab-item">
                <button class="tab-btn <?= $active_view === 2 ? 'active' : ''?>" data-id="2">Mail</button>
            </li>
            <li class="tab-item">
                <button class="tab-btn <?= $active_view === 3 ? 'active' : ''?>" data-id="3">Page</button>
            </li>
            <li class="tab-item">
                <button class="tab-btn <?= $active_view === 4 ? 'active' : ''?>" data-id="4">Apparance</button>
            </li>
        </ul>
        <div id="content-container" class="w-full">
            <article id="content-1" class="tab-content <?= $active_view === 1 ? 'active' : 'hidden'?>">
                Général
            </article>
            <article id="content-2" class="tab-content <?= $active_view === 2 ? 'active' : 'hidden'?>">
                Mail
            </article>
            <article id="content-3" class="tab-content <?= $active_view === 3 ? 'active' : 'hidden'?>">
                Page
            </article>
            <article id="content-4" class="tab-content <?= $active_view === 4 ? 'active' : 'hidden'?>">
                Apparance
            </article>
        </div>
    </article>
</article>