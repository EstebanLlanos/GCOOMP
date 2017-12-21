ALTER TABLE gios_datos_rechazados_r4505 ADD COLUMN campo_extra_120_eapb_regis character varying(320);
ALTER TABLE gios_datos_validados_exito_r4505 ADD COLUMN campo_extra_120_eapb_regis character varying(320);


CREATE TABLE gioss_indexador_dupl_del_validador_4505_agrup_ips
(
  id_usuario character varying(320),
  tipo_id_usuario character varying(320),
  nick_usuario character varying(320) NOT NULL,
  nombre_archivo_pyp character varying(320) NOT NULL,
  fecha_de_generacion date NOT NULL,
  hora_generacion character varying(320) NOT NULL,
  fecha_corte_reporte date NOT NULL,
  codigo_entidad_prestadora character varying(24) NOT NULL,
  codigo_entidad_eapb_generadora character varying(12) NOT NULL,
  campo_3_tipo_id character varying(320) NOT NULL,
  campo_4_numero_id character varying(320) NOT NULL,
  campo_extra_120_eapb_regis character varying(320) NOT NULL,
  lista_lineas_donde_hay_duplicados text NOT NULL,
  PRIMARY KEY (nombre_archivo_pyp, fecha_de_generacion, hora_generacion, fecha_corte_reporte, codigo_entidad_prestadora, codigo_entidad_eapb_generadora, nick_usuario, campo_3_tipo_id, campo_4_numero_id, campo_extra_120_eapb_regis),
  FOREIGN KEY (codigo_entidad_eapb_generadora) REFERENCES gioss_entidades_sector_salud (codigo_entidad)
);

CREATE TABLE gioss_indexador_dupl_del_reparador_4505_agrup_ips
(
  id_usuario character varying(320) NOT NULL,
  tipo_id_usuario character varying(320) NOT NULL,
  nick_usuario character varying(320) NOT NULL,
  nombre_archivo_pyp character varying(320) NOT NULL,
  fecha_de_generacion date NOT NULL,
  hora_generacion character varying(320) NOT NULL,
  fecha_corte_reporte date NOT NULL,
  codigo_entidad_prestadora character varying(24) NOT NULL,
  codigo_entidad_eapb_generadora character varying(12) NOT NULL,
  campo_3_tipo_id character varying(320) NOT NULL,
  campo_4_numero_id character varying(320) NOT NULL,  
  campo_extra_120_eapb_regis character varying(320) NOT NULL,
  lista_lineas_donde_hay_duplicados text NOT NULL,
  contiene_filas_coincidentes character varying(320),
  PRIMARY KEY (nombre_archivo_pyp, fecha_de_generacion, hora_generacion, fecha_corte_reporte, codigo_entidad_prestadora, codigo_entidad_eapb_generadora, id_usuario, tipo_id_usuario, nick_usuario, campo_3_tipo_id, campo_4_numero_id, campo_extra_120_eapb_regis),
  FOREIGN KEY (codigo_entidad_eapb_generadora) REFERENCES gioss_entidades_sector_salud (codigo_entidad)
);


ALTER TABLE corregidos_solo_duplicados_pyp4505 ADD COLUMN campo_extra_120_eapb_regis character varying(320);
ALTER TABLE corregidos_sin_duplicados_pyp4505 ADD COLUMN campo_extra_120_eapb_regis character varying(320);
ALTER TABLE corregidos_con_duplicados_pyp4505 ADD COLUMN campo_extra_120_eapb_regis character varying(320);