CREATE TABLE tabla_auditoria_usuarios_reportados_hemofilia
(
	consecutivo_registro SERIAL,
	campo_hemofilia_0 character varying(320),
	campo_hemofilia_1 character varying(320),
	campo_hemofilia_2 character varying(320),
	campo_hemofilia_3 character varying(320),
	campo_hemofilia_4 character varying(320),
	campo_hemofilia_5 character varying(320),
	campo_hemofilia_6 character varying(320),
	campo_hemofilia_7 character varying(320),
	campo_hemofilia_8 character varying(320),
	campo_hemofilia_9 character varying(320),
	campo_hemofilia_10 character varying(320),
	campo_hemofilia_11 character varying(320),
	campo_hemofilia_12 character varying(320),
	campo_hemofilia_13 character varying(320),
	campo_hemofilia_14 character varying(320),
	campo_hemofilia_15 character varying(320),
	campo_hemofilia_16 character varying(320),
	campo_hemofilia_17 character varying(320),
	campo_hemofilia_18 character varying(320),
	campo_hemofilia_19 character varying(320),
	campo_hemofilia_20 character varying(320),
	campo_hemofilia_21 character varying(320),
	campo_hemofilia_22 character varying(320),
	campo_hemofilia_23 character varying(320),
	campo_hemofilia_24 character varying(320),
	campo_hemofilia_25 character varying(320),
	campo_hemofilia_26 character varying(320),
	campo_hemofilia_27 character varying(320),
	campo_hemofilia_28 character varying(320),
	campo_hemofilia_29 character varying(320),
	campo_hemofilia_30 character varying(320),
	campo_hemofilia_31 character varying(320),
	campo_hemofilia_32 character varying(320),
	campo_hemofilia_33 character varying(320),
	campo_hemofilia_34 character varying(320),
	campo_hemofilia_35 character varying(320),
	campo_hemofilia_36 character varying(320),
	campo_hemofilia_37 character varying(320),
	campo_hemofilia_38 character varying(320),
	campo_hemofilia_39 character varying(320),
	campo_hemofilia_40 character varying(320),
	campo_hemofilia_41 character varying(320),
	campo_hemofilia_42 character varying(320),
	campo_hemofilia_43 character varying(320),
	campo_hemofilia_44 character varying(320),
	campo_hemofilia_45 character varying(320),
	campo_hemofilia_46 character varying(320),
	campo_hemofilia_47 character varying(320),
	campo_hemofilia_48 character varying(320),
	campo_hemofilia_49 character varying(320),
	campo_hemofilia_50 character varying(320),
	campo_hemofilia_51 character varying(320),
	campo_hemofilia_52 character varying(320),
	campo_hemofilia_53 character varying(320),
	campo_hemofilia_54 character varying(320),
	campo_hemofilia_55 character varying(320),
	campo_hemofilia_56 character varying(320),
	campo_hemofilia_57 character varying(320),
	campo_hemofilia_58 character varying(320),
	campo_hemofilia_59 character varying(320),
	campo_hemofilia_60 character varying(320),
	campo_hemofilia_61 character varying(320),
	campo_hemofilia_62 character varying(320),
	campo_hemofilia_63 character varying(320),
	campo_hemofilia_64 character varying(320),
	campo_hemofilia_65 character varying(320),
	campo_hemofilia_66 character varying(320),
	campo_hemofilia_67 character varying(320),
	campo_hemofilia_68 character varying(320),
	campo_hemofilia_69 character varying(320),
	campo_hemofilia_70 character varying(320),
	campo_hemofilia_71 character varying(320),
	campo_hemofilia_72 character varying(320),
	campo_hemofilia_73 character varying(320),
	campo_hemofilia_74 character varying(320),
	campo_hemofilia_75 character varying(320),
	campo_hemofilia_76 character varying(320),
	campo_hemofilia_77 character varying(320),
	campo_hemofilia_78 character varying(320),
	campo_hemofilia_79 character varying(320),
	campo_hemofilia_80 character varying(320),
	campo_hemofilia_81 character varying(320),
	campo_hemofilia_82 character varying(320),
	campo_hemofilia_83 character varying(320),
	campo_hemofilia_84 character varying(320),
	campo_hemofilia_85 character varying(320),
	campo_hemofilia_86 character varying(320),
	campo_hemofilia_87 character varying(320),
	campo_hemofilia_88 character varying(320),
	campo_hemofilia_89 character varying(320),
	campo_hemofilia_90 character varying(320),
	campo_hemofilia_91 character varying(320),
	campo_hemofilia_92 character varying(320),
	campo_hemofilia_93 character varying(320),
	campo_hemofilia_94 character varying(320),
	campo_hemofilia_95 character varying(320),
	periodo_reportado character varying(320),
	fecha_de_corte date,
	resultado_auditoria character varying(320),
	fecha_final_auditoria date,
	tipo_cohorte_afiliado int,
	PRIMARY KEY(consecutivo_registro, periodo_reportado, fecha_de_corte, resultado_auditoria, fecha_final_auditoria, tipo_cohorte_afiliado)
);

