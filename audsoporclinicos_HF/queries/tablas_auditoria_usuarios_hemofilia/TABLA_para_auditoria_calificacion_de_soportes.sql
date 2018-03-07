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
-- Name: tabla_auditoria_calificacion_de_soportes; Type: TABLE; Schema: public; Owner: giossuser
--

CREATE TABLE tabla_auditoria_calificacion_de_soportes (
    codigo character varying(4) NOT NULL,
    descripcion character varying(320)
);


ALTER TABLE tabla_auditoria_calificacion_de_soportes OWNER TO giossuser;

--
-- Data for Name: tabla_auditoria_calificacion_de_soportes; Type: TABLE DATA; Schema: public; Owner: giossuser
--

INSERT INTO tabla_auditoria_calificacion_de_soportes VALUES ('DC', 'Dato Conforme');
INSERT INTO tabla_auditoria_calificacion_de_soportes VALUES ('DNC', 'Dato Conforme');
INSERT INTO tabla_auditoria_calificacion_de_soportes VALUES ('DOND', 'Dato Conforme');


--
-- Name: tabla_auditoria_calificacion_de_soportes tabla_auditoria_calificacion_de_soportes_pkey; Type: CONSTRAINT; Schema: public; Owner: giossuser
--

ALTER TABLE ONLY tabla_auditoria_calificacion_de_soportes
    ADD CONSTRAINT tabla_auditoria_calificacion_de_soportes_pkey PRIMARY KEY (codigo);


--
-- PostgreSQL database dump complete
--

