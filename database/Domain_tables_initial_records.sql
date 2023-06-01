-- Primeiros registros a incluir nas tabelas de domínio:

-- status_usuario:
INSERT INTO status_usuario(status) VALUES('Ativo');
INSERT INTO status_usuario(status) VALUES('Bloqueado');

-- perfil_usuario:
INSERT INTO perfil_usuario(perfil) VALUES('Gerente de Projeto');
INSERT INTO perfil_usuario(perfil) VALUES('Analista de Projeto');
INSERT INTO perfil_usuario(perfil) VALUES('Administrador');

-- status_projeto:
INSERT INTO status_projeto(status) VALUES('Pendente');
INSERT INTO status_projeto(status) VALUES('Concluído');
INSERT INTO status_projeto(status) VALUES('Atrasado');
INSERT INTO status_projeto(status) VALUES('Em execução');
INSERT INTO status_projeto(status) VALUES('Impedido');
INSERT INTO status_projeto(status) VALUES('Cancelado');

-- status_tarefa:
INSERT INTO status_tarefa(status) VALUES('Pendente');
INSERT INTO status_tarefa(status) VALUES('Concluído');
INSERT INTO status_tarefa(status) VALUES('Atrasado');
INSERT INTO status_tarefa(status) VALUES('Em execução');
INSERT INTO status_tarefa(status) VALUES('Impedido');
INSERT INTO status_tarefa(status) VALUES('Cancelado');


