<?php

require_once 'clase_coneccion_bd.php';

class Utilidades {

    public function __construct() {
        
    }
	
	 public function incrementarSecuencia($nombreSecuencia) {
        $obj = new conexion();
        $sql = "select nextval('$nombreSecuencia')";
        $resultado = $obj->consultar($sql);
        return $resultado[0];
    }
	
    public function obtenerSecuencia($nombreSecuencia) {
        $obj = new conexion();
        $sql = "select nextval('$nombreSecuencia')";
        $resultado = $obj->consultar($sql);
        return $resultado[0];
    }
	
	public function obtenerSecuenciaActual($nombreSecuencia) {
        $obj = new conexion();
        $sql = "select last_value from  ".$nombreSecuencia.";";
        $resultado = $obj->consultar($sql);
        return $resultado[0];
    }
	
	public function cambiarValorSecuencia($nombreSecuencia,$valor) {
        $obj = new conexion();
        $sql = "ALTER SEQUENCE ".$nombreSecuencia." RESTART WITH ".$valor.";";
        $resultado = $obj->consultar($sql);
        return $resultado[0];
    }

    public function obtenerSecuenciaIntegracion($nombreSecuencia) {
        $obj = new conexion();
        $sql = "select nextval('$nombreSecuencia')";
        $resultado = $obj->consultar($sql);
        return $resultado[0];
    }

