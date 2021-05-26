<section class="installer-container">
    <h3 class="text-center">Bienvenue sur Go Travel</h3>
    <p>Veuillez remplir ci-dessous les détails de connexion à votre base de données.</p>
    <form class="form-default">
        <div class="form-filed-info">
            <label for="name-bdd">Nom de la base de données</label>
            <input type="text" class="form-control" id="name_bdd" name="name_bdd" placeholder="GoTravel">
            <span>Le nom de la base de données avec laquelle vous souhaitez utilisez Go Travel</span>
        </div>
        <div class="form-filed-info">
            <label for="id">Identifiant</label>
            <input type="text" class="form-control" id="id" name="id" placeholder="utilisateur">
            <span>Nom d'utilisateur MySQL</span>
        </div>
        <div class="form-filed-info">
            <label for="password">Mot de passe de la base de données</label>
            <input type="password" class="form-control" id="password" name="password" value="aaaazzzz">
            <span>Votre mot de passe de base de données</span>
        </div>
        <div class="form-filed-info">
            <label for="password_confirm">Confirmez le mot de passe de la base de données</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" value="bbbbnnnn">
            <span>Confirmez le mot de passe de base de données</span>
        </div>
        <div class="form-filed-info">
            <label for="adress">Adresse de la base de données</label>
            <input type="text" class="form-control" id="adress" name="adress" placeholder="localhost">
            <span>Si localhost ne fonctionne pas, demandez cette information à l'hébergeur de votre site</span>
        </div>
        <div class="form-action center btn">
            <a href="/installer-register" class="btn-success">Installer Go Travel</a>
        </div>
    </form>
</section>