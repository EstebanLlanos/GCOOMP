
	--ALTERS TABLAS REPORTE OBLIGATORIO 0123
	alter table gioss_consulta_reporte_obligatorio_hf0123_exitoso add column campo_hf_de_numero_orden_95 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_hf0123_exitoso_duplicado add column campo_hf_de_numero_orden_95 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_hf0123_rechazado add column campo_hf_de_numero_orden_95 character varying(320);
	
	--ALTERS TABLAS REGISTROS CARGADOS EXITO
	alter table gioss_tabla_registros_cargados_exito_r0123_hf add column campo_hf_de_numero_orden_95 character varying(320);
	alter table gioss_tabla_registros_no_cargados_rechazados_r0123_hf add column campo_hf_de_numero_orden_95 character varying(320);
	
	--ALTERS TABLAS CORRECCION
	alter table corregidos_con_duplicados_hf0123 add column campo_hf_de_numero_orden_95 character varying(320);
	alter table corregidos_sin_duplicados_hf0123 add column campo_hf_de_numero_orden_95 character varying(320);
	alter table corregidos_solo_duplicados_hf0123 add column campo_hf_de_numero_orden_95 character varying(320);

	--ALTERS TABLAS REPORTE OBLIGATORIO 0247
	alter table gioss_consulta_reporte_obligatorio_cancer0247_exitoso add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_cancer0247_exitoso_duplicado add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_cancer0247_rechazado add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_cancer0247_solo_duplicados add column campo_cancer_de_numero_orden_211 character varying(320);
	
	--ALTERS TABLAS REGISTROS CARGADOS EXITO
	alter table gioss_tabla_registros_cargados_exito_r0247_cancer add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table gioss_tabla_registros_no_cargados_rechazados_r0247_cancer add column campo_cancer_de_numero_orden_211 character varying(320);
	
	--ALTERS TABLAS CORRECCION
	alter table corregidos_con_duplicados_cancer0247 add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table corregidos_sin_duplicados_cancer0247 add column campo_cancer_de_numero_orden_211 character varying(320);
	alter table corregidos_solo_duplicados_cancer0247 add column campo_cancer_de_numero_orden_211 character varying(320);	

	--ALTERS TABLAS REPORTE OBLIGATORIO 2463
	alter table gioss_consulta_reporte_obligatorio_erc2463_exitoso add column campo_erc_de_numero_orden_119 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_erc2463_exitoso_duplicado add column campo_erc_de_numero_orden_119 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_erc2463_rechazado add column campo_erc_de_numero_orden_119 character varying(320);
	
	--ALTERS TABLAS REGISTROS CARGADOS EXITO
	alter table gioss_tabla_registros_cargados_exito_r2463_erc add column campo_erc_de_numero_orden_119 character varying(320);
	alter table gioss_tabla_registros_no_cargados_rechazados_r2463_erc add column campo_erc_de_numero_orden_119 character varying(320);
	
	--ALTERS TABLAS CORRECCION
	alter table corregidos_con_duplicados_erc2463 add column campo_erc_de_numero_orden_119 character varying(320);
	alter table corregidos_sin_duplicados_erc2463 add column campo_erc_de_numero_orden_119 character varying(320);
	alter table corregidos_solo_duplicados_erc2463 add column campo_erc_de_numero_orden_119 character varying(320);
	

	--ALTERS TABLAS REPORTE OBLIGATORIO 4725
	alter table gioss_consulta_reporte_obligatorio_sida4725_exitoso add column campo_sida_de_numero_orden_185 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_sida4725_exitoso_duplicado add column campo_sida_de_numero_orden_185 character varying(320);
	alter table gioss_consulta_reporte_obligatorio_sida4725_rechazado add column campo_sida_de_numero_orden_185 character varying(320);
	
	--ALTERS TABLAS REGISTROS CARGADOS EXITO
	alter table gioss_tabla_registros_cargados_exito_r4725_sida add column campo_sida_de_numero_orden_185 character varying(320);
	alter table gioss_tabla_registros_no_cargados_rechazados_r4725_sida add column campo_sida_de_numero_orden_185 character varying(320);
	
	--ALTERS TABLAS CORRECCION
	alter table corregidos_con_duplicados_sida4725 add column campo_sida_de_numero_orden_185 character varying(320);
	alter table corregidos_sin_duplicados_sida4725 add column campo_sida_de_numero_orden_185 character varying(320);
	alter table corregidos_solo_duplicados_sida4725 add column campo_sida_de_numero_orden_185 character varying(320);

	--ALTERS TABLAS archivo analisis

	ALTER TABLE gioss_archivo_para_analisis_0123 ADD COLUMN campo_hf_de_numero_orden_95 character varying(320);
	alter table gioss_archivo_para_analisis_0247 ADD COLUMN campo_cancer_de_numero_orden_211 character varying(320);
	alter table gioss_archivo_para_analisis_2463 ADD COLUMN campo_erc_de_numero_orden_119 character varying(320);
	alter table gioss_archivo_para_analisis_4725 ADD COLUMN campo_sida_de_numero_orden_185 character varying(320);
	