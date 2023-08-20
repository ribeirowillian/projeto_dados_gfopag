-- Table: relacional.cliente

-- DROP TABLE IF EXISTS relacional.cliente;

CREATE TABLE IF NOT EXISTS relacional.cliente
(
    id_cliente bigint NOT NULL,
    nome character varying(255) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT cliente_pkey PRIMARY KEY (id_cliente)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS relacional.cliente
    OWNER to gfopag;
	
-- Table: relacional.datas

-- DROP TABLE IF EXISTS relacional.datas;

CREATE TABLE IF NOT EXISTS relacional.datas
(
    id integer NOT NULL DEFAULT nextval('relacional.datas_id_seq'::regclass),
    mes integer NOT NULL,
    ano integer NOT NULL,
    CONSTRAINT datas_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS relacional.datas
    OWNER to gfopag;
	
-- Table: relacional.empresa

-- DROP TABLE IF EXISTS relacional.empresa;

CREATE TABLE IF NOT EXISTS relacional.empresa
(
    id_empresa integer NOT NULL DEFAULT nextval('relacional.empresa_id_empresa_seq'::regclass),
    id_cliente bigint NOT NULL,
    cod_empresa integer NOT NULL,
    nome character varying(150) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT empresa_pkey PRIMARY KEY (id_empresa),
    CONSTRAINT empresa_id_cliente_fkey FOREIGN KEY (id_cliente)
        REFERENCES relacional.cliente (id_cliente) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS relacional.empresa
    OWNER to gfopag;
	
-- Table: relacional.servico_comum

-- DROP TABLE IF EXISTS relacional.servico_comum;

CREATE TABLE IF NOT EXISTS relacional.servico_comum
(
    cod_serv_comum integer NOT NULL DEFAULT nextval('relacional.servico_comum_cod_serv_comum_seq'::regclass),
    id_empresa integer NOT NULL,
    id_data integer NOT NULL,
    pro_labore integer,
    empregado integer,
    rpa integer,
    estagiario integer,
    CONSTRAINT servico_comum_pkey PRIMARY KEY (cod_serv_comum),
    CONSTRAINT servico_comum_id_data_fkey FOREIGN KEY (id_data)
        REFERENCES relacional.datas (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT servico_comum_id_empresa_fkey FOREIGN KEY (id_empresa)
        REFERENCES relacional.empresa (id_empresa) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS relacional.servico_comum
    OWNER to gfopag;

-- Table: relacional.servico_extra

-- DROP TABLE IF EXISTS relacional.servico_extra;

CREATE TABLE IF NOT EXISTS relacional.servico_extra
(
    cod_serv_extra integer NOT NULL DEFAULT nextval('relacional.servico_extra_cod_serv_extra_seq'::regclass),
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
        REFERENCES relacional.datas (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT servico_extra_id_empresa_fkey FOREIGN KEY (id_empresa)
        REFERENCES relacional.empresa (id_empresa) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS relacional.servico_extra
    OWNER to gfopag;