--tabla_auditoria_usuarios_reportados_hemofilia
--consecutivo_registro, periodo_reportado, fecha_de_corte, resultado_auditoria, fecha_final_auditoria, tipo_cohorte_afiliado
--consecutivo_registro,campo_hemofilia_0,campo_hemofilia_1,campo_hemofilia_2,campo_hemofilia_3,campo_hemofilia_4,campo_hemofilia_5,campo_hemofilia_6,campo_hemofilia_7,campo_hemofilia_8,campo_hemofilia_9,campo_hemofilia_10,campo_hemofilia_11,campo_hemofilia_12,campo_hemofilia_13,campo_hemofilia_14,campo_hemofilia_15,campo_hemofilia_16,campo_hemofilia_17,campo_hemofilia_18,campo_hemofilia_19,campo_hemofilia_20,campo_hemofilia_21,campo_hemofilia_22,campo_hemofilia_23,campo_hemofilia_24,campo_hemofilia_25,campo_hemofilia_26,campo_hemofilia_27,campo_hemofilia_28,campo_hemofilia_29,campo_hemofilia_30,campo_hemofilia_31,campo_hemofilia_32,campo_hemofilia_33,campo_hemofilia_34,campo_hemofilia_35,campo_hemofilia_36,campo_hemofilia_37,campo_hemofilia_38,campo_hemofilia_39,campo_hemofilia_40,campo_hemofilia_41,campo_hemofilia_42,campo_hemofilia_43,campo_hemofilia_44,campo_hemofilia_45,campo_hemofilia_46,campo_hemofilia_47,campo_hemofilia_48,campo_hemofilia_49,campo_hemofilia_50,campo_hemofilia_51,campo_hemofilia_52,campo_hemofilia_53,campo_hemofilia_54,campo_hemofilia_55,campo_hemofilia_56,campo_hemofilia_57,campo_hemofilia_58,campo_hemofilia_59,campo_hemofilia_60,campo_hemofilia_61,campo_hemofilia_62,campo_hemofilia_63,campo_hemofilia_64,campo_hemofilia_65,campo_hemofilia_66,campo_hemofilia_67,campo_hemofilia_68,campo_hemofilia_69,campo_hemofilia_70,campo_hemofilia_71,campo_hemofilia_72,campo_hemofilia_73,campo_hemofilia_74,campo_hemofilia_75,campo_hemofilia_76,campo_hemofilia_77,campo_hemofilia_78,campo_hemofilia_79,campo_hemofilia_80,campo_hemofilia_81,campo_hemofilia_82,campo_hemofilia_83,campo_hemofilia_84,campo_hemofilia_85,campo_hemofilia_86,campo_hemofilia_87,campo_hemofilia_88,campo_hemofilia_89,campo_hemofilia_90,campo_hemofilia_91,campo_hemofilia_92,campo_hemofilia_93,campo_hemofilia_94,campo_hemofilia_95,periodo_reportado,fecha_de_corte,resultado_auditoria,fecha_final_auditoria,tipo_cohorte_afiliado 

CREATE TABLE tabla_auditoria_tipo_cohortes
(
	codigo int,
	descripcion character varying(320),
	PRIMARY KEY(codigo)
);

