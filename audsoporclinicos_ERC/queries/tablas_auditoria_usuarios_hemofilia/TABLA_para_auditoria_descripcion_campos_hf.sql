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
-- Name: tabla_auditoria_descripcion_campos_hf; Type: TABLE; Schema: public; Owner: giossuser
--

CREATE TABLE tabla_auditoria_descripcion_campos_hf (
    numero_orden_campo character varying(320) NOT NULL,
    numero_campo_norma character varying(320),
    descripcion_campo character varying(320)
);


ALTER TABLE tabla_auditoria_descripcion_campos_hf OWNER TO giossuser;

--
-- Data for Name: tabla_auditoria_descripcion_campos_hf; Type: TABLE DATA; Schema: public; Owner: giossuser
--

INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('numeroordencampo', 'numerocamponorma', 'descripcioncampo');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('0', '1', 'PrimerNombreUsuario');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('1', '2', 'SegundoNombreUsuario');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('2', '3', 'PrimerApellidoUsuario');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('3', '4', 'SegundoApellidoUsuario');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('4', '5', 'TipoDeIdentificacionUsuario');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('5', '6', 'NumeroDeIdentificacionUsuario');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('6', '7', 'FechaDeNacimiento');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('7', '8', 'Sexo');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('8', '9', 'Ocupacion');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('9', '10', 'RegimenDeAfiliacionAlSgsss');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('10', '11', 'EPS');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('11', '12', 'PertenenciaEtnica');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('12', '13', 'GrupoPoblacional');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('13', '14', 'MunicipioDeResidencia');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('14', '15', 'NumeroTelefonicoDelPaciente');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('15', '16', 'FechaDeAfiliacionALaEpsQueRegistra');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('16', '17', 'EstadoDeGestacionALaFechaDeCorte');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('17', '18', 'UsuarioEnProgramaDePlanificacionOConsejeriaGenetica');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('18', '19', 'EdadUsuarioEnElMomentoDelDiagnostico');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('19', '20', 'MotivoDeLaPruebaDeDiagnostico');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('20', '21', 'FechaDeDiagnostico');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('21', '22', 'IpsDondeSeRealizaLaConfirmacionDx');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('22', '23', 'TipoDeDeficienciaDiagnosticada');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('23', '24', 'ClasificacionDeSeveridadSegunNivelDeFactor');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('24', '25', 'ActividadCoagulanteDelFactor');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('25', '26', 'AntecedentesFamiliaresAsociadosAHemofilia');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('26', '27', 'FactorRecibidoTratamientoInicial');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('27', '28', 'EsquemaTratamientoInicial');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('28', '29', 'FechaDeInicioDelPrimerTratamiento');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('29', '30', 'FactorRecibidoTratamientoActual');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('30', '31', 'EsquemaTratamientoActual');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('31', '32', 'Peso');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('32', '32.1', 'Dosis');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('33', '32.2', 'FrecuenciaPorSemana');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('34', '32.3', 'NumeroDeUnidadesTotalesEnElPeriodo');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('35', '32.4', 'NumeroDeAplicacionesDelFactorEnElPeriodoDemanda');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('36', '33', 'ModalidadDeAplicacionTratamiento');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('37', '34', 'ViaDeAdministracion');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('38', '35', 'CodigoCumDelFactorPosRecibido');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('39', '36', 'CodigoCumDelFactorNoPosRecibido');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('40', '37', 'CodigoCumDeOtrosTratamientosUtilizados');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('41', '38', 'CodigoCumDeOtrosTratamientosUtilizados');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('42', '39', 'CodigoValidoDeHabilitacionDeLaIpsDondeSeRealizaElSeguimientoActual');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('43', '40', 'Hemartrosis');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('44', '40.1', 'NumeroDeHemartrosisEspontaneasDuranteLosultimosDoceMeses');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('45', '40.2', 'NumeroDeHemartrosisTraumaticasDuranteLosultimosDoceMeses');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('46', '41', 'HemorragiaDelIlio-Psoas');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('47', '42', 'HemorragiaDeOtrosMuscular/TejidosBlandos');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('48', '43', 'HemorragiaIntracraneal');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('49', '44', 'HemorragiaEnCuelloOGarganta');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('50', '45', 'HemorragiaOral');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('51', '46', 'OtrasHemorragias');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('52', '47.1', 'NumeroDeOtrasHemorragiasEspontaneasDiferentesAHemartrosisDuranteLosultimosDoceMeses');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('53', '47.2', 'NumeroDeOtrasHemorragiasTraumaticasDiferentesAHemartrosisDuranteLosultimosDoceMeses');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('54', '47.3', 'NumeroDeOtrasHemorragiasAsociadasAProcedimientoDiferentesAHemartrosisDuranteLosultimosDoceMeses');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('55', '48', 'PresenciaDeInhibidor');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('56', '48.1', 'FechaDeDeterminacionDeTitulosDelInhibidorUltimaMasCercanaALaFechaDeCorte');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('57', '48.2', 'HaRecibidoElPacienteIti-InduccionALaToleranciaInmune-');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('58', '48.3', 'EstaRecibiendoItiEnElPeriodoDeCorte');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('59', '48.4', 'TiempoQueLlevaElPacienteEnItiDias');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('60', '49', 'ArtropatiaHemofilicaCronica');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('61', '49.1', 'NumeroDeArticulacionesComprometidas');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('62', '50', 'UsuarioInfectadoPorVhc');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('63', '51', 'UsuarioInfectadoPorVhb');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('64', '52', 'UsuarioInfectadoPorVih');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('65', '53', 'Pseudotumores');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('66', '54', 'Fracturasosteopenia/osteoporosis');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('67', '55', 'Anafilaxis');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('68', '55.1', 'AQueFactorSeLeAtribuyeLaReaccionAnafilactica');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('69', '56', 'ReemplazosArticulares');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('70', '56.1', 'ReemplazosArticularesEnElPeriodoDeCorte');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('71', '57', 'SeleccionarElProfesionalQueLideraLaAtencionDelPaciente');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('72', '57.1', 'ConsultasConHematologo');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('73', '57.2', 'ConsultasConOrtopedista');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('74', '57.3', 'IntervencionPorParteDelProfesionalDeEnfermeria');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('75', '57.4', 'ConsultasConOdontologo');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('76', '57.5', 'ConsultasConNutricionista');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('77', '57.6', 'IntervencionPorParteDeTrabajoSocial');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('78', '57.7', 'ConsultasConFisiatria');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('79', '57.8', 'ConsultasConPsicologia');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('80', '57.9', 'IntervencionPorParteDeQuimicoFarmaceutico');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('81', '57.10', 'IntervencionPorParteDeFisioterapia');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('82', '57.11', 'PrimerNombreDelMedicoTratantePrincipal');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('83', '57.12', 'SegundoNombreDelMedicoTratantePrincipal');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('84', '57.13', 'PrimerApellidoDelMedicoTratantePrincipal');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('85', '57.14', 'SegundoApellidoDelMedicoTratantePrincipal');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('86', '58', 'NumeroDeAtencionesEnElServicioDeUrgenciasQueRequierenTratamientoParaLaCondicionDeHemofilia');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('87', '59', 'NumeroDeEventosHospitalariosPorCausaDeLaHemofiliaIncluyeEventosProgramadosYNoProgramados');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('88', '60', 'CostoDeFactoresPos');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('89', '61', 'CostoDeFactoresNoPos');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('90', '62', 'CostoTotalDelManejoAsociadoALaCoagulopatia');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('91', '63', 'CostoIncapacidadesLaboralesRelacionadasConLaCoagulopatia');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('92', '64', 'Novedades');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('93', '64.1', 'CausaDeMuerte');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('94', '64.2', 'FechaDeMuerte');
INSERT INTO tabla_auditoria_descripcion_campos_hf VALUES ('95', '65', 'Codigo Unico BDUA');


--
-- Name: tabla_auditoria_descripcion_campos_hf tabla_auditoria_descripcion_campos_hf_pkey; Type: CONSTRAINT; Schema: public; Owner: giossuser
--

ALTER TABLE ONLY tabla_auditoria_descripcion_campos_hf
    ADD CONSTRAINT tabla_auditoria_descripcion_campos_hf_pkey PRIMARY KEY (numero_orden_campo);


--
-- PostgreSQL database dump complete
--

