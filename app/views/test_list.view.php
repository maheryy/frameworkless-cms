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

<section id="list" style="margin-top:1em">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>State</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $random) : ?>
                <tr>
                    <td><?= $random['id'] ?></td>   
                    <td><a href="<?= $random['url_edit'] ?>"><?= $random['name'] ?></a></td>   
                    <td><?= $random['email'] ?></td>   
                    <td><?= $random['state'] ?></td> 
                    <td><a href="<?= $random['url_delete'] ?>">Delete</a></td>   
                </tr>
            <?php endforeach; ?>
            <tr></tr>
        </tbody>
    </table>
</section>