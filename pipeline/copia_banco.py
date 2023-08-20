import psycopg2

# Parâmetros de conexão
dbname = 'dbgfopag'
user = 'gfopag'
password = 'gfopag#123'
host = 'gfopag.ctruxpmyw7jm.us-east-2.rds.amazonaws.com'  # Endereço do seu banco na AWS
port = '5432'  # Porta padrão do PostgreSQL

# Conectando ao banco de dados
connection = psycopg2.connect(dbname=dbname, user=user, password=password, host=host, port=port)
cursor = connection.cursor()

# Truncar a tabela cliente e tabelas dependentes no data warehouse
cursor.execute("TRUNCATE datawarehouse.servico_comum, datawarehouse.servico_extra, datawarehouse.cliente CASCADE;")
connection.commit()

# Copiar a tabela cliente
cursor.execute("""
    INSERT INTO datawarehouse.cliente (id_cliente, nome)
    SELECT id_cliente, nome
    FROM relacional.cliente;
""")

# Truncar a tabela datas e tabelas dependentes no data warehouse
cursor.execute("TRUNCATE datawarehouse.empresa CASCADE;")
connection.commit()

# Copiar a tabela empresa
cursor.execute("""
    INSERT INTO datawarehouse.empresa (id_empresa, id_cliente, cod_empresa, nome)
    SELECT id_empresa, id_cliente, cod_empresa, nome
    FROM relacional.empresa;
""")

# Truncar a tabela datas e tabelas dependentes no data warehouse
cursor.execute("TRUNCATE datawarehouse.datas CASCADE;")
connection.commit()

# Copiar a tabela datas
cursor.execute("""
    INSERT INTO datawarehouse.datas (id, mes, ano)
    SELECT id, mes, ano
    FROM relacional.datas;
""")

# Truncar a tabela servico_comum e tabelas dependentes no data warehouse
cursor.execute("TRUNCATE datawarehouse.servico_comum CASCADE;")
connection.commit()

# Copiar a tabela servico_comum
cursor.execute("""
    INSERT INTO datawarehouse.servico_comum (cod_serv_comum, id_empresa, id_data, pro_labore, empregado, rpa, estagiario)
    SELECT cod_serv_comum, id_empresa, id_data, pro_labore, empregado, rpa, estagiario
    FROM relacional.servico_comum
    WHERE COALESCE(pro_labore, 0) > 0 OR COALESCE(empregado, 0) > 0 OR COALESCE(rpa, 0) > 0 OR COALESCE(estagiario, 0) > 0;
""")

# Truncar a tabela servico_extra e tabelas dependentes no data warehouse
cursor.execute("TRUNCATE datawarehouse.servico_extra CASCADE;")
connection.commit()

# Copiar a tabela servico_extra
cursor.execute("""
    INSERT INTO datawarehouse.servico_extra (cod_serv_extra, id_empresa, id_data, recibo_por_fora, recalculo_fgts, recalculo_inss, reabertura_de_folha, deslocamento_caixa_economica, dctfweb_sem_movimento, recalculo_grrf, recalculo_gps, sefip_sem_movimento, perd_comp)
    SELECT cod_serv_extra, id_empresa, id_data, recibo_por_fora, recalculo_fgts, recalculo_inss, reabertura_de_folha, deslocamento_caixa_economica, dctfweb_sem_movimento, recalculo_grrf, recalculo_gps, sefip_sem_movimento, perd_comp
    FROM relacional.servico_extra
    WHERE recibo_por_fora > 0 OR recalculo_fgts > 0 OR recalculo_inss > 0 OR reabertura_de_folha > 0 OR deslocamento_caixa_economica > 0 OR dctfweb_sem_movimento > 0 OR recalculo_grrf > 0 OR recalculo_gps > 0 OR sefip_sem_movimento > 0 OR perd_comp > 0;
""")

# Commit e fechamento da conexão
connection.commit()
cursor.close()
connection.close()

print("Cópia de dados concluída.")
