<html>
<head>
    <title>Galerijos link</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="media/css/style.css">
</head>
<body>
<?php
    const JS = 'media/js/';
    const CSS = 'media/css/';
?>
<?php if (isset($_GET["err"])): ?>
    <div id="msg" data-error="true">AHA</div>
<?php endif; ?>
<?php if (!isset($_GET["err"])): ?>
    <div id="msg" data-error="false">AHA</div>
<?php endif;
if (isset($_COOKIE["nuotrauka"])) {
    echo '<div id="image"><img height="400px" src="upload/' . $_COOKIE["nuotrauka"] . ' "/></div>';
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
        <form action="upload.php?action=delete" method="post" id="myForm">
            <p>
                <input type="submit" name="submit" value="Ištrinti">
            </p>
        </form>
    <?php
    }
    ?>
</body>
<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="<?php echo JS;?>/gallery.js"></script>
<html>
