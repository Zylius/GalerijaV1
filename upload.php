<?php
include_once("settings/db_config.php");
const MAX_SIZE = 4194304; // 4 MB
const SAVAITE = 604800;
if (!isset($_GET["action"])) {
    die();
}
$galerija_db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("Error " . mysqli_error($galerija_db));
if ($_GET["action"] == "delete" && isset($_POST["ID"])) {
    $find = glob('upload/' . $_POST["ID"] . '.*');
    if (!empty($find)) {
        unlink($find[0]);
        $query = "DELETE FROM `nuotraukos` where `ID` = {$_POST["ID"]}";
        mysqli_query($galerija_db, $query) or die ("Error " . mysqli_error($galerija_db));
        header('Location: ./?status=OK');
    }
    die();
}
if ($_GET["action"] != "upload" || !isset($_FILES["file"])) {
    header('Location: ./?status=wrong_link');
    die();
}
if (getimagesize($_FILES["file"]["tmp_name"])&& $_FILES["file"]["size"] < MAX_SIZE) {
    if ($_FILES["file"]["error"] > 0) {
        header('Location: /?status=err');
    } else {
        $pavadinimas = getrandID();
        $path = glob('upload/' . $pavadinimas . '.*');
        while (!empty($path)) {
            $pavadinimas = getrandID();
            $path = glob('upload/' . $pavadinimas . '.*');
        }
        $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        $query = "INSERT INTO `nuotraukos` (`ID`, `pavadinimas`, `aprasymas`, `ext`) VALUES ('$pavadinimas', '{$_FILES["file"]["name"]}', 'bla', '$ext')";
        move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $pavadinimas . "." . $ext);
        mysqli_query($galerija_db, $query) or die ("Error " . mysqli_error($galerija_db));
    }
    header('Location: ./?status=OK');
} else {
    header('Location: ./?status=err');
}
function getrandID()
{
    return time() * 1000 + rand(100, 999);
}