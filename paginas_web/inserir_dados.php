<?php
// Arquivo de configuração com informações de conexão com o banco de dados
require_once 'config.php';

// Função para se conectar ao banco de dados usando as informações do arquivo de configuração
function connect_to_database() {
    global $db_config;

    $dsn = "pgsql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['dbname']};user={$db_config['user']};password={$db_config['password']}";

    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Tratamento de erro ao se conectar ao banco de dados
        die("Erro de conexão: " . $e->getMessage());
    }
}

// Conectar ao banco de dados
$pdo = connect_to_database();

if (isset($_POST['submit_dados'])) {
    // Verificar a existência das chaves antes de acessá-las
    if (!isset($_POST['pro_labore']) || !isset($_POST['empregado']) || !isset($_POST['rpa']) || !isset($_POST['estagiario']) || !isset($_POST['id_empresa']) || !isset($_POST['data'])) {
        die("Erro: os campos pro_labore, empregado, rpa, estagiario, id_empresa e data devem estar presentes no formulário.");
    }

    // Recuperar os valores dos campos do formulário
    $data_id = $_POST['data'];
    $pro_labore = $_POST['pro_labore'];
    $empregado = $_POST['empregado'];
    $rpa = $_POST['rpa'];
    $estagiario = $_POST['estagiario'];
    $id_empresa = $_POST['id_empresa'];

    // Verificar se os campos estão vazios e definir como zero se necessário
    for ($i = 0; $i < count($pro_labore); $i++) {
        $pro_labore[$i] = empty($pro_labore[$i]) ? 0 : $pro_labore[$i];
        $empregado[$i] = empty($empregado[$i]) ? 0 : $empregado[$i];
        $rpa[$i] = empty($rpa[$i]) ? 0 : $rpa[$i];
        $estagiario[$i] = empty($estagiario[$i]) ? 0 : $estagiario[$i];
    }

    // Inserir os dados na tabela relacional.servico_comum
    try {
        $sql = "INSERT INTO relacional.servico_comum (id_data, id_empresa, pro_labore, empregado, rpa, estagiario)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        for ($i = 0; $i < count($pro_labore); $i++) {
            $stmt->execute([$data_id, $id_empresa[$i], $pro_labore[$i], $empregado[$i], $rpa[$i], $estagiario[$i]]);
        }

        echo "Dados inseridos com sucesso!";
    } catch (PDOException $e) {
        // Tratamento de erro ao executar consulta SQL
        die("Erro ao executar consulta SQL: " . $e->getMessage());
    }
}
?>
