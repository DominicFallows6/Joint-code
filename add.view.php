<html>


<h1>Add/Edit page</h1>

<form method="post" action="add.php">

    <?php
    if (!empty($errors)) {
        foreach ($errors as $eKey=>$eVal) {
            ?>
            <span class="error" style="color: #ff0000;"><?= $eVal?></span>
            <br />
            <?php
        }
    }
    ?>

    <p>
        <textarea rows="3" cols="20" name="NameTexts"><?= $NameText ?></textarea>
    </p>

    <p>
        <textarea rows="10" cols="100" name="DescTexts"><?= $DescriptionText ?></textarea>
    </p>

    <input type="hidden" name="action" value="<?= $actionType; ?>">

    <input type="hidden" name="hiddenid" value="<?= $id; ?>">

    <button type="Submit" name="submit" value="edit">Submit</button>
    <br>
    <br>
    <a href="index.php">Back</a>

</form>



</html>

