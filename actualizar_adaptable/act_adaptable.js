tday  =new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
tmonth=new Array("January","February","March","April","May","June","July","August","September","October","November","December");

function GetClock()
{
d = new Date();
nday   = d.getDay();
nmonth = d.getMonth();
ndate  = d.getDate();
nyear = d.getYear();
nhour  = d.getHours();
nmin   = d.getMinutes();
nsec   = d.getSeconds();

if(nyear<1000) nyear=nyear+1900;

     if(nhour ==  0) {ap = " AM";nhour = 12;} 
else if(nhour <= 11) {ap = " AM";} 
else if(nhour == 12) {ap = " PM";} 
else if(nhour >= 13) {ap = " PM";nhour -= 12;}

if(nmin <= 9) {nmin = "0" +nmin;}
if(nsec <= 9) {nsec = "0" +nsec;}


//document.getElementById('clockbox').innerHTML=""+tday[nday]+", "+tmonth[nmonth]+" "+ndate+", "+nyear+" "+nhour+":"+nmin+":"+nsec+ap+"";
document.getElementById('clockbox').innerHTML=""+nhour+":"+nmin+":"+nsec+ap+"";
setTimeout("GetClock()", 1000);
}//fin if funcion obtener reloj
window.onload=GetClock;

function prediligenciar_datos_tabla_seleccionada_para_cargar(valor_seleccionado)
{
	if(valor_seleccionado=='0')
	{
		document.getElementById('nombre_tabla').value="";
		document.getElementById('llaves').value="";
		document.getElementById('nombres_columnas_tablas').value="";
	}
	else if(valor_seleccionado=='1')
	{
		document.getElementById('nombre_tabla').value="gioss_codigo_medicamentos";
		document.getElementById('llaves').value="cod_atc,codigo_cum,codigo_cum_2,codigo_cum_3";
		document.getElementById('nombres_columnas_tablas').value="cod_atc,descripciopn_atc,principio_activo_medicamento,registro_sanitario,estado_registro,estado_cum,codigo_cum_con_guion,cod_concentracion,unidad_medida,cantidad,unidad_referencia,forma_farmaceutica,codigo_cum,codigo_cum_2,codigo_cum_3";
	}
	else if(valor_seleccionado=='1.5')
	{
		document.getElementById('nombre_tabla').value="gioss_codigo_medicamentos";
		document.getElementById('llaves').value="cod_atc,codigo_cum,codigo_cum_2,codigo_cum_3";
		document.getElementById('nombres_columnas_tablas').value="cod_atc,descripciopn_atc,principio_activo_medicamento,registro_sanitario,estado_registro,estado_cum,cod_concentracion,unidad_medida,forma_farmaceutica,codigo_cum,codigo_cum_2,codigo_cum_3";
	}
	else if(valor_seleccionado=='2')
	{
		document.getElementById('nombre_tabla').value="gioss_cups";
		document.getElementById('llaves').value="codigo_procedimiento";
		document.getElementById('nombres_columnas_tablas').value="codigo_procedimiento, descripcion_procedimiento, codigo_sistema_cups, codigo_ambito_rips, codigo_sexo_cups, tipo_rips_cups, codigo_nivel_atencion, codigo_frecuencia_cups, codigo_estancia_cups, codigo_grupo_cups, norma_reguladora, codigo_sugrupo_cups, codigo_categoria_cups";
	}
	else if(valor_seleccionado=='3')
	{
		document.getElementById('nombre_tabla').value="gioss_grupo_procedimientos_cups";
		document.getElementById('llaves').value="codigo_grupo_cups";
		document.getElementById('nombres_columnas_tablas').value="codigo_grupo_cups,descripcion_grupos_de_cups";
	}
	if(valor_seleccionado=='4')
	{
		document.getElementById('nombre_tabla').value="gioss_subgrupo_procedimientos_cups";
		document.getElementById('llaves').value="codigo_subgrupo_cups";
		document.getElementById('nombres_columnas_tablas').value="codigo_subgrupo_cups,descripcion_subgrupo_cups";
	}
	if(valor_seleccionado=='5')
	{
		document.getElementById('nombre_tabla').value="gioss_categoria_procedimientos_cups";
		document.getElementById('llaves').value="codigo_categoria_cups";
		document.getElementById('nombres_columnas_tablas').value="codigo_categoria_cups,descripcion_categoria_cups";
	}
	if(valor_seleccionado=='6')
	{
		document.getElementById('nombre_tabla').value="gioss_grupos_sistemas_cups";
		document.getElementById('llaves').value="cod_sistemas_cups";
		document.getElementById('nombres_columnas_tablas').value="cod_sistemas_cups,descripcion_sistemas_cups";
	}
	if(valor_seleccionado=='7')
	{
		document.getElementById('nombre_tabla').value="gioss_afiliados_eapb_mp";
		document.getElementById('llaves').value="codigo_eapb, tipo_id_afiliado, id_afiliado";
		document.getElementById('nombres_columnas_tablas').value="codigo_eapb,tipo_id_eapb,numero_id_eapb,tipo_id_afiliado,id_afiliado,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,codigo_tipo_regimen,cod_tipo_afiliado,cod_tipo_cotizante,cod_estado_afiliado,cod_ocupacion,sexo,fecha_nacimiento,cod_mpio,cod_dpto,cod_sucursal,nombre_sucursal,nombre_regional";
	}
	if(valor_seleccionado=='7.5')
	{
		document.getElementById('nombre_tabla').value="gioss_afiliados_eapb_mp";
		document.getElementById('llaves').value="codigo_eapb, tipo_id_afiliado, id_afiliado";
		document.getElementById('nombres_columnas_tablas').value="codigo_eapb,tipo_id_eapb,numero_id_eapb,tipo_id_afiliado,id_afiliado,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,codigo_tipo_regimen,cod_tipo_afiliado,cod_tipo_cotizante,cod_estado_afiliado,cod_ocupacion,sexo,fecha_nacimiento,cod_mpio,cod_dpto,fecha_ultima_actualizacion,cod_zona,cod_sucursal,nombre_sucursal,nombre_regional";
	}
	if(valor_seleccionado=='7.7')
	{
		document.getElementById('nombre_tabla').value="gioss_afiliados_eapb_mp";
		document.getElementById('llaves').value="codigo_eapb, tipo_id_afiliado, id_afiliado";
		document.getElementById('nombres_columnas_tablas').value="codigo_eapb,tipo_id_eapb,numero_id_eapb,tipo_id_afiliado,id_afiliado,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,sexo,fecha_nacimiento";
	}
	if(valor_seleccionado=='8')
	{
		document.getElementById('nombre_tabla').value="valores_permitidos_4505";
		document.getElementById('llaves').value="numero_campo_norma";
		document.getElementById('nombres_columnas_tablas').value="numero_campo_norma,nombre_campo,lista_valores_permitidos";
	}
	if(valor_seleccionado=='9')
	{
		document.getElementById('nombre_tabla').value="gios_prestador_servicios_salud";
		document.getElementById('llaves').value="cod_registro_especial_pss";
		document.getElementById('nombres_columnas_tablas').value="cod_tipo_identificacion,num_tipo_identificacion,cod_registro_especial_pss,nom_entidad_prestadora,des_representante_legal,cod_municipio,des_direccion,des_telefono,txt_correo_contacto,clase_prestador,cod_tipo_entidad,cod_naturaleza_juridica,cod_tipo_cobertura,num_sede_ips,digito_verificacion,nombre_comercial_prestador,zona,cod_nivel_atencion,cod_depto,ese,sede_principal";
	}
	if(valor_seleccionado=='10')
	{
		document.getElementById('nombre_tabla').value="gioss_entidades_sector_salud";
		document.getElementById('llaves').value="codigo_entidad";
		document.getElementById('nombres_columnas_tablas').value="cod_tipo_entidad,codigo_entidad,nombre_de_la_entidad,codigo_dpto,cod_mpio,des_tipo_entidad_salud,numero_identificacion,digito_verificacion";
	}
	if(valor_seleccionado=='11')
	{
		document.getElementById('nombre_tabla').value="gioss_archivo_para_analisis_4505";
		document.getElementById('llaves').value="cod_prestador_servicios_salud, codigo_eapb, fecha_inicio_periodo, fecha_de_corte, numero_fila, nombre_archivo, fecha_y_hora_validacion";
		document.getElementById('nombres_columnas_tablas').value="campo0-campo118,CodigoIPS;CodigoEAPB;FechaInicial20XX-XX-XX;FechaFinal20XX-XX-XX;FechaHoraVal20XX-XX-XX XX:XX:XX;SGD280RPED20XXXXXXNI000000000000O01.txt;X";
	}
}//fin function

function download_archivo(ruta)
{	
	window.open(ruta,'Download');
}