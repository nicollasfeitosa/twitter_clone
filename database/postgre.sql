-- Postgres
CREATE DATABASE twitter_clone
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    CONNECTION LIMIT = -1;

-- Postgres - Create Table Users
create table usuarios(
	id serial not null primary key,
	nome varchar(100) not null,
	email varchar(150) not null,
	senha varchar(32) not null
);

-- Postgres - Create Table Tweet
CREATE TABLE public.tweets
(
    id serial NOT NULL,
    id_user integer NOT NULL,
    tweet character varying(280) NOT NULL,
	data timestamp without time zone DEFAULT now()::timestamp,
    PRIMARY KEY (id)
);

ALTER TABLE public.tweets
    OWNER to postgres;

-- Postgres - Create Table User Seguidores
CREATE TABLE public.usuarios_seguidores
(
    id serial NOT NULL,
    id_usuario integer NOT NULL,
    id_usuario_seguindo integer NOT NULL,
    PRIMARY KEY (id)
);

ALTER TABLE public.usuarios_seguidores
    OWNER to postgres;