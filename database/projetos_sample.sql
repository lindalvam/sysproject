-- Exemplos de projetos 

INSERT INTO `projeto` (`id`, `titulo`, `descricao`, `dt_inicio`, `dt_fim`, `created_at`, `updated_at`, `status_projeto`) VALUES (NULL, 'Edificação de nova ala no Hospital Distrital de Santa Clara do Sul', 'Projeto tem objetivo de construir uma nova ala no Hospital Distrital de Santa Clara do Sul, incluindo:\r\n\r\n1) 4 consultórios médicos.\r\n2) 1 sala para Ressonância Magnética\r\n3) Sala para Hemodiálise\r\n4) Recepção interna\r\n5) Sala de Espera para até 20 pessoas', '2023-05-31', '2023-12-30', current_timestamp(), current_timestamp(), '1');


INSERT INTO `projeto` (`titulo`, `descricao`, `dt_inicio`, `dt_fim`, `created_at`, `updated_at`, `status_projeto`) VALUES ( 'Concurso de obras artisticas para Jovens da Comunidade da Figueira', 'Projeto tem objetivo de viabilizar concurso artístico para jovens da comunidade da Figueira. A viabilização busca financiamento para os prêmios e divulgação, bem como a disponibilização dos materiais necessários aos participantes', '2023-05-20', '2023-12-31', current_timestamp(), current_timestamp(), '2');


-- Criando fase de planejamento para todos os projetos:

INSERT INTO `projeto_fase` (`id`, `id_projeto`, `fase`, `ordem`, `created_at`) VALUES (NULL, '1', 'Planejamento', '1', current_timestamp());

INSERT INTO `projeto_fase` (`id`, `id_projeto`, `fase`, `ordem`, `created_at`) VALUES (NULL, '2', 'Planejamento', '1', current_timestamp());

INSERT INTO `projeto_fase` (`id`, `id_projeto`, `fase`, `ordem`, `created_at`) VALUES (NULL, '2', 'Execução', '2', current_timestamp());


-- Criando equipes para os projetos de exemplo:

INSERT INTO `projeto_equipe` (`id`, `id_projeto`, `id_usuario`, `perfil_usuario`, `created_at`) VALUES 
							 (NULL, '1', '7', 'Gerente', current_timestamp()), 
							 (NULL, '1', '5', 'Analista', current_timestamp()), 
							 (NULL, '1', '6', 'Analista', current_timestamp()), 
							 (NULL, '1', '4', 'Analista', current_timestamp());
							 
INSERT INTO `projeto_equipe` (`id`, `id_projeto`, `id_usuario`, `perfil_usuario`, `created_at`) VALUES 
							 (NULL, '2', '10', 'Gerente', current_timestamp()), 
							 (NULL, '2', '11', 'Analista', current_timestamp()), 
							 (NULL, '2', '12', 'Analista', current_timestamp()), 
							 (NULL, '2', '13', 'Analista', current_timestamp());
							 
-- Criando tarefas para a fase de planejamento dos projetos de exemplo:

INSERT INTO `tarefa` (`id`, `id_projeto`, `id_fase`, `id_responsavel`, `descricao`, `comentario`, `dt_inicio`, `dt_fim`, `created_at`, `status_tarefa`, `ordem_prioridade`) 
           VALUES (NULL, '1', '1', '1', 'Realizar Ata da primeira reunião com os Stakeholders do Projeto', 'Ata deve ser disponibilizada para todos os stakeholders em formato digital', '2023-05-01', '2023-05-23', current_timestamp(), '3', '1');
INSERT INTO `tarefa` (`id`, `id_projeto`, `id_fase`, `id_responsavel`, `descricao`, `comentario`, `dt_inicio`, `dt_fim`, `created_at`, `status_tarefa`, `ordem_prioridade`) 
           VALUES (NULL, '1', '1', '2', 'Lavrar documento no cartório validando os assuntos', 'Lavrar documento no cartório validando os assuntos com auenticalão', '2023-05-24', NULL, current_timestamp(), '4', '2'), 
		          (NULL, '1', '1', '3', 'Montar planilha para financiamento dos materiais', 'Planilha em formato digital', '2023-05-29', NULL, current_timestamp(), '1', '3');


INSERT INTO `tarefa` (`id`, `id_projeto`, `id_fase`, `id_responsavel`, `descricao`, `comentario`, `dt_inicio`, `dt_fim`, `created_at`, `status_tarefa`, `ordem_prioridade`) 
		      VALUES (NULL, '2', '2', '6', 'Montar roteiro da reunião inicial', 'Reunião com ata', '2023-05-17', '2023-05-19', current_timestamp(), '3', '1'), 
			         (NULL, '2', '2', '7', 'Teste testado do teste ', 'Montar teste', '2023-05-29', NULL, current_timestamp(), '4', '2');
INSERT INTO `tarefa` (`id`, `id_projeto`, `id_fase`, `id_responsavel`, `descricao`, `comentario`, `dt_inicio`, `dt_fim`, `created_at`, `status_tarefa`, `ordem_prioridade`) 
		     VALUES (NULL, '2', '2', '8', 'Fazer o concurso de pintura a dedo', 'Dividir por 4 equipes disputando uma unica fase', '2023-05-31', NULL, current_timestamp(), '1', '3'), 
			        (NULL, '2', '2', '6', 'Vistoriar local de execução do concurso', 'concurso', '2023-05-31', NULL, current_timestamp(), '5', '4');
					
INSERT INTO `tarefa` (`id`, `id_projeto`, `id_fase`, `id_responsavel`, `descricao`, `comentario`, `dt_inicio`, `dt_fim`, `created_at`, `status_tarefa`, `ordem_prioridade`) 
		      VALUES (NULL, '2', '2', '6', 'Montar outro roteiro da reunião inicial', 'Sem Reunião com ata', '2023-05-14', '2023-05-20', current_timestamp(), '4', '5'), 
			         (NULL, '2', '2', '6', 'Montar mais um roteiro da reunião inicial', 'Sem Reunião com ata', '2023-05-11', '2023-05-21', current_timestamp(), '4', '4'), 
			         (NULL, '2', '2', '6', 'Montar mais um roteiro da reunião inicial', 'Sem Reunião com ata', '2023-05-11', '2023-05-21', current_timestamp(), '5', '6'), 
			         (NULL, '2', '2', '6', 'Descontar um cheque na esquina', 'Com fundos', '2023-05-11', '2023-05-11', current_timestamp(), '1', '7');