<?php
include_once "../config.php";
include_once "testLogin.php";

 $tecLogado = tecnicoLogado();

/* Gera o proximo numero de OS */
$sql = "SELECT TOP 1
            FORMAT(TB00002_COD + 1, '000000') novaOS 
        FROM TB00002
        WHERE 
            TB00002_TABELA = 'TB02115'
    ";
$stmt = sqlsrv_query($conn, $sql);
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $novaOS = $row['novaOS'];
}

function gravaOS($conn, $estado, $local, $email, $contpb, $serie, $whatsapp, $solicitante, $defeito, $periodo)
{
    global $tecLogado, $novaOS;

    /* Trata o numero de caracteres que será inserido no campo TB02115_NOME */
    $motivo = substr($defeito, 0, 50);

    /* Trata o numero de caracteres que será inserido no campo TB02115_CELULAR */
    $whatsapp = substr($whatsapp, 0, 11);

    /* Trata o numero de caracteres que será inserido no campo TB02115_LOCAL */
    $local = substr($local, 0, 200);

    /* Trata o numero de caracteres que será inserido no campo TB02115_EMAIL */
    $email = substr($email, 0, 200);

    /* Trata o numero de caracteres que será inserido no campo TB02115_SOLICITANTE */
    $solicitante = substr($solicitante, 0, 30);

    /* Trata o numero de caracteres que será inserido no campo TB02115_SOLICITANTE */
    $contpb = substr($contpb, 0, 10);

    /* Verifica se e patrimonio ou serie antes de gravar */
    $sql = "SELECT TOP 1 
                TB02112_NUMSERIE NumSerie
            FROM TB02112
            WHERE TB02112_PAT = '$serie'
            AND TB02112_SITUACAO = 'A'
    ";
    $NumSerie = "";
    $stmt = sqlsrv_query($conn, $sql);
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $NumSerie = $row['NumSerie'];
    }

    if ($NumSerie != NULL || $NumSerie != '') {
        $serie = $NumSerie;
    } else {

    }

    /* Verifica situação */
    $sql = "SELECT TOP 1 
                TB02112_SITUACAO Situacao
            FROM TB02112
            WHERE TB02112_NUMSERIE = '$serie'
    ";
    $stmt = sqlsrv_query($conn, $sql);
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $Situacao = $row['Situacao'];
    }

    $sql = "INSERT INTO TB02115 ( 
                TB02115_CODIGO, 
                TB02115_DTCAD, 
                TB02115_CONTPB, 
                TB02115_NUMSERIE, 
                TB02115_STATUS, 
                TB02115_OPCAD,
                TB02115_CODCLI, 
                TB02115_TIPOINTERV, 
                TB02115_PRODUTO, 
                TB02115_CODTEC, 
                TB02115_ATENDENTE,
                TB02115_PREVENTIVA, 
                TB02115_DATA, 
                TB02115_SITUACAO, 
                TB02115_CODEMP, 
                TB02115_OBS, 
                TB02115_NOME,
                TB02115_CONTRATO, 
                TB02115_SOLICITANTE,
                TB02115_CEP,
                TB02115_END,
                TB02115_CIDADE,
                TB02115_BAIRRO,
                TB02115_NUM,
                TB02115_COMP,
                TB02115_ORIGEM, 
                TB02115_LOCAL
            )
            SELECT TOP 1
                /* 1: TB02115_CODIGO */ ?, 
                /* 2: TB02115_DTCAD */ GETDATE(), 
                /* 5: TB02115_CONTPB */ 0, 
                /* 6: TB02115_NUMSERIE */ TB02054_NUMSERIE, 
                /* 8: TB02115_STATUS */ '00', --pegar com o fabricio
                /* 9: TB02115_OPCAD */ 'PAINEL ABERTURA OS',
                /* 10: TB02115_CODCLI */ '00000000', 
                /* 11: TB02115_TIPOINTERV */ 'I', 
                /* 12: TB02115_PRODUTO */ TB02054_PRODUTO, 
                /* 13: TB02115_CODTEC */ ?,  -- será o tecnico vinculado na TB01066
                /* 14: TB02115_ATENDENTE */ 'PAINEL OS',
                /* 15: TB02115_PREVENTIVA */ 'E', 
                /* 16: TB02115_DATA */ GETDATE(), 
                /* 17: TB02115_SITUACAO */ 'A', 
                /* 18: TB02115_CODEMP */ ?, 
                /* 19: TB02115_OBS */ 'Os aberta por aplicação web que atualiza os equipamentos', 
                /* 20: TB02115_NOME */ '', 
                /* 21: TB02115_CONTRATO */ 'ESTOQUE', 
                /* 22: TB02115_SOLICITANTE */ 'APP Web AtualizaEquip',
                /* 23: TB02115_CEP */ TB00012_CEP, 
                /* 24: TB02115_END */ TB00012_END, 
                /* 25: TB02115_CIDADE */ TB00012_CIDADE,
                /* 26: TB02115_BAIRRO */ TB00012_BAIRRO, 
                /* 27: TB02115_NUM */ TB00012_NUM, 
                /* 28: TB02115_COMP */ CAST(TB00012_COMP AS VARCHAR(20)),
                /* 29: TB02115_ORIGEM */ 'E',
                /* 30: TB02115_LOCAL */ 'ESTOQUE'
            FROM TB02054
            LEFT JOIN TB00012 ON TB00012_CODIGO = TB02054_CODEMP
            WHERE TB02054_NUMSERIE = ?
            AND TB02054_QTPROD > TB02054_QTPRODS
        ";

    $stmt = sqlsrv_query($conn, $sql, array($novaOS, $tecLogado, '00', $serie));
    if ($stmt === false) {
        return [
            'success' => false,
            'message' => 'Erro ao gravar OS',
            'error' => sqlsrv_errors()
        ];
    } else if ($Situacao == 'I') {
        return [
            'success' => false,
            'message' => 'Equipamento inativo!',
            'error' => null
        ];
    } else {
        return [
            'success' => true,
            'message' => 'Sua OS foi aberta com sucesso!',
            'error' => null
        ];
    }

}

