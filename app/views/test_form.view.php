<section id="form-create">
    <form method="POST" action=<?= $form_action ?>>

        <input type="text" name="name" placeholder="name">
        <input type="text" name="email" placeholder="email">
        <input type="password" name="password" placeholder="password">

        <select name="state">
            <?php foreach ($states as $key => $value) : ?>
                <option value="<?= $key ?>">
                    <?= $value ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Save">
    </form>
</section>