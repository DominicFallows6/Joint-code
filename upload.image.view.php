<html>
<body>
<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="fileToUpload" id="fileToUpload">

    <input type="hidden" name="imagehiddenid" value="<?= $id; ?>">

    <input type="submit" value="upload image" name="Submit">

</form>
</body>

</html>