function gravaHistorico($conn, $numOS, $serie, $defeito, $statusInicial)
{
    /* Verifica se e patrimonio ou serie antes de gravar */
    $sql = "SELECT TOP 1 
                TB02112_NUMSERIE NumSerie
            FROM TB02112
            WHERE TB02112_PAT = '$serie'
            AND TB02112_SITUACAO = 'A'
    ";
    $stmt = sqlsrv_query($conn, $sql);
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $NumSerie = $row['NumSerie'];
    }

    if (isset($NumSerie)) {
        $serie = $NumSerie;
    } else {

    }

    $sql = "UPDATE TB00002
            SET TB00002_COD = '$numOS'
            WHERE TB00002_TABELA = 'TB02115'
    
            INSERT INTO TB02130
                (TB02130_CODIGO,
                TB02130_DATA, 
                TB02130_USER,
                TB02130_STATUS,
                TB02130_NOME,
                TB02130_OBS,
                TB02130_CODTEC,
                TB02130_PREVISAO,
                TB02130_NOMETEC,
                TB02130_TIPO, 
                TB02130_CODCAD,
                TB02130_CODEMP,
                TB02130_DATAEXEC,
                TB02130_HORASCOM,
                TB02130_HORASFIM)
            SELECT TOP 1
                '$numOS',
                GETDATE(),
                'APP ABERTURA_OS', 
                '$statusInicial', 
                TB01073_NOME, 
                '$defeito',
                TB02115_CODTEC,
                NULL,
                TB01024_NOME, 
                'O',
                TB02115_CODCLI,
                TB02115_CODEMP,
                GETDATE(), 
                '00:00', 
                '00:00'
            FROM TB02115
            LEFT JOIN TB01073 ON TB01073_CODIGO = TB02115_STATUS
            LEFT JOIN TB01024 ON TB01024_CODIGO = TB02115_CODTEC
            WHERE TB02115_NUMSERIE = '$serie'
            ORDER BY TB02115_DTCAD DESC";

    $stmt = sqlsrv_query($conn, $sql);
}