INSERT INTO tabla_auditoria_tipo_cohortes (codigo, descripcion) VALUES ('1','Hemofilia Nuevo');
INSERT INTO tabla_auditoria_tipo_cohortes (codigo, descripcion) VALUES ('2','Hemofilia Anterior');
INSERT INTO tabla_auditoria_tipo_cohortes (codigo, descripcion) VALUES ('3','Otra Cougalopatia Nueva');
INSERT INTO tabla_auditoria_tipo_cohortes (codigo, descripcion) VALUES ('4','Otra Cougalopatia Anterior');
INSERT INTO tabla_auditoria_tipo_cohortes (codigo, descripcion) VALUES ('5','Cambio de DX o Severidad');


CREATE TABLE tabla_auditoria_calificacion_de_soportes
(
	codigo character varying(4),
	descripcion character varying(320),
	PRIMARY KEY(codigo)
);

INSERT INTO tabla_auditoria_calificacion_de_soportes (codigo, descripcion) VALUES ('DC','Dato Conforme');
INSERT INTO tabla_auditoria_calificacion_de_soportes (codigo, descripcion) VALUES ('DNC','Dato Conforme');
INSERT INTO tabla_auditoria_calificacion_de_soportes (codigo, descripcion) VALUES ('DOND','Dato Conforme');



CREATE TABLE tabla_auditoria_descripcion_campos_hf
(
	numero_orden_campo character varying(320),
	numero_campo_norma character varying(320),
	descripcion_campo character varying(320),
	PRIMARY KEY(numero_orden_campo)
);

ALTER TABLE tabla_auditoria_usuarios_reportados_hemofilia add constraint  tabla_auditoria_usuarios_reportados_hemofilia_fkey FOREIGN KEY (resultado_auditoria) REFERENCES tabla_auditoria_calificacion_de_soportes(codigo);
ALTER TABLE tabla_auditoria_usuarios_reportados_hemofilia add constraint  tabla_auditoria_usuarios_reportados_hemofilia_fkey2 FOREIGN KEY (tipo_cohorte_afiliado) REFERENCES tabla_auditoria_tipo_cohortes(codigo);

/*
drop table para_auditoria_usuarios_reportados_hemofilia;
drop table para_auditoria_calificacion_de_soportes;
drop table para_auditoria_tipo_cohortes;
drop table para_auditoria_descripcion_campos_hf;
*/

/*
drop table tabla_auditoria_calificacion_de_soportes;
drop table tabla_auditoria_tipo_cohortes;
drop table tabla_auditoria_descripcion_campos_hf;
drop table tabla_auditoria_usuarios_reportados_hemofilia;
*/

INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('230','171','Auditoria','',FALSE,'..|audsoporclinicos_HF|audsoporclinicos_HF.php','50.01');

INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('231','13','Auditoria','',FALSE,'..|audsoporclinicos_CANCER|audsoporclinicos_CANCER.php','50.01');


INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('232','186','Auditoria','',FALSE,'..|audsoporclinicos_AR|audsoporclinicos_AR.php','50.01');


INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('233','12','Auditoria','',FALSE,'..|audsoporclinicos_VIH|audsoporclinicos_VIH.php','50.01');


INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('234','131','Auditoria','',FALSE,'..|audsoporclinicos_ERC|audsoporclinicos_ERC.php','50.01');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('230','5'); --admin sistema

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('231','5'); --admin sistema

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('232','5'); --admin sistema

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('233','5'); --admin sistema

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('234','5'); --admin sistema
--parte perfil auditoria
INSERT INTO gios_perfiles_sistema VALUES (14, 'Auditoria', 'NO');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('69','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('68','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('109','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('171','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('13','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('186','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('12','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('131','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('230','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('231','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('232','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('233','14'); --auditor
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('234','14'); --auditor

--cambio restriccion primary key para permitir un usuario con tipo y numero identificacion y entidad tener varios login con diferentes perfiles

alter table gioss_entidad_nicklogueo_perfil_estado_persona drop constraint gioss_entidad_nicklogueo_perfil_estado_persona_pkey;
alter table gioss_entidad_nicklogueo_perfil_estado_persona add constraint gioss_entidad_nicklogueo_perfil_estado_persona_pkey PRIMARY KEY (entidad, tipo_id, identificacion_usuario,perfil_asociado);

--nuevo usuario

INSERT INTO gioss_entidad_nicklogueo_perfil_estado_persona VALUES ('EMP028', 'Auditor_1', 'CC', '1024488857', 14, 1, 'jdmejia2009@gmail.com', '2018-01-05', '2019-01-05', '2018-01-11', 'omega002', '12:00:49');