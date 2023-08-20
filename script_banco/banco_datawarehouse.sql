-- Table: datawarehouse.cliente

-- DROP TABLE IF EXISTS datawarehouse.cliente;

CREATE TABLE IF NOT EXISTS datawarehouse.cliente
(
    id_cliente bigint NOT NULL,
    nome character varying(255) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT cliente_pkey PRIMARY KEY (id_cliente)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS datawarehouse.cliente
    OWNER to gfopag;
	
-- Table: datawarehouse.datas

-- DROP TABLE IF EXISTS datawarehouse.datas;

CREATE TABLE IF NOT EXISTS datawarehouse.datas
(
    id integer NOT NULL DEFAULT nextval('datawarehouse.datas_id_seq'::regclass),
    mes integer NOT NULL,
    ano integer NOT NULL,
    CONSTRAINT datas_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS datawarehouse.datas
    OWNER to gfopag;
	
-- Table: datawarehouse.empresa

-- DROP TABLE IF EXISTS datawarehouse.empresa;

CREATE TABLE IF NOT EXISTS datawarehouse.empresa
(
    id_empresa integer NOT NULL,
    id_cliente bigint NOT NULL,
    cod_empresa integer NOT NULL,
    nome character varying(150) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT empresa_pkey PRIMARY KEY (id_empresa),
    CONSTRAINT empresa_id_cliente_fkey FOREIGN KEY (id_cliente)
        REFERENCES datawarehouse.cliente (id_cliente) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS datawarehouse.empresa
    OWNER to gfopag;

-- Table: datawarehouse.servico_comum

-- DROP TABLE IF EXISTS datawarehouse.servico_comum;

CREATE TABLE IF NOT EXISTS datawarehouse.servico_comum
(
    cod_serv_comum integer NOT NULL,
    id_empresa integer NOT NULL,
    id_data integer NOT NULL,
    pro_labore integer,
    empregado integer,
    rpa integer,
    estagiario integer,
    CONSTRAINT servico_comum_pkey PRIMARY KEY (cod_serv_comum),
    CONSTRAINT servico_comum_id_data_fkey FOREIGN KEY (id_data)
        REFERENCES datawarehouse.datas (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT servico_comum_id_empresa_fkey FOREIGN KEY (id_empresa)
        REFERENCES datawarehouse.empresa (id_empresa) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS datawarehouse.servico_comum
    OWNER to gfopag;
	
-- Table: datawarehouse.servico_extra

-- DROP TABLE IF EXISTS datawarehouse.servico_extra;

CREATE TABLE IF NOT EXISTS datawarehouse.servico_extra
(
    cod_serv_extra integer NOT NULL,
    id_empresa integer NOT NULL,
    id_data integer NOT NULL,
    recibo_por_fora integer,
    recalculo_fgts integer,
    recalculo_inss integer,
    reabertura_de_folha integer,
    deslocamento_caixa_economica integer,
    dctfweb_sem_movimento integer,
    recalculo_grrf integer,
    recalculo_gps integer,
    sefip_sem_movimento integer,
    perd_comp integer,
    CONSTRAINT servico_extra_pkey PRIMARY KEY (cod_serv_extra),
    CONSTRAINT servico_extra_id_data_fkey FOREIGN KEY (id_data)
        REFERENCES datawarehouse.datas (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT servico_extra_id_empresa_fkey FOREIGN KEY (id_empresa)
        REFERENCES datawarehouse.empresa (id_empresa) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS datawarehouse.servico_extra
    OWNER to gfopag;
	
-- Table: datawarehouse.valores

-- DROP TABLE IF EXISTS datawarehouse.valores;

CREATE TABLE IF NOT EXISTS datawarehouse.valores
(
    id_cliente integer NOT NULL,
    valor numeric,
    CONSTRAINT valores_id_cliente_fkey FOREIGN KEY (id_cliente)
        REFERENCES datawarehouse.cliente (id_cliente) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS datawarehouse.valores
    OWNER to gfopag;