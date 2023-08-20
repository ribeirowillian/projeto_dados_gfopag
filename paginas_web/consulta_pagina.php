
<!DOCTYPE html>
<html>
<head>
    <title>Seleção de Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 30px;
        }
        label, select, button {
            display: block;
            margin-bottom: 10px;
        }
        select, button {
            width: 100%;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="text"] {
            width: 80px;
            padding: 5px;
        }
        .submit-button {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
        /* Ajuste para evitar quebra de linha no nome da empresa */
        .empresa-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <h1>Selecione os Dados</h1>

    <?php
    require_once 'config.php';

    $conn = pg_connect("host={$db_config['host']} port={$db_config['port']} dbname={$db_config['dbname']} user={$db_config['user']} password={$db_config['password']}");

    if ($conn) {
        function getClientes($conn) {
            $sql = "SELECT id_cliente, nome FROM datawarehouse.cliente";
            $result = pg_query($conn, $sql);
            $clientes = pg_fetch_all($result);
            return $clientes;
        }

        $clientes = getClientes($conn);

        echo '<form action="result.php" method="post">';
        echo '<label for="cliente">Selecione um Cliente:</label>';
        echo '<select name="cliente" id="cliente">';
        foreach ($clientes as $cliente) {
            echo '<option value="' . $cliente['id_cliente'] . '">' . $cliente['nome'] . '</option>';
        }
        echo '</select><br>';

        // Buscar datas da tabela datas
        $sqlDatas = "SELECT DISTINCT mes, ano FROM datawarehouse.datas";
        $resultDatas = pg_query($conn, $sqlDatas);
        $datas = pg_fetch_all($resultDatas);

        echo '<label for="data">Selecione uma Data:</label>';
        echo '<select name="data" id="data">';
        foreach ($datas as $data) {
            echo '<option value="' . $data['ano'] . '-' . str_pad($data['mes'], 2, '0', STR_PAD_LEFT) . '">' . $data['mes'] . '/' . $data['ano'] . '</option>';
        }
        echo '</select><br>';

        echo '<button type="submit" class="submit-button">Buscar</button>';

        echo '</form>';
    } else {
        echo "Erro na conexão com o banco de dados.";
    }

    pg_close($conn);
    ?>
</body>
</html>
