--
-- PostgreSQL database dump
--

-- Dumped from database version 10.1
-- Dumped by pg_dump version 10.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: tabla_auditoria_tipo_cohortes; Type: TABLE; Schema: public; Owner: giossuser
--

CREATE TABLE tabla_auditoria_tipo_cohortes (
    codigo integer NOT NULL,
    descripcion character varying(320)
);


ALTER TABLE tabla_auditoria_tipo_cohortes OWNER TO giossuser;

--
-- Data for Name: tabla_auditoria_tipo_cohortes; Type: TABLE DATA; Schema: public; Owner: giossuser
--

INSERT INTO tabla_auditoria_tipo_cohortes VALUES (1, 'Hemofilia Nuevo');
INSERT INTO tabla_auditoria_tipo_cohortes VALUES (2, 'Hemofilia Anterior');
INSERT INTO tabla_auditoria_tipo_cohortes VALUES (3, 'Otra Cougalopatia Nueva');
INSERT INTO tabla_auditoria_tipo_cohortes VALUES (4, 'Otra Cougalopatia Anterior');
INSERT INTO tabla_auditoria_tipo_cohortes VALUES (5, 'Cambio de DX o Severidad');


--
-- Name: tabla_auditoria_tipo_cohortes tabla_auditoria_tipo_cohortes_pkey; Type: CONSTRAINT; Schema: public; Owner: giossuser
--

ALTER TABLE ONLY tabla_auditoria_tipo_cohortes
    ADD CONSTRAINT tabla_auditoria_tipo_cohortes_pkey PRIMARY KEY (codigo);


--
-- PostgreSQL database dump complete
--

