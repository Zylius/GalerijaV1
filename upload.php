<?php
const MAX_SIZE = 2097152; // 2 MB
const SAVAITE = 604800;
if ($_GET["action"] == "delete") {
    if (file_exists("upload/" . $_COOKIE["nuotrauka"])) {
        unlink("upload/" . $_COOKIE["nuotrauka"]);
        setcookie("nuotrauka", '', time() - 10); // nesaugu
        header('Location: /');
    }
    die();
}
if ($_GET["action"] != "upload" || !isset($_FILES["file"])) {
    die();
}
if (getimagesize($_FILES["file"]["tmp_name"]) && $_FILES["file"]["size"] < MAX_SIZE) {
    if ($_FILES["file"]["error"] > 0) {
        echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    } else {
        setcookie("nuotrauka", $_FILES["file"]["name"], time() + SAVAITE);
        if (file_exists("upload/" . $_COOKIE["nuotrauka"])) {
            unlink("upload/" . $_COOKIE["nuotrauka"]);
        }
        if (file_exists("upload/" . $_FILES["file"]["name"])) {
            unlink("upload/" . $_FILES["file"]["name"]);
        }
        move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
    }
    header('Location: /');
} else {
    header('Location: /?err=invalid_file');
}