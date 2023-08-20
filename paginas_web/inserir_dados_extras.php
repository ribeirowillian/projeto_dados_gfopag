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

if (isset($_POST['submit_servicos'])) {
    $data_selecionada = $_POST['data_selecionada'];
    $valores = $_POST['valores'];

    try {
        foreach ($valores as $id_empresa => $servicos) {
            // Tratar valores vazios como 0
            $servicos = array_map(function ($valor) {
                return $valor === '' ? 0 : $valor;
            }, $servicos);

            // Verificar se os valores são numéricos
            if (array_sum(array_map('is_numeric', $servicos)) !== count($servicos)) {
                die("Erro: Insira apenas valores numéricos.");
            }

            // Monta a string com os nomes das colunas e os placeholders
            $colunas = implode(', ', array_keys($servicos));
            $placeholders = implode(', ', array_fill(0, count($servicos), '?'));

            // Monta o SQL de inserção com base nas colunas
            $sql_inserir = "INSERT INTO relacional.servico_extra (id_empresa, id_data, $colunas) VALUES (?, ?, $placeholders)";
            $params = array_merge([$id_empresa, $data_selecionada], array_values($servicos));

            $stmt_inserir = $pdo->prepare($sql_inserir);
            $stmt_inserir->execute($params);
        }

        echo "Dados inseridos com sucesso!";
    } catch (PDOException $e) {
        die("Erro ao inserir dados: " . $e->getMessage());
    }
}
?>
