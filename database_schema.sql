-- Criação do banco de dados
DROP DATABASE IF EXISTS db_sistema_cupons;
CREATE DATABASE db_sistema_cupons DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_sistema_cupons;

-- Tabela CATEGORIA
CREATE TABLE CATEGORIA (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nom_categoria VARCHAR(25) NOT NULL
) ENGINE=InnoDB;

-- Tabela ASSOCIADO (CPF como VARCHAR para manter zeros)
CREATE TABLE ASSOCIADO (
    cpf_associado VARCHAR(11) PRIMARY KEY,
    nom_associado VARCHAR(40) NOT NULL,
    dtn_associado DATE NOT NULL,
    end_associado VARCHAR(30),
    bai_associado VARCHAR(30),
    cep_associado VARCHAR(8),
    cid_associado VARCHAR(40),
    uf_associado CHAR(2),
    cel_associado VARCHAR(15),
    email_associado VARCHAR(50) NOT NULL UNIQUE,
    sen_associado VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Tabela COMERCIO (CNPJ como VARCHAR para manter zeros)
CREATE TABLE COMERCIO (
    cnpj_comercio VARCHAR(14) PRIMARY KEY,
    id_categoria INT NOT NULL,
    raz_social_comercio VARCHAR(50) NOT NULL,
    nom_fantasia_comercio VARCHAR(30),
    end_comercio VARCHAR(30),
    bai_comercio VARCHAR(30),
    cep_comercio VARCHAR(8),
    cid_comercio VARCHAR(40),
    uf_comercio CHAR(2),
    con_comercio VARCHAR(15),
    email_comercio VARCHAR(50) NOT NULL UNIQUE,
    sen_comercio VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_categoria) REFERENCES CATEGORIA(id_categoria)
) ENGINE=InnoDB;

-- Tabela CUPOM
CREATE TABLE CUPOM (
    num_cupom CHAR(12) PRIMARY KEY,
    tit_cupom VARCHAR(25) NOT NULL,
    cnpj_comercio VARCHAR(14) NOT NULL,
    dta_emissao_cupom DATE NOT NULL,
    dta_inicio_cupom DATE NOT NULL,
    dta_termino_cupom DATE NOT NULL,
    per_desc_cupom NUMERIC(5,2) NOT NULL,
    FOREIGN KEY (cnpj_comercio) REFERENCES COMERCIO(cnpj_comercio)
) ENGINE=InnoDB;

-- Tabela CUPOM_ASSOCIADO (Reservas)
CREATE TABLE CUPOM_ASSOCIADO (
    id_cupom_associado INT PRIMARY KEY AUTO_INCREMENT,
    num_cupom CHAR(12) NOT NULL,
    cpf_associado VARCHAR(11) NOT NULL,
    dta_cupom_associado DATE NOT NULL,
    dta_uso_cupom_associado DATE NULL,
    FOREIGN KEY (num_cupom) REFERENCES CUPOM(num_cupom),
    FOREIGN KEY (cpf_associado) REFERENCES ASSOCIADO(cpf_associado),
    UNIQUE KEY unique_reserva (num_cupom, cpf_associado)
) ENGINE=InnoDB;

-- Inserção de categorias padrão
INSERT INTO CATEGORIA (nom_categoria) VALUES 
('Alimentação'),('Vestuário'),('Serviços'),('Saúde'),('Educação'),
('Lazer'),('Beleza'),('Automotivo'),('Tecnologia'),('Outros');
