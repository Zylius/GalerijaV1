<style>
    img {
        width: 400px;
        height: auto;
    }
</style>
<html>
<body>
<?php
if (isset($_COOKIE["nuotrauka"])) {
    echo '
<img height="400px" src="upload/' . $_COOKIE["nuotrauka"] . ' "/>';
} else {
    ?>
    <form action="upload.php" method="post"
          enctype="multipart/form-data">
        <label for="file">Filename:</label>
        <input type="file" name="file" id="file"><br>
        <input type="submit" name="submit" value="Submit">
    </form>
<?php
}
?>
</body>
</html>
