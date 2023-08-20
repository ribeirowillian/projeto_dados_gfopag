<!DOCTYPE html>
    <html>
    <head>
        <title>Selecionar Cliente e Empresas</title>
        <style>
            /* Adicione o estilo aqui */
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f0f0f0;
            }
            .page-container {
                max-width: 2000px;
                margin: 0 auto;
                padding: 20px;
                background-color: #fff;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .title {
                text-align: center;
                margin-bottom: 20px;
            }
            .form-container {
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
            .empresa-column {
                float: left;
                width: 50%;
                padding: 10px;
            }
            .button-container {
                text-align: center;
                margin-top: 20px;
            }
            .empresa-name {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        </style>
    </head>
    <body>
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

    // Buscar clientes
    try {
        $sql_clientes = "SELECT id_cliente, nome FROM relacional.cliente";
        $stmt_clientes = $pdo->query($sql_clientes);
        $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao executar consulta SQL: " . $e->getMessage());
    }

    if (isset($_POST['submit_cliente'])) {
        $cliente_selecionado = $_POST['cliente_selecionado'];
        // Buscar empresas do cliente selecionado
        try {
            $sql_empresas_servicos = "SELECT e.id_empresa, e.nome AS nome_empresa 
                FROM relacional.empresa e
                WHERE e.id_cliente = ?";
            $stmt_empresas_servicos = $pdo->prepare($sql_empresas_servicos);
            $stmt_empresas_servicos->execute([$cliente_selecionado]);
            $empresas_servicos = $stmt_empresas_servicos->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro ao executar consulta SQL: " . $e->getMessage());
        }

        // Buscar serviços extras disponíveis
        try {
            $sql_servicos = "SELECT column_name FROM information_schema.columns WHERE table_name = 'servico_extra' AND column_name NOT IN ('cod_serv_extra', 'id_empresa', 'id_data')";
            $stmt_servicos = $pdo->query($sql_servicos);
            $servicos = $stmt_servicos->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            die("Erro ao executar consulta SQL: " . $e->getMessage());
        }
    }

    // Buscar datas disponíveis
    try {
        $sql_datas = "SELECT id, mes, ano FROM relacional.datas";
        $stmt_datas = $pdo->query($sql_datas);
        $datas = $stmt_datas->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao executar consulta SQL: " . $e->getMessage());
    }
    ?>
    </style>
            <div class="page-container">
            <h1 class="title">SERVIÇOS EXTRAS</h1>

            <div class="form-container">
                <form method="post" action="">
                    <label for="cliente">Selecione o Cliente:</label>
                    <select id="cliente" name="cliente_selecionado">
                        <?php
                        foreach ($clientes as $cliente) {
                            echo "<option value='{$cliente['id_cliente']}'>{$cliente['nome']}</option>";
                        }
                        ?>
                    </select>
                    <button class="submit-button" type="submit" name="submit_cliente">Selecionar</button>
                </form>
                <?php if (isset($empresas_servicos)): ?>
                    <form method="post" action="inserir_dados_extras.php">
                        <div class="empresa-column">
                            <?php $halfCount = ceil(count($empresas_servicos) / 2); ?>
                            <?php for ($i = 0; $i < $halfCount; $i++): ?>
                                <?php $empresa = $empresas_servicos[$i]; ?>
                                <h2 class="empresa-name">Empresa: <?php echo $empresa['nome_empresa']; ?></h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Serviço Extra</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($servicos as $servico): ?>
                                            <tr>
                                                <td><?php echo $servico; ?></td>
                                                <td>
                                                    <input type="text" name="valores[<?php echo $empresa['id_empresa']; ?>][<?php echo $servico; ?>]" placeholder="Valor">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endfor; ?>
                        </div>
                        <div class="empresa-column">
                            <?php for ($i = $halfCount; $i < count($empresas_servicos); $i++): ?>
                                <?php $empresa = $empresas_servicos[$i]; ?>
                                <h2 class="empresa-name">Empresa: <?php echo $empresa['nome_empresa']; ?></h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Serviço Extra</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($servicos as $servico): ?>
                                            <tr>
                                                <td><?php echo $servico; ?></td>
                                                <td>
                                                    <input type="text" name="valores[<?php echo $empresa['id_empresa']; ?>][<?php echo $servico; ?>]" placeholder="Valor">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endfor; ?>
                        </div>
                        <div style="clear: both;"></div>

                        <h2>Selecione a Data:</h2>
                        <select name="data_selecionada">
                            <?php foreach ($datas as $data): ?>
                                <option value="<?php echo $data['id']; ?>"><?php echo $data['mes'] . "/" . $data['ano']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <br>
                        <div class="button-container">
                            <button class="submit-button" type="submit" name="submit_servicos">Enviar Serviços Extras</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
</body>
</html>
