<section class="content review">
    <h2>Votre avis compte !</h2>
    <article class="review-content">
        <form class="review-form" action="review">
            <div class="form-field">
                <div class="rating">
                    <input type="radio" id="star1" name="rate" value="1">
                    <input type="radio" id="star2" name="rate" value="2">
                    <input type="radio" id="star3" name="rate" value="3">
                    <input type="radio" id="star4" name="rate" value="4" checked>
                    <input type="radio" id="star5" name="rate" value="5">
                    <label for="star1"></label>
                    <label for="star2"></label>
                    <label for="star3"></label>
                    <label for="star4"></label>
                    <label for="star5"></label>
                </div>
            </div>
            <div class="form-field">
                <input type="text" id="name" name="name" placeholder="Votre nom">
            </div>
            <div class="form-field">
                <input type="email" id="email" name="email" placeholder="Votre adresse email">
            </div>
            <div class="form-field">
                <textarea id="review" name="review" rows="7" placeholder="Donnez votre avis..."></textarea>
            </div>
            <div class="form-field">
                <button class="form-action">Envoyer</button>
            </div>
            <div class="info-box">
                <span class="info-description"></span>
            </div>
        </form>
    </article>
</section>
