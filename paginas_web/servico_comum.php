<!DOCTYPE html>
<html>
<head>
    <title>Selecionar Cliente e Empresas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 1200px;
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
    <?php
    require_once 'config.php';

    function connect_to_database() {
        global $db_config;

        $dsn = "pgsql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['dbname']};user={$db_config['user']};password={$db_config['password']}";

        try {
            $pdo = new PDO($dsn);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    $pdo = connect_to_database();
    ?>
</head>
<body>
    <div class="container">
        <h1>SERVIÇO COMUM</h1>
        <form method="post" action="">
            <label for="cliente">Selecione o Cliente:</label>
            <select id="cliente" name="cliente">
                <?php
                try {
                    $sql = "SELECT id_cliente, nome FROM relacional.cliente";
                    $result = $pdo->query($sql);

                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $row["id_cliente"] . "'>" . $row["nome"] . "</option>";
                    }
                } catch (PDOException $e) {
                    die("Erro ao executar consulta SQL: " . $e->getMessage());
                }
                ?>
            </select>
            <button class="submit-button" type="submit" name="submit_cliente">Selecionar</button>
        </form>

        <?php
        if (isset($_POST['submit_cliente'])) {
            $cliente_id = $_POST['cliente'];
    
            try {
                $sql = "SELECT c.id_cliente, e.cod_empresa, e.id_empresa, e.nome
                        FROM relacional.cliente c
                        JOIN relacional.empresa e ON c.id_cliente = e.id_cliente
                        WHERE c.id_cliente = ?";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$cliente_id]);
    
                echo "<h2>Empresas Relacionadas:</h2>";
                echo "<form method='post' action='inserir_dados.php'>";
                echo "<table>";               
                echo "<tr><th>COD</th><th>NOME</th><th>Pró Labore</th><th>Empregado</th><th>RPA</th><th>Estagiário</th></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr><td>" . $row["cod_empresa"] . "</td><td class='empresa-name'>" . $row["nome"] . "</td>";
                    echo "<td><input type='text' name='pro_labore[]'></td>";
                    echo "<td><input type='text' name='empregado[]'></td>";
                    echo "<td><input type='text' name='rpa[]'></td>";
                    echo "<td><input type='text' name='estagiario[]'></td></tr>";
                    echo "<input type='hidden' name='id_empresa[]' value='" . $row["id_empresa"] . "'>";
                }
                echo "</table>";
                
                echo "<label for='data'>Data de Fechamento:</label>";
                echo "<select id='data' name='data'>";
                try {
                    $sql = "SELECT id, mes, ano FROM relacional.datas"; // Usar 'id' ao invés de 'id_data'
                    $result = $pdo->query($sql);
    
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $row["id"] . "'>" . $row["mes"] . "/" . $row["ano"] . "</option>";
                    }
                } catch (PDOException $e) {
                    die("Erro ao executar consulta SQL: " . $e->getMessage());
                }
                echo "</select>";
                echo "<button class='submit-button' type='submit' name='submit_dados'>Enviar Dados</button>";
                echo "</form>";
            } catch (PDOException $e) {
                die("Erro ao executar consulta SQL: " . $e->getMessage());
            }
        }
        ?>
    </div>
</body>
</html>
