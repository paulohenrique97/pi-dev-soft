create database db_ecommerce;

use db_ecommerce;

create table tb_usuarios (
    id int auto_increment primary key,
    usuario varchar(32) not null unique,
    senha varchar(255) not null,
    nome varchar(32) not null,
    sobrenome varchar(32) not null,
    cep varchar(8),
    numero_residencia int,
    admin bool not null default false
);

create table tb_categorias (
    id int auto_increment primary key,
    nome varchar(32) not null
);

create table tb_formatos_pacote (
	id int auto_increment primary key,
	id_externo varchar(255) not null,
	descricao varchar(255) not null
);

create table tb_produtos (
    id int auto_increment primary key,
    id_categoria int not null,
    id_formato int not null,
    nome varchar(255) not null,
    descricao text not null,
    link_foto varchar(255) not null,
    preco float not null,
    peso float not null,
    altura float not null,
    largura float not null,
    comprimento float not null,
    diametro float not null,
    estoque int not null default 0,
    data_criacao datetime not null default now(),
    foreign key (id_categoria) references tb_categorias(id),
    foreign key (id_formato) references tb_formatos_pacote(id)
);

create table tb_carrinhos (
	id int auto_increment primary key,
	id_usuario int not null,
	id_produto int not null,
	quantidade int not null,
	foreign key (id_usuario) references tb_usuarios(id),
	foreign key (id_produto) references tb_produtos(id)
);

create table tb_pedidos (
	id int auto_increment primary key,
	id_usuario int not null,
	valor float not null,
	valor_frete float not null,
	cep varchar(8) not null,
    numero_residencia int not null,
	foreign key (id_usuario) references tb_usuarios(id)
);

DELIMITER //
create procedure sp_carrinho (in usuario int) 
begin
select
	b.nome as nome,
	a.quantidade as quantidade,
	a.preco as preco,
	a.quantidade * b.preco as valor
from
	tb_carrinhos a
inner join 
	tb_produtos b on a.id_produto = b.id 
where a.id_usuario = @usuario;
end;
//
DELIMITER ;

INSERT INTO tb_categorias (nome) VALUES 
('CELULARES')
,('ELETRôNICOS')
,('ACESSóRIOS')
,('LIVROS')
,('JOGOS')
;

insert
	into
	tb_formatos_pacote (id_externo, descricao)
values ('1', 'Caixa/Pacote - CORREIOS') ,
('2', 'Rolo/Prisma - CORREIOS') ,
('3', 'Envelope - CORREIOS') ;

INSERT INTO tb_produtos (id_categoria,id_formato,nome,descricao,link_foto,preco,peso,altura,largura,comprimento,diametro,estoque,data_criacao) VALUES 
(1,1,'SMARTPHONE XIAOMI REDMI NOTE 8 - BRANCO','Smartphone Xiaomi Redmi Note 8 4GB Ram Tela 6.3 64GB Camera Quad 48+8+2+2MP','https://images-na.ssl-images-amazon.com/images/I/61iHCUlBVIL._AC_SL1000_.jpg',1400.0,0.549,1.0,4.0,14.0,0.0,20,'2020-06-12 23:53:59.000')
,(1,1,'ZENFONE MAX PRO M2-4GB 128GB - BLACK SAPHIRE','Zenfone Max Pro M2-4GB 128GB / SNAPDRAGON / 4 GB / 128 GB / Android / Black Saphire / SIM (Nano) / SIM (Nano)','https://images-na.ssl-images-amazon.com/images/I/61RnpDG2rpL._AC_SL1000_.jpg',1300.0,0.399,5.4,8.7,16.3,0.0,30,'2020-06-13 16:07:10.000')
,(2,1,'SMART TV LG LCD 32','SMART TV LG LCD 32 COM COMANDOS DE VOZ, WEBOS 4.5, UPSCALER HD, HDR ATIVO E WI-FI PRETA - 32LM625BPSB','https://images-na.ssl-images-amazon.com/images/I/71g%2BYAZ1IML._AC_SL1500_.jpg',1100.0,6.3,14.6,80.8,51.2,0.0,0,'2020-06-13 16:28:57.000')
;

INSERT INTO tb_usuarios (usuario,senha,nome,sobrenome,cep,numero_residencia,admin) VALUES 
(' paulopatine','$2y$10$9EVHdl1IE0lz.4TjBm034e9TrPITq7Uiet0DddNpimly7L2ikpl0q','PAULO','HENRIQUE','35181548',286,1)
,('junim','$2y$10$fAILt9emMPVwcK.Eb4lC/e2ErD5mZ99hDXmzOThgtnQZVX2rY8vxO','PAULO','JUNIOR','0',0,0)
;
