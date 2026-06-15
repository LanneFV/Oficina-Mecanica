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

INSERT INTO enderecos (rua, numero, bairro, cidade, estado) VALUES
	('Rua das Flores',    '123', 'Centro',     'São Paulo',   'SP'),
	('Av. Brasil',        '456', 'Jardins',    'Campinas',    'SP'),
	('Rua das Palmeiras', '789', 'Boa Vista',  'Curitiba',    'PR');

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

INSERT INTO clientes (nome, documento, id_endereco, perfil, senha) VALUES
	('Ana Lima',    '11122233344', 1, 'administrador',  '$2y$10$e0NRp3JbSdSBIfKaR5JoUuKx1a5FqLkDvS7Oc4UNqYFgE3RDTFKiW'),
	('Bruno Souza', '22233344455', 2, 'gerencia',       '$2y$10$e0NRp3JbSdSBIfKaR5JoUuKx1a5FqLkDvS7Oc4UNqYFgE3RDTFKiW'),
	('Carla Nunes', '33344455566', 3, 'usuario_comum',  '$2y$10$e0NRp3JbSdSBIfKaR5JoUuKx1a5FqLkDvS7Oc4UNqYFgE3RDTFKiW');

CREATE TABLE contato(
	ID_contato INT PRIMARY KEY AUTO_INCREMENT,
	contato VARCHAR(11) NOT NULL,
	id_cliente INT,
	FOREIGN KEY(id_cliente)
	REFERENCES clientes(ID_cliente)
);

INSERT INTO contato (contato, id_cliente) VALUES
	('11999990001', 1),
	('11999990002', 2),
	('11999990003', 3);

CREATE TABLE marcas(
	ID_marca INT PRIMARY KEY AUTO_INCREMENT,
	nome_marca VARCHAR(30)
);

INSERT INTO marcas (nome_marca) VALUES
	('Toyota'),
	('Honda'),
	('Ford');

CREATE TABLE modelos(
	ID_modelo INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(50) NOT NULL,
	id_marca INT,
	FOREIGN KEY(id_marca)
	REFERENCES marcas(ID_marca)
);

INSERT INTO modelos (nome, id_marca) VALUES
	('Corolla', 1),  
	('Civic',   2),  
	('Ka',      3);  

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

INSERT INTO veiculos (placa, ano, id_cliente, id_modelo) VALUES
	('ABC1D23', 2020, 1, 1), 
	('XYZ9E87', 2019, 2, 2),  
	('QWE5F12', 2021, 3, 3);  

CREATE TABLE mecanicos(
	ID_mecanico INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL,
	especialidade VARCHAR(50) NOT NULL,
	disponibilidade BOOLEAN NOT NULL
);

INSERT INTO mecanicos (nome, especialidade, disponibilidade) VALUES
	('João Melo',    'Motor',     TRUE),
	('Pedro Alves',  'Elétrica',  FALSE),
	('Marcos Silva', 'Suspensão', TRUE);

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

INSERT INTO ordens_servicos (status, data_abertura, data_entrega_prevista, garantia_meses, id_veiculo, id_mecanico) VALUES
	('aberta',    NOW(), '2025-06-01', 3, 1, 1),
	('em andamento', NOW(), '2025-06-05', 6, 2, 2),
	('concluida', NOW(), '2025-05-20', 12, 3, 3);

CREATE TABLE pecas(
	ID_peca INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL,
	descricao TEXT(500) NOT NULL,
	preco_unitario DECIMAL(10,2) NOT NULL,
	nivel_estoque INT(5) NOT NULL
);

INSERT INTO pecas (nome, descricao, preco_unitario, nivel_estoque) VALUES
	('Filtro de óleo',  'Filtro para motor 1.0', 35.00,  50),
	('Pastilha de freio','Par dianteiro',        120.00, 30),
	('Vela de ignição', 'Jogo com 4 velas',      80.00,  20);

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

INSERT INTO itens_os_pecas (ID_os, ID_peca, quantidade, preco_venda) VALUES
	(1, 1, 2, 40.00),   
	(2, 2, 1, 130.00), 
	(3, 3, 4, 90.00);  

CREATE TABLE servicos_catalagos(
	ID_servico_ref INT PRIMARY KEY AUTO_INCREMENT,
	descricao VARCHAR(205) NOT NULL,
	preco_base DECIMAL(10,2) NOT NULL
);

INSERT INTO servicos_catalagos (descricao, preco_base) VALUES
	('Troca de óleo',         150.00),
	('Alinhamento',           100.00),
	('Revisão completa',      500.00);

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

INSERT INTO itens_os_servicos (ID_os, ID_servico_ref, valor_cobrado, diagnostico_tecnico) VALUES
	(1, 1, 160.00, 'Óleo muito escuro, troca necessária'),
	(2, 2, 110.00, 'Pneus com desgaste irregular'),
	(3, 3, 520.00, 'Revisão dos 50mil km');
    
USE oficina;
UPDATE clientes SET senha = '$2y$10$XtlwheklyaqxrmBB6yQpDeG9PEIdaDhwXjvitUu4tinrGL0yri5qe';