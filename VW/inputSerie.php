<?php
ob_start();
session_start();
header('Content-type: text/html; charset=ISO-8895-1');
include_once "../Config.php";
include_once "../DB/conexaoSQL.php";
include_once "../DB/testLogin.php";

testLogin($conn);

ini_set('max_input_vars', 3000);
error_reporting(0);
ini_set('display_errors', '0');

$ret = $_GET['ret'];
if ($ret == '1') {
    $msgErr = 'Série ou patrimônio inexistente!';
} else {
    $msgErr = '';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATABIT</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/styleBtn.css">
</head>

<body>
    <form class="form-serie" method="get" action="index.php">
        <img src="../img/logo.jpg" alt="logo">

        <div class="form-group">
            <div class="div-serie">
                <label for="serie">Série/Pat *</label>
                <input id="serie" name="serie" autofocus required>
            </div>
        </div>

        <button type="submit" class="submit-btn">OK</button>       

        <!-- ERRO -->
        <?php if (!empty($msgErr)) { ?>
            <div style="
                margin-top: 15px;
                color: blue;
                font-size: 18px;
                text-align: center;
                width: 100%;
            ">
                <?= $msgErr ?>
            </div>
        <?php } ?>

    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../JS/script.js" charset="utf-8"></script>
</body>

</html>
