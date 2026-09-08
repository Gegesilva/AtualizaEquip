<?php
ob_start();
session_start();
header('Content-type: text/html; charset=ISO-8895-1');
include_once "../DB/conexaoSQL.php";
include_once "../DB/dados.php";
include_once "../Config.php";
include_once "../DB/testLogin.php";

testLogin($conn);

ini_set('max_input_vars', 3000);
error_reporting(0);
ini_set('display_errors', '0');

$serie = $_GET["serie"];

list($estado, $Cliente, $Local, $UltCont, $Email, $Serie, $Tel, $CodEmp) = preenchimento($conn, $serie);


if (indentificaProd($conn, $serie) != '1') {
    header("Location: ../VW/inputSerie.php?ret=1");
    return;
}

echo $CodEmp;
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
    <div class="div-save" id="div-save"></div>
    <div class="div-form">
        <form method="POST" class="form-geral" id="form-geral-req">
            <img src="../img/logo.jpg" alt="logo">
            <div class="btn-solic-btn">
            </div>
            <h1 class="titulos">ATUALIZAÇÃO DE EQUIPAMENTOS</h1>
            <div class="buttons-forms">
                <button class="btn-req" id="btn-req"
                    onClick="window.location='<?= $url ?>/index.php?serie=<?= $serie ?>';" type="submit"
                    class="voltar-btn-form">Abertura OS</button>
                <button class="btn-req" id="btn-req-sup" style="color: black; opacity: 0.4;"
                    onClick="window.location='<?= $url ?>/req.php?serie=<?= $serie ?>';" type="submit"
                    class="voltar-btn-form">Requisição</button>
            </div>
            <h6 class="msg-os"><? /* PegaTipo($conn, $serie) */ ?></h6>
            <div class="form-group">
                <div class="form-input">
                    <label for="serie">Série/Pat *</label>
                    <input id="serie" name="serie" rows="4" placeholder="<?= $serie; ?>" value="<?= $serie ?>" required
                        readonly></input>
                </div>
            </div>
            <div class="form-group">
                <div class="form-input">
                    <label for="cliente">Nome do Cliente</label>
                    <input type="text" id="cliente" name="cliente" placeholder="<?= $Cliente; ?>"
                        value="<?= $Cliente; ?>" readonly>
                </div>
            </div>
           
            <div class="form-group">
                <div class="form-input">
                    <label for="defeito">Observação*</label>
                    <textarea id="defeito" name="defeito" rows="4" required></textarea>
                </div>
            </div>
            <div class="btn-index">
                <input type="hidden" name="trava" id="trava" value="1">
                <button type="submit" class="submit-btn">Enviar</button>
                <!-- <button onClick="window.location='<? ?>/inputSerie.php';"
                    type="submit" class="voltar-btn-form">Voltar</button> -->
            </div>
            <input type="hidden" id="urlReq" value="<?= $url ?>/savereq.php">
            <input type="hidden" name="codEmp" id="codEmp" value="<?= $CodEmp ?>">
        </form>
        <?php
        /* gera um codigo de 6 numeros pseudo aleatorio */

        //echo 'A'.sprintf("%'.05d\n",  mt_rand(0, 0xF00));
        ?>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../JS/script.js" charset="utf-8"></script>
</body>

</html>
