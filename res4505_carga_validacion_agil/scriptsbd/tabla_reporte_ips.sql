

CREATE TABLE gioss_indexador_para_reporte_ips
(
  entidad_reportante character varying(12) NOT NULL,
  entidad_a_reportar character varying(90) NOT NULL,
  fecha_inicio_periodo date NOT NULL,
  fecha_de_corte date NOT NULL,
  fecha_y_hora_validacion timestamp NOT NULL,  
  nombre_archivo character varying(320) NOT NULL,
  numero_de_secuencia integer,
  prestador_en_archivo character varying(12),
  nit_prestador_en_archivo character varying(20),
  nombre_prestador character varying(320),
  codigo_departamento character varying(2),
  codigo_municipio character varying(5),
  cantidad_lineas_en_archivo character varying(320),
  cantidad_lineas_correctas_en_archivo character varying(320),
  PRIMARY KEY (entidad_reportante, entidad_a_reportar, fecha_inicio_periodo, fecha_de_corte, nombre_archivo, fecha_y_hora_validacion, prestador_en_archivo)
);

ALTER TABLE gioss_indexador_para_reporte_ips ADD COLUMN cantidad_inconsistencias_para_ips character varying(320);