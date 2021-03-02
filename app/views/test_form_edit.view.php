<section id="form-edit">
    <h3><?= $random_data['name'] ?></h3>
    <form method="POST" action=<?= $form_action ?>>

        <input type="text" name="name" placeholder="name" value="<?= $random_data['name'] ?>">
        <input type="text" name="email" placeholder="email" value="<?= $random_data['email'] ?>">
        <input type="password" name="password" placeholder="password" value="<?= $random_data['password'] ?>">

        <select name="state">
            <?php foreach ($states as $key => $value) : ?>
                <option value="<?= $key ?>" <?= $key == $random_data['state'] ? "selected='selected'" : '' ?>>
                    <?= $value ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Save">
    </form>
</section>