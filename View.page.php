<html>


<style>

    a.button {
        -webkit-appearance: button;
        -moz-appearance: button;
        appearance: button;

        color: initial;
    }

    h1 {

        text-align: center;
        font-size: 300%

    }

    #descc {

        text-align: center;
        font-size: 150%;

    }

    #descc1 {

        text-align: center;
        font-size: 100%;
    }

    header {

        text-align: center;
        font-size: 250%;
    }


</style>

<h1>View page</h1>


<?php require 'View.page.index.php'; ?>
<header> <?= $NameText ?> </header>

<div id="descc" class="" ><?= $DescriptionText ?></div>
<div id="descc1" class="">
    <?php
    $newDate = new DateTime($DateText);
    echo $newDate->format('d m y');
    ?>
    <br><br><br>
    <img src="uploads/<?= $ImageNameText ?>" height="200" width="200">

</div>

<a href="index.php" class="button">Back</a>

</html>

