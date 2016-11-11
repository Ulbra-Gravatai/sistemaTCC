ALTER TABLE usuario CHANGE senha senha VARCHAR(255) NOT NULL;
INSERT INTO pessoa (id, nome, email, telefone, sexo) VALUES (1, 'Admin', 'admin', 12345678, 1);
INSERT INTO usuario_acesso (id, nome, nivel) VALUES (1, 'Admin', 0);
INSERT INTO usuario (id, pessoa_id, usuario_acesso_id,  senha) VALUES (1, 1, 1, '$2y$13$mjrXASS2pTP1EOj7PjV4zujcFyOCA9seWayXZ9YdksfKWCCAOoErq');
