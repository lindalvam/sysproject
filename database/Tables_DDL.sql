-- TABELAS DE DOMINIO

CREATE TABLE status_usuario(
id bigint unsigned not null  auto_increment,
status varchar(120) not null , 
PRIMARY KEY(id)
);

CREATE TABLE status_projeto(
id bigint unsigned not null  auto_increment,
status varchar(120) not null , 
PRIMARY KEY(id)
);

CREATE TABLE status_tarefa(
id bigint unsigned not null  auto_increment,
status varchar(120) not null , 
PRIMARY KEY(id)
);

CREATE TABLE perfil_usuario(
id bigint unsigned not null  auto_increment,
perfil varchar(120) not null , 
PRIMARY KEY(id)
);




-- TABELAS OPERACIONAIS
CREATE TABLE usuario(
id bigint unsigned not null  auto_increment,
nome varchar(120) not null , 
usuario varchar(120) not null , 
email varchar(120) not null , 
perfil_usuario bigint unsigned not null,
senha varchar(120) not null, 
status_usuario bigint unsigned not null,
created_at timestamp default NOW(), 
updated_at timestamp default NOW(), 
 PRIMARY KEY(id),
 CONSTRAINT status_usfk FOREIGN KEY (status_usuario)
 REFERENCES status_usuario(id) ON DELETE RESTRICT ON UPDATE CASCADE,
 CONSTRAINT perfil_usfk FOREIGN KEY (perfil_usuario)
 REFERENCES perfil_usuario(id) ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE projeto(
id bigint unsigned not null  auto_increment,
titulo varchar(120) not null , 
descricao text not null,
dt_inicio date not null, 
dt_fim date not null, 
status_projeto bigint unsigned not null,
created_at timestamp default NOW(), 
updated_at timestamp default NOW(), 
 PRIMARY KEY(id),
 CONSTRAINT status_prfk FOREIGN KEY (status_projeto)
 REFERENCES status_projeto(id) ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE projeto_equipe(
id bigint unsigned not null  auto_increment,
id_projeto bigint unsigned not null, 
id_usuario bigint unsigned not null, 
perfil_usuario varchar(100) not null,
created_at timestamp default NOW(), 
 PRIMARY KEY(id),
 CONSTRAINT projeto_equipe_usfk FOREIGN KEY (id_usuario)
 REFERENCES usuario(id) ON DELETE CASCADE,
 CONSTRAINT projeto_equipe_prfk FOREIGN KEY (id_projeto)
 REFERENCES projeto(id) ON DELETE CASCADE 
);

CREATE TABLE projeto_fase(
id bigint unsigned not null  auto_increment,
id_projeto bigint unsigned not null, 
fase varchar(1000) not null,
ordem int unsigned not null default 0,
created_at timestamp default NOW(), 
 PRIMARY KEY(id),
 CONSTRAINT projeto_fase_prfk FOREIGN KEY (id_projeto)
 REFERENCES projeto(id) ON DELETE CASCADE 
);

CREATE TABLE projeto_risco(
id bigint unsigned not null  auto_increment,
id_projeto bigint unsigned not null, 
risco text not null,
created_at timestamp default NOW(), 
 PRIMARY KEY(id),
 CONSTRAINT projeto_risco_prfk FOREIGN KEY (id_projeto)
 REFERENCES projeto(id) ON DELETE CASCADE 
);


CREATE TABLE tarefa(
id bigint unsigned not null  auto_increment,
id_projeto bigint unsigned not null, 
id_fase bigint unsigned not null, 
id_responsavel bigint unsigned not null, 
descricao varchar(120) not null,
comentario text,
dt_inicio date not null, 
dt_fim date, 
status_tarefa bigint unsigned not null,
ordem_prioridade int unsigned not null,
created_at timestamp default NOW(), 
 PRIMARY KEY(id),
 CONSTRAINT tarefa_prfk FOREIGN KEY (id_projeto)
 REFERENCES projeto(id) ON DELETE CASCADE ,
 CONSTRAINT tarefa_fsfk FOREIGN KEY (id_fase)
 REFERENCES projeto_fase(id) ON DELETE CASCADE ,
 CONSTRAINT tarefa_usfk FOREIGN KEY (id_responsavel)
 REFERENCES projeto_equipe(id_usuario) ON DELETE CASCADE ,
 CONSTRAINT status_trfk FOREIGN KEY (status_tarefa)
 REFERENCES status_tarefa(id) ON UPDATE CASCADE ON DELETE RESTRICT 
);

CREATE TABLE relatorio(
id bigint unsigned not null  auto_increment,
id_projeto bigint unsigned not null, 
dt_relatorio date not null, 
tipo_relatorio varchar(100) not null, 
dados_relatorio text not null,
created_at timestamp default NOW(), 
 PRIMARY KEY(id),
 CONSTRAINT relatorio_prfk FOREIGN KEY (id_projeto)
 REFERENCES projeto(id) ON DELETE CASCADE 
);