    public function obtenerTiposDocumentosRips() {
        $obj = new conexion();
        $sql = "select * from avs_tipo_archivo where cod_tipo_validacion ='1' order by seq_lectura";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerTiposDocumentoPYP() {

        $obj = new conexion();
        $sql = "select * from avs_tipo_archivo where cod_tipo_validacion ='2' order by seq_lectura";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerEPS() {

        $obj = new conexion();
        $sql = "select * from avs_entidad_administradora ;";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerSeqEPS($numEntidad) {

        $obj = new conexion();
        $sql = "select seq_entidad_administradora from avs_entidad_administradora
                where num_tipo_identificacion ='" . $numEntidad . "'";

        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerEPS2($seqEps) {
        $obj = new conexion();
        $sql = "select * from gioss_entidades_sector_salud
                where codigo_entidad ='" . $seqEps . "'";

        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerEntidadPrestadora($id) {
        $obj = new conexion();
        $sql = "select * from avs_entidad_prestadora where num_tipo_identificacion ='" . $id . "'";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerEntidadAdministradora($id) {

        $obj = new conexion();
        $sql = "select seq_entidad_administradora from avs_entidad_administradora where num_tipo_identificacion = '" . $id . "' ;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerIPSActiva($codEntidad) {

        $obj = new conexion();
        $sql = "select * from avs_sede_ips_habilitada where seq_entidad_prestadora =" . $codEntidad . "";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obTenerDatos($usuario) {

        $obj = new conexion();

        $sql2 = "SELECT avs_usuario_sistema.seq_usuario,
  						  avs_institucion.cod_registro_especial_pss
 				   FROM
						  public.avs_usuario_sistema,
						  public.avs_institucion
					WHERE
						  avs_usuario_sistema.lgn_usuario='" . $usuario . "'and
 						  avs_usuario_sistema.seq_institucion = avs_institucion.seq_institucion ;";

        $resultado = $obj->consultar($sql2);

        return $resultado;
    }

    public function obtenerTipoValidacion() {

        $obj = new conexion();

        $sql3 = "select * from public.gioss_tipo_validacion;";

        $resultado = $obj->consultar2($sql3);

        return $resultado;
    }

    public function obtenerPeriodo($fecha) {

        $obj = new conexion();

        $sql = "select * from gioss_periodo_informacion
                where   gioss_periodo_informacion.fec_inicio_periodo  <= '" . $fecha . "'
                and cod_tipo_validacion='1'";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerPeriodo2($fecha) {

        $obj = new conexion();

        $sql = "select * from gioss_periodo_informacion
                where   gioss_periodo_informacion.fec_inicio_periodo  <= '" . $fecha . "'
                 and cod_tipo_validacion='2' and flg_activo = 'S' order by nom_periodo_informacion asc";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function consultaNroRemision($longNumeroRemision, $ips, $codPeriodo) {

        $obj = new conexion();
        $sql = "select * from avs_archivos_validados where nro_remision ='" . $longNumeroRemision . "' 
                         and cod_registro_especial_pss='" . $ips . "' 
                         and nro_periodo_reportado='" . $codPeriodo . "'";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function consultaNroRemision2($longNumeroRemision, $ips, $year) {

        $obj = new conexion();
        $sql = "select seq_cargue_archivo_obligatorio 
                       from avs_cargue_archivo_obligatorio where nro_remision ='" . $longNumeroRemision . "' 
                       and cod_registro_especial_pss='" . $ips . "' 
                       and fec_remision between '" . $year . "-01-01' and '" . $year . "-12-31'";

        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function consultaNroRemision3($longNumeroRemision, $ips, $codPeriodo) {

        $obj = new conexion();
        $sql = "select seq_cargue_archivo_obligatorio 
                       from avs_cargue_archivo_obligatorio where nro_remision ='" . $longNumeroRemision . "' 
                       and cod_registro_especial_pss='" . $ips . "' 
                       and nro_periodo_reportado='" . $codPeriodo . "'";


        $resultado = $obj->consultar2($sql);
        return $resultado;
    }
    
    public function consultaNroRemision4($seqCargue){
        
         $obj = new conexion();
        $sql = "select nro_remision 
                      from avs_cargue_archivo_obligatorio where seq_cargue_archivo_obligatorio = ".$seqCargue." ";
        $resultado = $obj->consultar2($sql);
        return $resultado;
        
        
    }

    public function borradoRegistros($seqCargue) {


        $obj = new conexion();

        $sql1 = "delete from avs_inconsistencia_archivo_fila where seq_cargue_archivo_obligatorio=" . $seqCargue . " ";
        $obj->borrar($sql1);

        $sql2 = "delete from avs_detalle_archivo_obligatorio where seq_cargue_archivo_obligatorio=" . $seqCargue . "";
        $obj->borrar($sql2);

        $sql3 = "delete from avs_cargue_archivo_obligatorio where seq_cargue_archivo_obligatorio=" . $seqCargue . "";
        $obj->borrar($sql3);
    }

    public function obtenerIPS($codEntidad) {

        $obj = new conexion();
        $sql = "select seq_ from avs_entidad_prestadora where seq_entidad_prestadora='" . $codEntidad . "'";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerRedEpsIps($seqIPS) {

        $obj = new conexion();

        $sql = "select avs_entidad_administradora.nom_entidad_administradora, avs_entidad_administradora.seq_entidad_administradora from avs_red_ips_eapb, avs_entidad_administradora 
                where avs_red_ips_eapb.seq_entidad_prestadora ='" . $seqIPS . "' and
                avs_red_ips_eapb.seq_entidad_administradora = avs_entidad_administradora.seq_entidad_administradora";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerRedEpsIps2($seqEPS) {

        $obj = new conexion();

        $sql = "select avs_entidad_prestadora.nom_entidad_prestadora, avs_entidad_prestadora.seq_entidad_prestadora from avs_red_ips_eapb, avs_entidad_prestadora 
                where avs_red_ips_eapb.seq_entidad_administradora ='" . $seqEPS . "'
                and   avs_red_ips_eapb.seq_entidad_prestadora = avs_entidad_prestadora.seq_entidad_prestadora order by avs_entidad_prestadora.nom_entidad_prestadora ";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerFechaFinPeriodo($idPer)
    {

        $obj = new conexion();

        $sql = "select fec_final_periodo from gioss_periodo_informacion where cod_periodo_informacion = '" . $idPer . "' and cod_tipo_validacion='2'";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }
    
    public function obtenerFechaFinPeriodoMensual4505($idPer)
    {

        $obj = new conexion();

        $sql = "select fec_final_periodo from gioss_periodo_informacion_4505_mensual where cod_periodo_informacion = '" . $idPer . "' and cod_tipo_validacion='2'";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerTipoIdEntidad($codTipoIdEntidadPrestadora) {

        $obj = new conexion();

        $sql = "select * from avs_tipo_identificacion where cod_tipo_identificacion =" . $codTipoIdEntidadPrestadora . "";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerNroRemisionMax($codRegEspecial, $seqEntidad, $codPeriodo) {

        $obj = new conexion();

        $sql = "select max(nro_remision) from avs_cargue_archivo_obligatorio where cod_registro_especial_pss='" . $codRegEspecial . "' 
                and seq_entidad_administradora= '" . $seqEntidad . "' and nro_periodo_reportado= '" . $codPeriodo . "'";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerNombreArchivo($seqCargue) {

        $obj = new conexion();

        $sql = "select * from avs_detalle_archivo_obligatorio where seq_cargue_archivo_obligatorio='" . $seqCargue . "'";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function consultarIntegracion() {

        $obj = new conexion();

        $sql = "select * from avs_tipo_integracion";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerPeriodos() {

        $obj = new conexion();

        //$sql = "select cod_periodo_informacion, nom_periodo_informacion from gioss_periodo_informacion";

        $sql = "select * from gioss_periodo_informacion
                 where   
                 cod_tipo_validacion='2' order by nom_periodo_informacion";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerPeriodos2() {

        $obj = new conexion();

        //$sql = "select cod_periodo_informacion, nom_periodo_informacion from gioss_periodo_informacion";

        $sql = "select * from gioss_periodo_informacion
                where   
                 cod_tipo_validacion='1'";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function confirmarExistenciaMail($mail) {

        $obj = new conexion();
        $mail = "'" . $mail . "'";

        $sql = 'select sgd."SGD_USUARIOS".clave_usuario
                      from sgd."SGD_USUARIOS" 
                      where sgd."SGD_USUARIOS".correo_usuario=' . $mail . ' ';

        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function confirmarExistenciaNombreUsuario($user) {

        $obj = new conexion();

        $user = "'" . $user . "'";

        $sql = 'select public."SGD_USUARIOS".clave_usuario,public."SGD_USUARIOS".correo_usuario
                      from public."SGD_USUARIOS" 
                      where public."SGD_USUARIOS".nombre_usuario =' . $user . ' ';

        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obetenerCodEntidadAdministradotabyNumtipoId($id) {

        $obj = new conexion();
        $sql = "select cod_entidad_administradora from avs_entidad_administradora where num_tipo_identificacion = '" . $id . "'  ";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerIPSporLetra($letra, $seqEPS) {

        $obj = new conexion();

        $sql = "select avs_entidad_prestadora.nom_entidad_prestadora, avs_entidad_prestadora.seq_entidad_prestadora from avs_red_ips_eapb, avs_entidad_prestadora 
                where avs_red_ips_eapb.seq_entidad_administradora ='" . $seqEPS . "'
                and avs_red_ips_eapb.seq_entidad_prestadora = avs_entidad_prestadora.seq_entidad_prestadora
                and avs_entidad_prestadora.nom_entidad_prestadora LIKE '%$letra%' ;";

        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerCentroPobladoPorLetra($letra) {

        $obj = new conexion();

        $sql = "select cod_centro_poblado,nom_centro_poblado from avs_centro_poblado where nom_centro_poblado LIKE '" % $letra % "';";

        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerPeriodosIntegracion() {

        $obj = new conexion();
        $sql = "select cod_periodo_informacion, nom_periodo_informacion from gioss_periodo_informacion where cod_tipo_validacion='3' ;";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerAreaTematica() {

        $obj = new conexion();
        $sql = "select cod_area_tematica, nom_area_tematica from avs_area_tematica order by nom_area_tematica;";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerTipoIndicador() {

        $obj = new conexion();
        $sql = "select cod_tipo_indicador,nom_tipo_indicador from avs_tipo_indicador order by nom_tipo_indicador; ";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerEstado() {

        $obj = new conexion();
        $sql = "select cod_estado_registro,nom_estado_registro from avs_estado_registro order by nom_estado_registro; ";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerTipoRegimen() {

        $obj = new conexion();
        $sql = "select cod_tipo_regimen,nom_tipo_regimen from avs_tipo_regimen_sgsss order by nom_tipo_regimen; ";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    public function obtenerPeriocidadMedicion() {

        $obj = new conexion();
        $sql = "select cod_periodicidad_medicion,nom_periodicidad_medicion from avs_periodicidad_medicion order by nom_periodicidad_medicion;";
        $resultado = $obj->consultar2($sql);
        return $resultado;
    }

    //=============================================================================
    public function tipoInstituciones() {

        $obj = new conexion();

        $sql = "select avs_tipo_institucion.cod_tipo_institucion,
                                    avs_tipo_institucion .nom_tipo_institucion
                       from 
                       public.avs_tipo_institucion  ; ";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function tipoIdentificacion() {

        $obj = new conexion();

        $sql = " select avs_tipo_identificacion_prestador.cod_tipo_identificacion,
                                     avs_tipo_identificacion_prestador.nom_tipo_identificacion
                                     from public.avs_tipo_identificacion_prestador ;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerSecuencia2($nombreSecuencia) {

        $obj = new conexion();

        $sql = "select nextval('$nombreSecuencia')";

//      echo "<br>";  echo $sql ; echo "<br>"; die();
        $resultado = $obj->consultar($sql);

        return $resultado[0]['nextval'];
    }

    //=============================================================================


    public function obetenerModulos() {

        $obj = new conexion();

        $sql = " select cod_modulo_solucion,des_modulo_solucion from avs_modulo_solucion;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerSubModulos() {

        $obj = new conexion();

        $sql = " select cod_submodulo_solucion,des_submodulo_solucion from avs_submodulo_solucion;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerTipoSolicitud() {

        $obj = new conexion();

        $sql = " select cod_tipo_solicitud,des_tipo_solicitud from avs_tipo_solicitud;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerNivelAtencion() {

        $obj = new conexion();

        $sql = " select cod_nivel_atencion,nom_nivel_atencion from avs_nivel_atencion;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerNaturalezaJuridica() {

        $obj = new conexion();

        $sql = " select cod_naturaleza_juridica,nom_naturaleza_juridica from avs_naturaleza_juridica;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerTipoCobertura() {

        $obj = new conexion();

        $sql = " select cod_tipo_cobertura,nom_tipo_cobertura from avs_tipo_cobertura;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerTipoEntidad() {

        $obj = new conexion();

        $sql = " select cod_tipo_entidad,nom_tipo_entidad from avs_tipo_entidad;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }

    public function obtenerCentroPoblado() {

        $obj = new conexion();

        $sql = " select cod_centro_poblado,nom_centro_poblado from avs_centro_poblado;";

        $resultado = $obj->consultar2($sql);

        return $resultado;
    }
    
 
    public function __destruct() {
        
    }

}

?>
