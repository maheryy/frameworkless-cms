<div class="content list-review">
    <?php if (!empty($reviews)) : ?>
    <h2>Les avis de nos clients</h2>
    <ul class="list-container">
        <?php foreach ($reviews as $review) : ?>
            <li class="review-item">
                <div class="head">
                    <div class="stars">
                        <div class="rating">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <label for="star<?= $i ?>"
                                       class="default <?= $i <= $review['rate'] ? 'active' : '' ?>"></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <span class="description">par <?= $review['author'] ?>, <?= date('d F Y', strtotime($review['date'])) ?></span>
                </div>
                <div class="item-content">
                    <p><?= $review['review'] ?></p>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
        <h2 class="text-3xl font-semibold" >Aucun avis disponible</h2>
    <?php endif; ?>
</div>
