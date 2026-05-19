CREATE DATABASE oficina;

USE oficina;

CREATE TABLE enderecos(
	ID_endereco INT PRIMARY KEY AUTO_INCREMENT,
	rua VARCHAR(100) NOT NULL,
	numero VARCHAR(100) NOT NULL,
	bairro VARCHAR(100) NOT NULL,
	cidade VARCHAR(100) NOT NULL,
	estado VARCHAR(100) NOT NULL
);

CREATE TABLE clientes(
	ID_cliente INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL,
	documento VARCHAR(20) NOT NULL,
	id_endereco INT,
    perfil ENUM('administrador', 'gerencia', 'usuario_comum') NOT NULL DEFAULT 'usuario_comum',
    senha VARCHAR(255) NOT NULL,
	FOREIGN KEY(id_endereco)
	REFERENCES enderecos(ID_endereco)
);

CREATE TABLE contato(
	ID_contato INT PRIMARY KEY AUTO_INCREMENT,
	contato VARCHAR(11) NOT NULL,
	id_cliente INT,
	FOREIGN KEY(id_cliente)
	REFERENCES clientes(ID_cliente)
);

CREATE TABLE marcas(
	ID_marca INT PRIMARY KEY AUTO_INCREMENT,
	nome_marca VARCHAR(30)
);

CREATE TABLE modelos(
	ID_modelo INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(50) NOT NULL,
	id_marca INT,
	FOREIGN KEY(id_marca)
	REFERENCES marcas(ID_marca)
);

CREATE TABLE veiculos(
	ID_veiculo INT PRIMARY KEY AUTO_INCREMENT,
	placa VARCHAR(7) NOT NULL,
	ano INT(4) NOT NULL,
	id_cliente INT,
	id_modelo INT, 
	FOREIGN KEY(id_cliente)
	REFERENCES clientes(ID_cliente),
	FOREIGN KEY(id_modelo)
	REFERENCES modelos(ID_modelo)
);

CREATE TABLE mecanicos(
	ID_mecanico INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL,
	especialidade VARCHAR(50) NOT NULL,
	disponibilidade BOOLEAN NOT NULL
);

CREATE TABLE ordens_servicos(
	ID_os INT PRIMARY KEY AUTO_INCREMENT,
	status VARCHAR(20) NOT NULL,
	data_abertura TIMESTAMP NOT NULL,
	data_entrega_prevista DATE,
	garantia_meses INT(7),
	id_veiculo INT,
	id_mecanico INT,
	FOREIGN KEY(id_veiculo)
	REFERENCES veiculos(ID_veiculo),
	FOREIGN KEY(id_mecanico)
	REFERENCES mecanicos(ID_mecanico)
);

CREATE TABLE pecas(
	ID_peca INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL,
	descricao TEXT(500) NOT NULL,
	preco_unitario DECIMAL(10,2) NOT NULL,
	nivel_estoque INT(5) NOT NULL
);

CREATE TABLE itens_os_pecas(
	ID_os INT NOT NULL,
	ID_peca INT NOT NULL,
	quantidade INT(50) NOT NULL,
	preco_venda DECIMAL(10,2) NOT NULL,
	PRIMARY KEY(ID_os, ID_peca),
	FOREIGN KEY(ID_os) 
	REFERENCES ordens_servicos(ID_os),
	FOREIGN KEY(ID_peca)
	REFERENCES pecas(ID_peca)
);

CREATE TABLE servicos_catalagos(
	ID_servico_ref INT PRIMARY KEY AUTO_INCREMENT,
	descricao VARCHAR(205) NOT NULL,
	preco_base DECIMAL(10,2) NOT NULL
);

CREATE TABLE itens_os_servicos(
	ID_os INT NOT NULL,
	ID_servico_ref INT NOT NULL,
	PRIMARY KEY(ID_os, ID_servico_ref),
	valor_cobrado DECIMAL(10,2) NOT NULL,
	diagnostico_tecnico TEXT(500) NOT NULL,
	FOREIGN KEY(ID_os)
	REFERENCES ordens_servicos(ID_os),
	FOREIGN KEY(ID_servico_ref)
	REFERENCES servicos_catalagos(ID_servico_ref)
);