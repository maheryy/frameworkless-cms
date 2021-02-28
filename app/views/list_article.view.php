<form method="POST" action=<?= $form_action ?>>

    <input type="text" name="test" value="<?= $default_test ?>">
    <input type="text" name="title" value="<?= $default_title ?>">
    <input type="text" name="name" value="<?= $default_name ?>">

    <select name="select">
        <?php foreach ($select_options as $value) : ?>
            <option value="<?= $value['value'] ?>" <?= $value['value'] === $default_select ? "selected='selected'" : '' ?>>
                <?= $value['label'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="Save">
</form>