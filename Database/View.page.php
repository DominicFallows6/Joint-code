<html>


<h1>View page</h1>

<style>
    #time{

        align: center;


    }

    a.button {
        -webkit-appearance: button;
        -moz-appearance: button;
        appearance: button;

        color: initial;
    }




</style>
<?php
require 'View.page.index.php';

?>
<header> <?= $NameText ?> </header>
 <p>
     <textarea readonly rows="10" cols="100"><?=$DescriptionText?></textarea>
     <div>  </div>
<textarea readonly id="time" rows="3" cols="10"><?=$DateText?></textarea>
 </p>

<a href="index.php" class="button">Back</a>

</html>

