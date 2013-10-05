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
include_once("settings/db_config.php");

$galerija_db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("Error " . mysqli_error($galerija_db));
$query = "SELECT `ID`, `pavadinimas`, `aprasymas`, CONCAT(ID, '.', ext) AS path FROM `nuotraukos` ";
if (isset($_GET["status"])) {
    switch ($_GET["status"]) {
        case "OK":
            echo '<div class="msg" id="msgok">Operacija sėkmingai atlikta.</div>';
            break;
        case "err":
            echo '<div class="msg" id="msgerr">Netinkamas failas.</div>';
            break;
        default:
            echo '<div class="msg" id="msgerr">Klaida.</div>';
    }
}
$result = mysqli_query($galerija_db, $query) or die("Error " . mysqli_error($galerija_db));
?>
<div>
    <ul id="imglist">
        <?php
        while ($row = mysqli_fetch_array($result)) {
            ?>
            <li id="imglist">
                <div id="image">
                    <img alt="<?php echo $row["pavadinimas"] ?>" src="./upload/<?php echo $row["path"] ?>"/>
                    <form action="upload.php?action=delete" method="post" enctype="multipart/form-data">
                        <input type="image" name="ID" src="./media/images/delete.png" value="<?php echo $row["ID"] ?>"/>
                    </form>
                </div>
            </li>
        <?php
        }
        ?>
</div>
</ul>
<div id="ikelimas">
    <form action="upload.php?action=upload" method="post"
          enctype="multipart/form-data">
        <label for="file">Filename:</label>
        <input type="file" name="file" id="file" accept="image/*">

        <p></p><input type="submit" name="submit" value="'Įkelti'"></p>
    </form>
</div>
</body>
<script src="<?php echo JS; ?>jquery-1.10.2.min.js"></script>
<script src="<?php echo JS; ?>gallery.js"></script>
<html>
