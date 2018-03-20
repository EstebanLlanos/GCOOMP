<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap_4.0/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="formulario_auditoria_HF.css?=2.2" rel="stylesheet" />
        
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-3.3.1.js"> </script> 
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-3.3.1.min.js"> </script> 
        <script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="formulario_auditoria_HF.js?v=2.0"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap_4.0/bootstrap.bundle.js"></script>
        <script type="text/javascript" src="../librerias_externas/fontawesome-all.js"></script>
        <script type="text/javascript" src="../librerias_externas/Simple-Flexible-jQuery-Tree-Grid-Plugin-TreeGrid/js/jquery.treegrid.js"></script>

        <title>Auditoria Hemofilia Contra Soportes Clinicos</title>
        <link rel="icon" href="../assets/imagenes/logo_gioss_fav.ico" />
        <link rel="stylesheet" href="../librerias_externas/Simple-Flexible-jQuery-Tree-Grid-Plugin-TreeGrid/css/jquery.treegrid.css">
    </head>

    <body id="cuerpo_pagina">

        <!-- SECCIÓN DE ALERTS PARA EL DSPLPIEGUE DE MENSAJES DE: ÉXITO, ERROR E INFORMACIÓN  -->

        <div id="alert_group" class="form-group alert_group">

            <div id="success_alert" class="alert alert-success d-none" role="alert">
                <i class="fas fa-clipboard-check fa-5x"></i>
                <hr>
                <p><strong>¡Información Guardada!</strong> <br> La información de auditoría ha sido guardada exitosamente.</p>
            </div>

            <div id="danger_alert" class="alert alert-danger d-none" role="alert">
                <i class="fas fa-exclamation-triangle fa-5x"></i>
                <hr>
                <p><strong>¡Error al Guardar!</strong> <br> La información de auditoría no ha sido guardada correctamente. <br> 
                    <strong>Por favor, inténtelo de nuevo. </strong>
                </p>
            </div>

        </div>

        <div id="encabezado">
            
            <nav id="barra_de_navegacion" class="navbar navbar-expand-lg navbar-dark bg-dark">
              
              <span class="navbar-brand" href="#">
                <i id="aud_logo" class="fab fa-searchengin"></i> 
                <label id="aud_label"> <strong>PANEL DE AUDITORÍA HEMOFILIA</strong> </label> </span>

              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                  
                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Selección de Cohorte
                        <i id="select_logo" class="far fa-list-alt"></i>
                    </a>
                    <div id="menu_cohortes" class="dropdown-menu" aria-labelledby="navbarDropdown">
                      
                      <a id="hemofilia_nuevo" class="dropdown-item" href="#" data-toggle="modal" data-target="#modal_hemofilia_nuevos"><i class="fas fa-angle-double-right"></i> Hemofilia Nuevos</a>

                      <a id="hemofilia_anterior" class="dropdown-item" href="#"><i class="fas fa-angle-double-right"></i> Hemofilia Anterior</a>
                      
                      <div class="dropdown-divider"></div>
                      
                      <a id="coagulo_nuevo" class="dropdown-item" href="#"><i class="fas fa-angle-double-right"></i> Otras Coagulopatias Nuevos</a>
                      <a id="coagulo_anterior" class="dropdown-item" href="#"><i class="fas fa-angle-double-right"></i> Otras Coagulopatias Anterior</a>
                      
                      <div class="dropdown-divider"></div>

                      <a id="diagnostico_severidad" class="dropdown-item" href="#"><i class="fas fa-angle-double-right"></i> Cambio de Diagnóstico o Severidad</a>
                    
                    </div>

                  </li>

                </ul>
              </div>

              <span id="cohorte_actual" class="navbar-text"></span>
              <a id="btn_salida" class="btn btn-sm btn-outline-secondary" href="audsoporclinicos_HF.php" role="button">Salir del Panel de Auditoría <i class="fas fa-external-link-alt"></i></a>

            </nav>

        </div>

        <!-- SECCIÓN DE DESPLIEGUE DE MODALES.  -->

        <!-- MODAL LISTADO DE COHORTES PARA LA NORMA ACTUAL -->
        <div class="modal fade" id="modal_cohorte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog rounded" role="document">
            <div class="modal-content rounded">
              <div class="modal-header">
                <h5 class="modal-title sombra_texto" id="exampleModalLabel">SELECTOR DE COHORTE</h5>
              </div>
              <div class="modal-body">
                
                <div class="card">
                  <div class="card-header">
                    <strong>Seleccione a Continuación la Cohorte a Auditar</strong>
                  </div>
                  <ul class="list-group list-group-flush">
                    <button id="inicio_hemo_nuevos" class="btn btn-outline-info btn-sm"> Hemofilia Nuevos </button>
                    <button id="inicio_hemo_anteriores" class="btn btn-outline-info btn-sm"> Hemofilia Anteriores </button>
                    <button id="inicio_coagu_nuevos" class="btn btn-outline-info btn-sm"> Otras Coagulopatias Nuevos </button>
                    <button id="inicio_coagu_anteriores" class="btn btn-outline-info btn-sm"> Otras Coagulopatias Anterior </button>
                    <button id="inicio_diagnostico_severidad" class="btn btn-outline-info btn-sm"> Cambio de Diagnóstico o Severidad </button>
                  </ul>
                </div>

              </div>
              <div class="modal-footer">
                <a class="btn btn-dark btn-sm" href="audsoporclinicos_HF.php"> Salir del Panel de Auditoría </a>
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL LISTADO DE PACIENTES DE LA COHORTE SELECCIONADA -->
        <div class="modal fade" id="modal_pacientes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title sombra_texto" id="exampleModalLabel"> PACIENTES HEMOFILIA NUEVO </h5>
              </div>
              <div class="modal-body">

                <table class="table table-striped table-hover rounded">
                  <thead class="thead-dark">
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Nombres</th>
                      <th scope="col">Apellidos</th>
                      <th scope="col">Tipo de Identificación</th>
                      <th scope="col">Número de Identificación</th>
                      <th scope="col">Estado de Auditoría</th>
                      <th scope="col">  </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">1</th>
                      <td>Esteban Antonio</td>
                      <td>Llanos Millán</td>
                      <td>CC</td>
                      <td>1118258478</td>
                      <td> <span class="badge badge-danger">Sin Auditar</span> </td>
                      <td> <button id="iniciar_auditoria" class="btn btn-outline-danger btn-sm"> Auditar Paciente </button> </td>
                    </tr>
                    <tr>
                      <th scope="row">2</th>
                      <td>Juan Carlos</td>
                      <td>Martinez Gonzales</td>
                      <td>CC</td>
                      <td>12345678</td>
                      <td><span class="badge badge-success">Auditado</span></td>
                      <td>  </td>
                    </tr>
                    <tr>
                      <th scope="row">3</th>
                      <td>Juan David</td>
                      <td>Mejía Mena</td>
                      <td>CC</td>
                      <td>12345678</td>
                      <td> <span class="badge badge-primary">Auditoría en Proceso</span> </td>
                      <td> <button id="continuar_auditoria" class="btn btn-outline-primary btn-sm"> Continuar Auditoría </button> </td>
                    </tr>
                  </tbody>
                </table>

              </div>
            </div>
          </div>
        </div>

        <!-- MODAL AUDITORÍA DE ARCHIVO SELECCIONADO Y CUYO PROCESO NO HA INICIADO-->
        <div class="modal fade" id="modal_nuevo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title sombra_texto" id="exampleModalLabel"> EVALUACIÓN DE DOCUMENTOS SOPORTE </h5>
                <span aria-hidden="true" class="close">Consolidado01</span>
              </div>
              <div class="modal-body">

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id=""> <strong>Nombre de Carpeta </strong></span>
                    </div>
                    <input type="text" class="form-control" disabled="true" placeholder="CC1118258478">
                </div>

                <br><hr>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id=""><strong> Nombre de Archivo </strong></span>
                    </div>
                    <input type="text" class="form-control" disabled="true" placeholder="Consolidado01">
                </div>

                <br><hr>

                <div id="calificacion_aud_archivo" class="input-group mb-3">
                    <div class="input-group-prepend" style="height: 2.15em; font-family: 'Rajdhani';">
                        <label class="input-group-text" for="inputGroupSelect01"> <strong> Calificación </strong> </label>
                    </div>
                    <div id="selector_calificacion" class="dropdown-alt" style="width: 40%;">
                        <input class="dropdown-toggle-alt" type="text">
                        <div id="calificacion_archivo_selec" class="dropdown-text-alt" style="font-family: 'Rajdhani';">Seleccione una Opción...</div>
                        <ul id="elementos_calificacion" class="dropdown-content-alt" style="width: 100%; font-family: 'Rajdhani';">
                            <li><a id="calificacion_aud_archivo_1">Valor Auditoría 1</a></li>
                            <li><a id="calificacion_aud_archivo_2">Valor Auditoría 2</a></li>
                            <li><a id="calificacion_aud_archivo_3">Valor Auditoría 3</a></li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 150%;  font-family: 'Rajdhani';">
                        Descripción de la Calificación
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" rows="5" id="comment" style="font-family: 'Rajdhani';"></textarea>
                    </div>
                </div>

              </div>
              <div class="modal-footer">
                <div>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar Auditoría</button>
                  <button type="button" class="btn btn-success">Guardar Auditoría</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL AUDITORÍA DE ARCHIVO SELECCIONADO Y QUE ESTÁ EN PROCESO-->
        <div class="modal fade" id="modal_en_proceso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title sombra_texto" id="exampleModalLabel"> EVALUACIÓN DE DOCUMENTOS SOPORTE </h5>
                <span aria-hidden="true" class="close">Consolidado06</span>
              </div>
              <div class="modal-body">

                <div class="input-group" style="font-family: 'Rajdhani';">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id=""> <strong>Nombre de Carpeta</strong> </span>
                    </div>
                    <input type="text" class="form-control" disabled="true" placeholder="CC1118258478"> 
                </div>

                <br><hr>

                <div class="input-group" style="font-family: 'Rajdhani';">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id=""> <strong>Nombre de Archivo</strong> </span>
                    </div>
                    <input type="text" class="form-control" disabled="true" placeholder="Consolidado06">
                </div>

                <br><hr>

                <div class="input-group mb-3" style="width: 100%; padding-left: 30%; z-index: 150 !important; font-family: 'Rajdhani';">
                    <div class="input-group-prepend" style="height: 2.15em;">
                        <label class="input-group-text" for="inputGroupSelect01"> <strong>Calificación</strong> </label>
                    </div>
                    <div id="selector_calificacion" class="dropdown-alt" style="width: 40%;font-family: 'Rajdhani';">
                        <input class="dropdown-toggle-alt" type="text">
                        <div id="calificacion_archivo_selec" class="dropdown-text-alt" style="font-family: 'Rajdhani';">Seleccione una Opción...</div>
                        <ul id="elementos_calificacion" class="dropdown-content-alt" style="width: 100%; font-family: 'Rajdhani';">
                            <li><a id="calificacion_aud_archivo_1">Valor Auditoría 1</a></li>
                            <li><a id="calificacion_aud_archivo_2">Valor Auditoría 2</a></li>
                            <li><a id="calificacion_aud_archivo_3">Valor Auditoría 3</a></li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 150%;  font-family: 'Rajdhani';">
                        Descripción de la Calificación
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" rows="5" id="comment" style="font-family: 'Rajdhani';"> Este archivo tiene su proceso de auditoría en proceso. </textarea>
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                <div>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar Auditoría</button>
                  <button type="button" class="btn btn-success" >Guardar Auditoría</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="panel_auditoria">
            
            <div id="contenedor_archivos" class="row" style="height: 58vh">

                <div id="visor_archivos" class="col-md-8 col-sm-8">

                    <iframe src = "../librerias_externas/ViewerJS/#../../assets/pdf_files/manual.pdf" width='101.2%' height='100%' allowfullscreen webkitallowfullscreen>
                        
                    </iframe>

                </div>

                <div id="selector_archivos" class="col-md-4 col-sm-4" style="background-color: #99ffeb"><br>

                    <div id="contenedor_interno_archivos" style="background-color: #ccfff5">
                        <table class="tree table table-striped table-hover">
                            <tr class="treegrid-1" style="font-size: 17px;">
                                <td style="font-family: 'Sarpanch'"><i class="fas fa-folder-open"></i> <strong>CC1118258478</strong></td><td></td>
                            </tr>
                            
                            <tr class="treegrid-2 treegrid-parent-1" style="font-size: 15px; vertical-align: middle;">
                                <td style="font-family: 'Rajdhani';"><i class="far fa-file-pdf"></i> <strong>Consolidado01</strong></td>
                                
                                <td style="text-align: center; margin: auto;">
                                    <button class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#modal_nuevo" style="font-family: 'Lobster Two'; font-size: 17px;"> Auditar Archivo </button>
                                    <button class="btn btn-success btn-sm" style="font-family: 'Lobster Two'; font-size: 17px;"> Ver </button>
                                </td>
                            </tr>
                            
                            <tr class="treegrid-3 treegrid-parent-1">
                                <td style="font-family: 'Rajdhani';"><i class="far fa-file-pdf"></i> <strong>Consolidado02</strong></td>

                                <td style="text-align: center; margin: auto;">
                                    <button class="btn btn-outline-danger btn-sm" style="font-family: 'Lobster Two'; font-size: 17px;"> Auditar Archivo </button>
                                    <button class="btn btn-success btn-sm" style="font-family: 'Lobster Two'; font-size: 17px;"> Ver </button>
                                </td>
                            </tr>
                            
                            <tr class="treegrid-4 treegrid-parent-1">
                                <td style="font-family: 'Rajdhani';"><i class="far fa-file-pdf"></i> <strong>Consolidado03</strong></td>
                                
                                <td style="text-align: center; margin: auto;">
                                    <button class="btn btn btn-outline-success btn-sm" disabled font-family: style="font-family: 'Lobster Two'; font-size: 17px;"> Archivo Auditado </button>
                                    <button class="btn btn-success btn-sm" style="font-family: 'Lobster Two'; font-size: 17px;"> Ver </button>
                                </td>
                            </tr>

                            <tr class="treegrid-5 treegrid-parent-1">
                                <td style="font-family: 'Rajdhani';"><i class="far fa-file-pdf"></i> <strong>Consolidado04</strong></td>

                                <td style="text-align: center; margin: auto;">
                                    <button class="btn btn btn-outline-success btn-sm" disabled style="font-family: 'Lobster Two'; font-size: 17px;"> Archivo Auditado </button>
                                    <button class="btn btn-success btn-sm" style="font-family: 'Lobster Two'; font-size: 17px;"> Ver </button>
                                </td>
                            </tr>

                            <tr class="treegrid-6 treegrid-parent-1">
                                <td style="font-family: 'Rajdhani';"><i class="far fa-file-pdf"></i> <strong>Consolidado05</strong></td>

                                <td style="text-align: center; margin: auto;">
                                    <button class="btn btn-outline-danger btn-sm" style="font-family: 'Lobster Two'; font-size: 17px;"> Auditar Archivo </button>
                                    <button class="btn btn-success btn-sm" style="font-family: 'Lobster Two'; font-size: 17px;"> Ver </button>
                                </td>
                            </tr>

                            <tr class="treegrid-8 treegrid-parent-1">
                                <td style="font-family: 'Rajdhani';"><i class="far fa-file-pdf"></i> <strong>Consolidado06</strong></td>

                                <td style="text-align: center; margin: auto;">
                                    <button class="btn btn-outline-primary btn-sm" data-toggle="modal"  data-target="#modal_en_proceso" style="font-family: 'Lobster Two'; font-size: 17px;"> Auditoría en Proceso </button>
                                    <button class="btn btn-success btn-sm" style="font-family: 'Lobster Two'; font-size: 17px;"> Ver </button>
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>

            </div>

            <div id="contenedor_formulario" class="row text-center">

                <div id="contenedor_info_paciente" class="card text-center">
                    <div id="cabecera_info_paciente" class="card-header text-center bg-secondary sombra_texto">
                        <strong>INFORMACIÓN DEL PACIENTE</strong> 
                    </div>
                    <div class="card-body align-middle">

                        <table class="table table-inverse" style="font-family: 'Rajdhani';">
                            <thead style="font-family: 'Sarpanch';">
                                <tr>
                                    <th>#</th>
                                    <th>Tipo de Identificación</th>
                                    <th>Número de Identificación</th>
                                    <th>Número de Identificación BDUA</th>
                                    <th>Primer Nombre</th>
                                    <th>Segundo Nombre</th>
                                    <th>Primer Apellido</th>
                                    <th>Segundo Apellido</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="table-info">
                                    <th scope="row">1</th>
                                    <td>CC</td>
                                    <td>1.118.258.478</td>
                                    <td>........</td>
                                    <td>Esteban</td>
                                    <td>Antonio</td>
                                    <td>Llanos</td>
                                    <td>Millán</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="contenedor_campos_auditoria" style="background-color: #ccfff5;">
                    <div id="card_campos_auditoria" class="card text-center">

                        <div class="card-header" id="headingOne" style="background-color: #99ffeb">
                            <button id="campos_auditar_btn" class="btn btn-success collapsed sombra_texto" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="width: 100%; font-size: 130%; font-family: 'Rajdhani';">
                              <strong>CAMPOS PARA AUDITAR</strong>
                            </button>
                        </div>

                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#contenedor_campos_auditoria">

                            <div class="card-body" style="background-image: url('resources/turquoise-1540436_960_720.png'); background-size: cover; height: 41em">

                                <div class="row" style="padding-left: 3%">
                                    <div class="col-md-2 col-sm-2">
                                        <div class="list-group lista-campos" id="list-tab" role="tablist">
                                            <a class="list-group-item list-group-item-action active btn-outline-info" id="campo_1_list" data-toggle="list" href="#lista_campo1" role="tab" aria-controls="home" style="font-family: 'Rajdhani';">Nombre Campo 1 <span class="badge badge-pill badge-success" style="font-size: 15px">Auditado</span></a>
                                            <a class="list-group-item list-group-item-action btn-outline-info" id="list-profile-list" data-toggle="list" href="#lista_campo2" role="tab" aria-controls="profile" style="font-family: 'Rajdhani';">Nombre Campo 2 <span class="badge badge-pill badge-success" style="font-size: 15px">Auditado</span></a>
                                            <a class="list-group-item list-group-item-action btn-outline-info" id="list-messages-list" data-toggle="list" href="#lista_campo3" role="tab" aria-controls="messages" style="font-family: 'Rajdhani';">Nombre Campo 3 <span class="badge badge-pill badge-danger" style="font-size: 15px">Sin Auditar</span></a>
                                            <a class="list-group-item list-group-item-action btn-outline-info" id="list-settings-list" data-toggle="list" href="#lista_campo4" role="tab" aria-controls="settings" style="font-family: 'Rajdhani';">Nombre Campo 4 <span class="badge badge-pill badge-warning" style="font-size: 15px">Auditoría <br>en Proceso</span></a>
                                        </div>
                                    </div>

                                    <div class="col-md-10 col-sm-10">
                                        <div class="tab-content" id="nav-tabContent">
                                            
                                            <div class="tab-pane fade show active" id="lista_campo1" role="tabpanel" aria-labelledby="campo_1_list">

                                                <div class="row">
                                                    <div class="container col-md-4 col-sm-4" style="color: #42f1f4; max-width: 28%">
                                                        
                                                        <div class="row" style="margin: auto">

                                                          <div class="col-md-6 col-sm-6" style="font-family: 'Rajdhani'; text-align: left; color: black">
                                                            <label class="label label-success"><strong>N° Orden</strong></label>
                                                            <input type="text" class="form-control field-shader" disabled="true" placeholder="00">
                                                          </div>

                                                          <div class="col-md-6 col-sm-6" style="font-family: 'Rajdhani'; text-align: left; color: black">
                                                            <label class="label label-success"><strong>N° Campo</strong></label>
                                                            <input type="text" class="form-control field-shader" disabled="true" placeholder="01">
                                                          </div>

                                                        </div>

                                                        <hr>

                                                        <div class="input-group" style="font-family: 'Rajdhani';">
                                                          <div class="input-group-prepend field-shader">
                                                            <span class="input-group-text" id=""><strong>Valor Reportado</strong></span>
                                                          </div>
                                                          <input type="text" class="form-control field-shader" disabled="true" placeholder="11">
                                                        </div>

                                                        <hr>

                                                        <div class="card contenedor_detalles_auditoria" style="height: 55%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-family: 'Rajdhani';">
                                                                Descripción del Valor Reportado
                                                            </div>
                                                            <div class="card-body" style="height: 100%;">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';">Campo separado para descripción del Valor Registrado. </textarea>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_selector_archivo" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            Nombre de Archivo PDF
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="selector_archivo" class="dropdown-menu">
                                                            <h4 class="dropdown-header">Seleccione el Archivo de Soporte</h4>
                                                            <a class="dropdown-item">Consolidado01</a>
                                                            <a class="dropdown-item">Consolidado02</a>
                                                            <a class="dropdown-item">Consolidado03</a>
                                                            <a class="dropdown-item">Consolidado04</a>
                                                            <a class="dropdown-item">Consolidado05</a>
                                                            <a class="dropdown-item">Consolidado06</a>
                                                          </div>
                                                        </div>

                                                    </div>

                                                    <div class="container col-md-4 col-sm-4" style="color: #42f1f4; max-width: 28%;">

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Número de Página</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_modifica_dato" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold; overflow-x: hidden">
                                                            <strong>¿Se modifica el Dato original?</strong>
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="modifica_dato" class="dropdown-menu">
                                                            <h4 class="dropdown-header">¿Se Modifica el Dato Original?</h4>
                                                            <a class="dropdown-item">Si</a>
                                                            <a class="dropdown-item">No</a>
                                                          </div>
                                                        </div>

                                                        <hr>

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Valor Sugerido</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_modifica_archivo_original" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            <strong>Soporte de Modificación</strong>
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="modifica_archivo_original" class="dropdown-menu">
                                                            <h4 class="dropdown-header">Seleccione el Archivo que Soporta la Modificación</h4>
                                                            <a class="dropdown-item">Consolidado01</a>
                                                            <a class="dropdown-item">Consolidado02</a>
                                                            <a class="dropdown-item">Consolidado03</a>
                                                            <a class="dropdown-item">Consolidado04</a>
                                                            <a class="dropdown-item">Consolidado05</a>
                                                            <a class="dropdown-item">Consolidado06</a>
                                                          </div>
                                                        </div>

                                                        <hr>

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Número de Página</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                    </div>

                                                    <div class="container col-md-4 col-sm-4">
                                                        <div class="card contenedor_detalles_auditoria" style="height: 45%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 120%; font-family: 'Rajdhani';">
                                                                Descripción de Motivo de Dato No Conforme
                                                            </div>
                                                            <div class="card-body" style="height: 40%">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';"> Aquí va escrita la descripción del motivo por el cuál el dato es no conforme. </textarea>
                                                            </div>
                                                        </div>

                                                        <br>

                                                        <div class="card contenedor_detalles_auditoria" style="height: 45%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 120%; font-family: 'Rajdhani';">
                                                                Observaciones Generales
                                                            </div>
                                                            <div class="card-body" style="height: 40%">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';"> Campo separado para Observaciones sobre el campo. </textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <div class="col-md-3 col-sm-3 offset-md-4 offset-sm-4">
                                                    <button id="btn_guardar_campo_1" type="button" class="btn btn-success guardar-auditoria-btn" style="font-family: 'Lobster Two'; font-size: 17px;">Guardar Información</button>
                                                </div>

                                            </div>
                                            
                                            <div class="tab-pane fade" id="lista_campo2" role="tabpanel" aria-labelledby="campo_2_list">

                                                <div class="row">
                                                    <div class="container col-md-4 col-sm-4" style="color: #42f1f4; max-width: 28%">
                                                        
                                                        <div class="row" style="margin: auto">

                                                          <div class="col-md-6 col-sm-6" style="font-family: 'Rajdhani'; text-align: left; color: black">
                                                            <label class="label label-success"><strong>N° Orden</strong></label>
                                                            <input type="text" class="form-control field-shader" disabled="true" placeholder="00">
                                                          </div>

                                                          <div class="col-md-6 col-sm-6" style="font-family: 'Rajdhani'; text-align: left; color: black">
                                                            <label class="label label-success"><strong>N° Campo</strong></label>
                                                            <input type="text" class="form-control field-shader" disabled="true" placeholder="01">
                                                          </div>

                                                        </div>

                                                        <hr>

                                                        <div class="input-group" style="font-family: 'Rajdhani';">
                                                          <div class="input-group-prepend field-shader">
                                                            <span class="input-group-text" id=""><strong>Valor Reportado</strong></span>
                                                          </div>
                                                          <input type="text" class="form-control field-shader" disabled="true" placeholder="11">
                                                        </div>

                                                        <hr>

                                                        <div class="card contenedor_detalles_auditoria" style="height: 55%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-family: 'Rajdhani';">
                                                                Descripción del Valor Reportado
                                                            </div>
                                                            <div class="card-body" style="height: 100%;">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';">Campo separado para descripción del Valor Registrado. </textarea>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_selector_archivo" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            Nombre de Archivo PDF
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="selector_archivo" class="dropdown-menu">
                                                            <h4 class="dropdown-header">Seleccione el Archivo de Soporte</h4>
                                                            <a class="dropdown-item">Consolidado01</a>
                                                            <a class="dropdown-item">Consolidado02</a>
                                                            <a class="dropdown-item">Consolidado03</a>
                                                            <a class="dropdown-item">Consolidado04</a>
                                                            <a class="dropdown-item">Consolidado05</a>
                                                            <a class="dropdown-item">Consolidado06</a>
                                                          </div>
                                                        </div>

                                                    </div>

                                                    <div class="container col-md-4 col-sm-4" style="color: #42f1f4; max-width: 28%">

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Número de Página</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_modifica_dato" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            <strong>¿Se modifica el dato original?</strong>
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="modifica_dato" class="dropdown-menu">
                                                            <h4 class="dropdown-header">¿Se Modifica el Dato Original?</h4>
                                                            <a class="dropdown-item">Si</a>
                                                            <a class="dropdown-item">No</a>
                                                          </div>
                                                        </div>

                                                        <hr>

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Valor Sugerido</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_modifica_archivo_original" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            <strong>Soporte de Modificación</strong>
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="modifica_archivo_original" class="dropdown-menu">
                                                            <h4 class="dropdown-header">Seleccione el Archivo que Soporta la Modificación</h4>
                                                            <a class="dropdown-item">Consolidado01</a>
                                                            <a class="dropdown-item">Consolidado02</a>
                                                            <a class="dropdown-item">Consolidado03</a>
                                                            <a class="dropdown-item">Consolidado04</a>
                                                            <a class="dropdown-item">Consolidado05</a>
                                                            <a class="dropdown-item">Consolidado06</a>
                                                          </div>
                                                        </div>

                                                        <hr>

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Número de Página</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                    </div>

                                                    <div class="container col-md-4 col-sm-4">
                                                        <div class="card contenedor_detalles_auditoria" style="height: 45%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 120%; font-family: 'Rajdhani';">
                                                                Descripción de Motivo de Dato No Conforme
                                                            </div>
                                                            <div class="card-body" style="height: 40%">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';"> Aquí va escrita la descripción del motivo por el cuál el dato es no conforme. </textarea>
                                                            </div>
                                                        </div>

                                                        <br>

                                                        <div class="card contenedor_detalles_auditoria" style="height: 45%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 120%; font-family: 'Rajdhani';">
                                                                Observaciones Generales
                                                            </div>
                                                            <div class="card-body" style="height: 40%">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';"> Campo separado para Observaciones sobre el campo. </textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <div class="col-md-3 col-sm-3 offset-md-4 offset-sm-4">
                                                    <button id="btn_guardar_campo_2" type="button" class="btn btn-success guardar-auditoria-btn" style="font-family: 'Lobster Two'; font-size: 17px;">Guardar Información</button>
                                                </div>

                                            </div>

                                            <div class="tab-pane fade" id="lista_campo3" role="tabpanel" aria-labelledby="campo_3_list">
                                            
                                                <div class="row">
                                                    <div class="container col-md-4 col-sm-4" style="color: #42f1f4; max-width: 28%">
                                                        
                                                        <div class="row" style="margin: auto">

                                                          <div class="col-md-6 col-sm-6" style="font-family: 'Rajdhani'; text-align: left; color: black">
                                                            <label class="label label-success"><strong>N° Orden</strong></label>
                                                            <input type="text" class="form-control field-shader" disabled="true" placeholder="00">
                                                          </div>

                                                          <div class="col-md-6 col-sm-6"  style="font-family: 'Rajdhani'; text-align: left; color: black">
                                                            <label class="label label-success"><strong>N° Campo</strong></label>
                                                            <input type="text" class="form-control field-shader" disabled="true" placeholder="01">
                                                          </div>

                                                        </div>

                                                        <hr>

                                                        <div class="input-group" style="font-family: 'Rajdhani';">
                                                          <div class="input-group-prepend field-shader">
                                                            <span class="input-group-text" id=""><strong>Valor Reportado</strong></span>
                                                          </div>
                                                          <input type="text" class="form-control field-shader" disabled="true" placeholder="11">
                                                        </div>

                                                        <hr>

                                                        <div class="card contenedor_detalles_auditoria" style="height: 55%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-family: 'Rajdhani';">
                                                                Descripción del Valor Reportado
                                                            </div>
                                                            <div class="card-body" style="height: 100%;">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';">Campo separado para descripción del Valor Registrado. </textarea>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_selector_archivo" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            Nombre de Archivo PDF
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="selector_archivo" class="dropdown-menu">
                                                            <h4 class="dropdown-header">Seleccione el Archivo de Soporte</h4>
                                                            <a class="dropdown-item">Consolidado01</a>
                                                            <a class="dropdown-item">Consolidado02</a>
                                                            <a class="dropdown-item">Consolidado03</a>
                                                            <a class="dropdown-item">Consolidado04</a>
                                                            <a class="dropdown-item">Consolidado05</a>
                                                            <a class="dropdown-item">Consolidado06</a>
                                                          </div>
                                                        </div>

                                                    </div>

                                                    <div class="container col-md-4 col-sm-4" style="color: #42f1f4; max-width: 28%">

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Número de Página</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_modifica_dato" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            <strong>¿Se modifica el dato original?</strong>
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="modifica_dato" class="dropdown-menu">
                                                            <h4 class="dropdown-header">¿Se Modifica el Dato Original?</h4>
                                                            <a class="dropdown-item">Si</a>
                                                            <a class="dropdown-item">No</a>
                                                          </div>
                                                        </div>

                                                        <hr>

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Valor Sugerido</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_modifica_archivo_original" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            <strong>Soporte de Modificación</strong>
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="modifica_archivo_original" class="dropdown-menu">
                                                            <h4 class="dropdown-header">Seleccione el Archivo que Soporta la Modificación</h4>
                                                            <a class="dropdown-item">Consolidado01</a>
                                                            <a class="dropdown-item">Consolidado02</a>
                                                            <a class="dropdown-item">Consolidado03</a>
                                                            <a class="dropdown-item">Consolidado04</a>
                                                            <a class="dropdown-item">Consolidado05</a>
                                                            <a class="dropdown-item">Consolidado06</a>
                                                          </div>
                                                        </div>

                                                        <hr>

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Número de Página</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                    </div>

                                                    <div class="container col-md-4 col-sm-4">
                                                        <div class="card contenedor_detalles_auditoria" style="height: 45%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 120%; font-family: 'Rajdhani';">
                                                                Descripción de Motivo de Dato No Conforme
                                                            </div>
                                                            <div class="card-body" style="height: 40%">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';"> Aquí va escrita la descripción del motivo por el cuál el dato es no conforme. </textarea>
                                                            </div>
                                                        </div>

                                                        <br>

                                                        <div class="card contenedor_detalles_auditoria" style="height: 45%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 120%; font-family: 'Rajdhani';">
                                                                Observaciones Generales
                                                            </div>
                                                            <div class="card-body" style="height: 40%">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';"> Campo separado para Observaciones sobre el campo. </textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <div class="col-md-3 col-sm-3 offset-md-4 offset-sm-4">
                                                    <button id="btn_guardar_campo_3" type="button" class="btn btn-success guardar-auditoria-btn" style="font-family: 'Lobster Two'; font-size: 17px;">Guardar Información</button>
                                                </div>

                                            </div>
                                            
                                            <div class="tab-pane fade" id="lista_campo4" role="tabpanel" aria-labelledby="campo_4_list">

                                                <div class="row">
                                                    <div class="container col-md-4 col-sm-4" style="color: #42f1f4; max-width: 28%">
                                                        
                                                        <div class="row" style="margin: auto">

                                                          <div class="col-md-6 col-sm-6" style="font-family: 'Rajdhani'; text-align: left; color: black">
                                                            <label class="label label-success"><strong>N° Orden</strong></label>
                                                            <input type="text" class="form-control field-shader" disabled="true" placeholder="00">
                                                          </div>

                                                          <div class="col-md-6 col-sm-6" style="font-family: 'Rajdhani'; text-align: left; color: black">
                                                            <label class="label label-success"><strong>N° Campo</strong></label>
                                                            <input type="text" class="form-control field-shader" disabled="true" placeholder="01">
                                                          </div>

                                                        </div>

                                                        <hr>

                                                        <div class="input-group" style="font-family: 'Rajdhani';">
                                                          <div class="input-group-prepend field-shader">
                                                            <span class="input-group-text" id=""><strong>Valor Reportado</strong></span>
                                                          </div>
                                                          <input type="text" class="form-control field-shader" disabled="true" placeholder="11">
                                                        </div>

                                                        <hr>

                                                        <div class="card contenedor_detalles_auditoria" style="height: 55%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-family: 'Rajdhani';">
                                                                Descripción del Valor Reportado
                                                            </div>
                                                            <div class="card-body" style="height: 100%;">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';">Campo separado para descripción del Valor Registrado. </textarea>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_selector_archivo" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            Nombre de Archivo PDF
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="selector_archivo" class="dropdown-menu">
                                                            <h4 class="dropdown-header">Seleccione el Archivo de Soporte</h4>
                                                            <a class="dropdown-item">Consolidado01</a>
                                                            <a class="dropdown-item">Consolidado02</a>
                                                            <a class="dropdown-item">Consolidado03</a>
                                                            <a class="dropdown-item">Consolidado04</a>
                                                            <a class="dropdown-item">Consolidado05</a>
                                                            <a class="dropdown-item">Consolidado06</a>
                                                          </div>
                                                        </div>

                                                    </div>

                                                    <div class="col-md-4 col-sm-4" style="color: #42f1f4; max-width: 28%">

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Número de Página</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_modifica_dato" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            <strong>¿Se modifica el dato original?</strong>
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="modifica_dato" class="dropdown-menu">
                                                            <h4 class="dropdown-header">¿Se Modifica el Dato Original?</h4>
                                                            <a class="dropdown-item">Si</a>
                                                            <a class="dropdown-item">No</a>
                                                          </div>
                                                        </div>

                                                        <hr>

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Valor Sugerido</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                        <hr>

                                                        <div class="btn-group dropup">
                                                          <button id="btn_modifica_archivo_original" type="button" class="btn btn-outline-info" style="color: black; font-family: 'Rajdhani'; font-weight: bold">
                                                            <strong>Soporte de Modificación</strong>
                                                          </button>
                                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                          </button>
                                                          <div id="modifica_archivo_original" class="dropdown-menu">
                                                            <h4 class="dropdown-header">Seleccione el Archivo que Soporta la Modificación</h4>
                                                            <a class="dropdown-item">Consolidado01</a>
                                                            <a class="dropdown-item">Consolidado02</a>
                                                            <a class="dropdown-item">Consolidado03</a>
                                                            <a class="dropdown-item">Consolidado04</a>
                                                            <a class="dropdown-item">Consolidado05</a>
                                                            <a class="dropdown-item">Consolidado06</a>
                                                          </div>
                                                        </div>

                                                        <hr>

                                                        <div class="input-group field-shader" style="font-family: 'Rajdhani';">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id=""><strong>Número de Página</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control">
                                                        </div>

                                                    </div>

                                                    <div class="container col-md-4 col-sm-4">
                                                        <div class="card contenedor_detalles_auditoria" style="height: 45%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 120%; font-family: 'Rajdhani';">
                                                                Descripción de Motivo de Dato No Conforme
                                                            </div>
                                                            <div class="card-body" style="height: 40%">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';"> Aquí va escrita la descripción del motivo por el cuál el dato es no conforme. </textarea>
                                                            </div>
                                                        </div>

                                                        <br>

                                                        <div class="card contenedor_detalles_auditoria" style="height: 45%;">
                                                            <div class="card-header bg-dark mb-3" style="color: #42f1f4; font-size: 120%; font-family: 'Rajdhani';">
                                                                Observaciones Generales
                                                            </div>
                                                            <div class="card-body" style="height: 40%">
                                                                <textarea class="form-control" rows="5" id="comment" style="height: 100%; font-family: 'Rajdhani';"> Campo separado para Observaciones sobre el campo. </textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <div class="col-md-3 col-sm-3 offset-md-4 offset-sm-4">
                                                    <button id="btn_guardar_campo_4" type="button" class="btn btn-success guardar-auditoria-btn" style="font-family: 'Lobster Two'; font-size: 17px;">Guardar Información</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>