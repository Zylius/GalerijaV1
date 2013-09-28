<html>
<head>
    <title>Galerijos link</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
if (isset($_COOKIE["nuotrauka"])) {
    echo '
<img height="400px" src="upload/' . $_COOKIE["nuotrauka"] . ' "/>';
}
?>
<form action="upload.php?action=upload" method="post"
      enctype="multipart/form-data">
    <label for="file">Filename:</label>
    <input type="file" name="file" id="file" accept="image/*">

    <p>
        <input type="submit" name="submit" value="<?php echo(isset($_COOKIE["nuotrauka"]) ? 'Pakeisti' : 'Įkelti') ?>">
    </p>
</form>
<?php
if (isset($_COOKIE["nuotrauka"])) {
    ?>
    <form action="upload.php?action=delete" method="post">
        <p>
            <input type="submit" name="submit" value="Ištrinti">
        </p>
    </form>
<?php
}
?>
</body>
</html>
