<!DOCTYPE html>
<html>
<head>
    <style>
    .results-container {
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .company-row {
        display: flex;
        align-items: center;
        padding: 10px;
        border: 1px solid #ccc;
    }

    .company-name {
        flex-basis: 30%;
        font-weight: bold;
    }

    .column-common {
        flex-basis: 15%;
        text-align: center;
    }

    .columns-container-common {
        display: flex;
    }

    .column-name-common {
        font-weight: bold;
    }

    .column-value-common {
        margin-bottom: 5px;
    }

    .columns-container-extra {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .column-extra {
        flex-basis: 50%;
        text-align: center;
    }

    .column-name-extra {
        font-weight: bold;
    }

    .column-value-extra {
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        padding: 10px;
        border: 1px solid #ccc;
    }
</style>

</head>
<body>
    <div class="container">
        <h1>Relação de Seviços Mesais</h1>

        <?php
        require_once 'config.php';

        $conn = pg_connect("host={$db_config['host']} port={$db_config['port']} dbname={$db_config['dbname']} user={$db_config['user']} password={$db_config['password']}");

        if ($conn) {
            function getEmpresas($conn, $idCliente) {
                $sql = "SELECT id_empresa, nome FROM datawarehouse.empresa WHERE id_cliente = $idCliente";
                $result = pg_query($conn, $sql);
                $empresas = pg_fetch_all($result);
                return $empresas;
            }
			function getServicosComuns($conn, $idEmpresa, $idData) {
                $sql = "SELECT * FROM datawarehouse.servico_comum WHERE id_empresa = $idEmpresa AND id_data = $idData";
                $result = pg_query($conn, $sql);
                $servicosComuns = pg_fetch_assoc($result);
                return $servicosComuns;
            }

            function getServicosExtras($conn, $idEmpresa, $idData) {
                $sql = "SELECT * FROM datawarehouse.servico_extra WHERE id_empresa = $idEmpresa AND id_data = $idData";
                $result = pg_query($conn, $sql);
                $servicosExtras = pg_fetch_assoc($result);
                return $servicosExtras;
            }
            $clienteId = $_POST['cliente'];
            $selectedDate = $_POST['data'];

            $selectedYear = substr($selectedDate, 0, 4);
            $selectedMonth = substr($selectedDate, 5, 2);

            // Buscar o id da data selecionada
            $sqlDate = "SELECT id FROM datawarehouse.datas WHERE mes = $selectedMonth AND ano = $selectedYear";
            $resultDate = pg_query($conn, $sqlDate);
            $dateRow = pg_fetch_assoc($resultDate);
            $idData = $dateRow['id'];

            // Buscar empresas do cliente selecionado
            $empresas = getEmpresas($conn, $clienteId);
            
            echo '<div class="results-container">';

            // Cabeçalho da tabela servico_comum
            echo '<div class="company-row">';
            echo '<div class="company-name">Empresa</div>';
            $servicosComuns = getServicosComuns($conn, $empresas[0]['id_empresa'], $idData);

            // Colunas para serviços comuns
            foreach ($servicosComuns as $coluna => $valor) {
                if ($coluna !== 'id_serv_comum' && $coluna !== 'id_empresa' && $coluna !== 'id_data' && $coluna !== 'cod_serv_comum') {
                    echo '<div class="column-common">';
                    echo '<div class="column-name-common">' . $coluna . '</div>';
                    echo '</div>';
                }
            }

            // Coluna Total
            echo '<div class="column-common">';
            echo '<div class="column-name-common">Total</div>';
            echo '</div>';

            // Coluna Valor
            echo '<div class="column-common">';
            echo '<div class="column-name-common">Valor</div>';
            echo '</div>';

            echo '</div>';

            $valorTotalServicosComuns = 0; // Inicializa a variável para armazenar o valor total dos serviços comuns

            foreach ($empresas as $empresa) {
                echo '<div class="company-row">';
                echo '<div class="company-name">' . $empresa['nome'] . '</div>';

                $servicosComuns = getServicosComuns($conn, $empresa['id_empresa'], $idData);
                $somaTotal = 0; // Variável para armazenar o total

                foreach ($servicosComuns as $coluna => $valor) {
                    if ($coluna !== 'id_serv_comum' && $coluna !== 'id_empresa' && $coluna !== 'id_data' && $coluna !== 'cod_serv_comum') {
                        echo '<div class="column-common">';
                        echo '<div class="column-value-common">' . $valor . '</div>';
                        echo '</div>';

                        // Somar os valores das colunas dos serviços comuns
                        $somaTotal += $valor;
                    }
                }

                // Exibir a soma total dos serviços comuns para a empresa
                echo '<div class="column-common">';
                echo '<div class="column-value-common">' . $somaTotal . '</div>';
                echo '</div>';

                // Buscar o valor da tabela "valores" relacionado ao cliente
                $sqlValor = "SELECT valor FROM datawarehouse.valores WHERE id_cliente = {$clienteId}";
                $resultValor = pg_query($conn, $sqlValor);
                $rowValor = pg_fetch_assoc($resultValor);

                if ($rowValor && isset($rowValor['valor'])) {
                    $valorCliente = $rowValor['valor'];

                    // Calcular o valor multiplicado
                    $valorMultiplicado = $valorCliente * $somaTotal;

                    // Exibir o valor multiplicado na coluna "Valor"
                    echo '<div class="column-common">';
                    echo '<div class="column-value-common">' . number_format($valorMultiplicado, 2, '.', ',') . '</div>';
                    echo '</div>';

                    // Adicionar ao valor total dos serviços comuns
                    $valorTotalServicosComuns += $valorMultiplicado;
                } else {
                    // Caso não haja valor na tabela "valores", exibir coluna "Valor" vazia
                    echo '<div class="column-common">';
                    echo '<div class="column-value-common">0.00</div>'; // Define um valor padrão caso não haja valor na tabela
                    echo '</div>';
                }

                echo '</div>'; // Fechamento da div company-row
            }

            // Exibir a linha com o valor total dos serviços comuns de todas as empresas
            echo '<div class="company-row">';
            echo '<div class="company-name text-center" style="flex-basis: 30%;">Total Somado</div>';
            echo '<div class="columns-container-common">';
            echo '<div class="column-common">';
            echo '<div class="column-value-common" style="text-align: right;">' . number_format($valorTotalServicosComuns, 2, '.', ',') . '</div>'; // Alinha à direita
            echo '</div>';
            echo '</div>';
            echo '</div>';           
            echo '</div>'; // Fechamento da div results-container

            // Bloco de empresas com serviços extras
            echo '<div class="results-container">';
            echo '<h2>Serviços Extras</h2>';


            // Dentro do loop de empresas para serviços extras
            $somaTotalServicosExtras = 0;
            foreach ($empresas as $empresa) {
                $servicosExtras = getServicosExtras($conn, $empresa['id_empresa'], $idData);

                if ($servicosExtras) {
                    echo '<div class="company-row">';
                    echo '<div class="company-name">' . $empresa['nome'] . '</div>';
                        
                    echo '<div class="columns-container-extra">';
                        
                    // Coluna "Nome do Serviço"
                    echo '<div class="column-extra">';
                    echo '<div class="column-name-extra">Nome Serviço</div>';
                    foreach ($servicosExtras as $coluna => $valor) {
                        if ($coluna !== 'id_serv_extra' && $coluna !== 'id_empresa' && $coluna !== 'id_data' && $coluna !== 'cod_serv_extra' && $valor > 0) {
                            echo '<div class="column-value-extra">' . $coluna . '</div>';
                        }
                    }
                    echo '</div>';
                        
                    // Coluna "Quantidade"
                    echo '<div class="column-extra">';
                    echo '<div class="column-name-extra">Quantidade</div>';
                    foreach ($servicosExtras as $coluna => $valor) {
                        if ($coluna !== 'id_serv_extra' && $coluna !== 'id_empresa' && $coluna !== 'id_data' && $coluna !== 'cod_serv_extra' && $valor > 0) {
                            echo '<div class="column-value-extra">' . $valor . '</div>';
                        }
                    }
                    echo '</div>';
                        
                    // Coluna "Valor"
                    echo '<div class="column-extra">';
                    echo '<div class="column-name-extra">Valor</div>';
                    foreach ($servicosExtras as $coluna => $valor) {
                        if ($coluna !== 'id_serv_extra' && $coluna !== 'id_empresa' && $coluna !== 'id_data' && $coluna !== 'cod_serv_extra' && $valor > 0) {
                            $sqlValor = "SELECT valor FROM datawarehouse.valores WHERE id_cliente = $clienteId";
                            $resultValor = pg_query($conn, $sqlValor);
                            if ($resultValor) {
                                $rowValor = pg_fetch_assoc($resultValor);
                        
                                if ($rowValor && isset($rowValor['valor'])) {
                                    $valorCliente = $rowValor['valor'];
                        
                                    // Converta a quantidade para um valor numérico
                                    $quantidade = (int) $valor;
                        
                                    // Calcular o valor multiplicado diretamente
                                    $valorMultiplicado = $valorCliente * $quantidade;
                        
                                    // Exiba o valor multiplicado na coluna "Valor"
                                    echo '<div class="column-value-extra">' . number_format($valorMultiplicado, 2, '.', ',') . '</div>';
                                    
                                    // Calcula a soma total dos valores dos serviços extras
                                    $somaTotalServicosExtras += $valorMultiplicado;
                                } else {
                                    // Caso não haja valor na tabela "valores", exibir coluna "Valor" vazia
                                    echo '<div class="column-value-extra"></div>';
                                }
                            } else {
                                // Caso haja um erro na consulta SQL
                                echo '<div class="column-value-extra"></div>';
                            }
                        }
                    }
                    echo '</div>';
                        
                    echo '</div>';
                    echo '</div>';
                }
            }

            // Mostrar a linha de Total Geral para serviços extras
            echo '<div class="company-row">';
            echo '<div class="company-name text-center" style="flex-basis: 30%;">Total Somado</div>';
            echo '<div class="columns-container-extra">';
            echo '<div class="column-extra">';
            echo '<div class="column-name-extra"></div>';
            echo '</div>';
            echo '<div class="column-extra">';
            echo '<div class="column-name-extra"></div>';
            echo '</div>';
            echo '<div class="column-extra">';
            echo '<div class="column-name-extra text-right">' . number_format($somaTotalServicosExtras, 2, '.', ',') . '</div>'; // Soma total alinhada à direita
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';           
            echo '</div>'; // Fechamento da div results-container
            // Calcular o total geral somando os totais somados de serviços comuns e extras
            $totalGeral = $valorTotalServicosComuns + $somaTotalServicosExtras;

            // Mostrar a linha de Total Geral
            echo '<div class="company-row">';
            echo '<div class="company-name text-center" style="flex-basis: 30%;">Total Geral</div>';
            echo '<div class="columns-container-extra">';
            echo '<div class="column-extra">';
            echo '<div class="column-name-extra text-right"></div>';
            echo '</div>';
            echo '<div class="column-extra">';
            echo '<div class="column-name-extra text-right"></div>';
            echo '</div>';
            echo '<div class="column-extra">';
            echo '<div class="column-name-extra text-right">' . number_format($totalGeral, 2, '.', ',') . '</div>'; // Total geral alinhado à direita
            echo '</div>';
            echo '</div>';
            echo '</div>';
           
        } else {
            echo "Erro na conexão com o banco de dados.";
        }

        pg_close($conn);
        ?>
    </div>
</body>
</html>
