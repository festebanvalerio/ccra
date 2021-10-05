<!DOCTYPE html>
<html lang="es">
<head>	
	<title>SISTEMA RESTAURANT | Panel de Administracion</title>
	<meta charset="UTF-8">
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>	
    <!-- Bootstrap 3.3.4 -->
	<link type="text/css" rel="stylesheet" href="bootstrap/css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="font-awesome/css/font-awesome.min.css"/>
    <link type="text/css" rel="stylesheet" href="dist/css/AdminLTE.min.css"/>
	<link type="text/css" rel="stylesheet" href="dist/css/skins/skin-blue-light.min.css"/>
	<link type="text/css" rel="stylesheet" href="plugins/jquery/jquery-ui.min.css"/>	
	<link type="text/css" rel="stylesheet" href="plugins/morris/morris.css"/>
	<link type="text/css" rel="stylesheet" href="plugins/morris/example.css"/>
	<link type="text/css" rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css"/>
	<link type="text/css" rel="stylesheet" href="plugins/fullcalendar/fullcalendar.min.css"/>
	<link type="text/css" rel="stylesheet" href="plugins/fullcalendar/fullcalendar.print.css" media="print"/>	
    <link type="text/css" rel="stylesheet" href="plugins/select2/select2.min.css"/>
    <link type="text/css" rel="stylesheet" href="plugins/multiselect/multiselect.css"/>
    <link type="text/css" rel="stylesheet" href="plugins/sweet/sweetalert2.min.css" id="theme-styles"/>
    <script type="text/javascript" src="plugins/jquery/jquery-2.1.4.min.js"></script>	
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="plugins/morris/raphael-min.js"></script>
	<script type="text/javascript" src="plugins/morris/morris.js"></script>
    <script type="text/javascript" src="plugins/jspdf/jspdf.min.js"></script>
	<script type="text/javascript" src="plugins/jspdf/jspdf.plugin.autotable.js"></script>
	<script type="text/javascript" src="plugins/jquery/jquery.blockUI.js"></script>	
	<script type="text/javascript" src="plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="plugins/fullcalendar/moment.min.js"></script>  	
	<script type="text/javascript" src="plugins/fullcalendar/fullcalendar.min.js"></script>	
	<script type="text/javascript" src="plugins/jquery/jquery.validate.min.js"></script>
	<script type="text/javascript" src="plugins/sweet/sweetalert2.min.js"></script>
	<script type="text/javascript" src="plugins/multiselect/multiselect.js"></script>
	<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
	<style type="text/css">
        .select2 {
            width: 100%!important;
            font-size: 12px;
        }
        textarea {
            overflow: scroll;
            resize: none;
        }
        .panel1Icon {
            color: white;
        }
        .panel2Icon {
            color: white;
        }
        .panel3Icon {
            color: white;
        }
        body {
            font-family: helvetica;            
        }
        label.error { 
            float: none;
            color: red;
            padding-left: .5em; 
            vertical-align: middle;
            font-size: 11px;
        }
        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
            padding: 0 1.4em 0.4em 1.4em !important;
            margin: 0 0 0.5em 0 !important;
            -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
        }
        legend.scheduler-border {
            width:inherit; /* Or auto */
            padding:0 10px; /* To give a bit of padding on the left and right */
            border-bottom:none;
        }
        input, select, label, table {
            font-size: 12px;
        }
        .form-control:focus {
            border-color: #9b442f !important;
        }
	</style>
	<script type="text/javascript">
		$(document).ready(function(){
  			//$("select").select2();
		});

		$.datepicker.regional['es'] = {
			closeText: 'Cerrar',
			prevText: 'Anterior',
			nextText: 'Siguiente',
			monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
			dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
			dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
			dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
			dateFormat: 'dd/mm/yy',
			firstDay: 1,
			isRTL: false							
		};
		$.datepicker.setDefaults($.datepicker.regional['es']);

		function pad(input, length, padding) {			
		    var str = input + "";
		    return (length <= str.length) ? str : pad(str + padding, length, padding);
		}
		function fillDecimals(number, length) {
			var str = number + "";
			var dot = str.lastIndexOf('.');
			var isDecimal = dot != -1;
			var integer = isDecimal ? str.substr(0, dot) : str;
			var decimals = isDecimal ? str.substr(dot+1)  : "";
			decimals = pad(decimals, length, 0);
			return integer + '.' + decimals;
		}
		function soloNumeros(e) {
			var key = window.Event ? e.which : e.keyCode;
			return ((key >= 48 && key <= 57) || (key==8))
		}
		function filterFloat(e,element) { 
	  		var charCode = (e.which) ? e.which : e.keyCode;
	  	    if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8)) {
	  		    return false;
	  	    } else {
	  		    var len = $(element).val().length;
	  		    var index = $(element).val().indexOf('.');
	  		    if (index > 0 && charCode == 46) {
	  			    return false;
	  			}
	  			if (index > 0) {
	  	  			var CharAfterdot = (len + 1) - index;
	  	  			if (CharAfterdot > 3) {
	  	  	  			return false;
	  	  	  		}
	  	  	  	}
			}
			return true;  	    
	  	}
	</script>
	<style type="text/css">
        table.dataTable thead tr {
            background-color: #3c8dbc;
            color: white;            
        }
    </style>
</head>
<body class="<?php if(isset($_SESSION["user"])): ?> skin-blue-light sidebar-mini sidebar-collapse <?php else: ?>login-page<?php endif; ?>">
	<div class="wrapper">
    <!-- Main Header -->
   		<?php if(isset($_SESSION["user"])): ?>
      	<header class="main-header">
			<!-- Logo -->
			<a href="./index.php?view=home" class="logo"><!-- mini logo for sidebar mini 50x50 pixels -->
				<span class="logo-mini"><b>S</b>R</span> <!-- logo for regular state and mobile devices -->
				<span class="logo-lg" style="font-size: 14px;"><strong>SISTEMA RESTAURANT</strong></span>
			</a>
			<!-- Header Navbar -->
			<nav class="navbar navbar-static-top" role="navigation">
				<!-- Sidebar toggle button-->
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><span class="sr-only">Toggle navigation</span></a>
				<!-- Navbar Right Menu -->
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
					   <!-- User Account Menu -->
						<li class="dropdown user user-menu">
							<!-- Menu Toggle Button -->
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><!-- The user image in the navbar--> <!-- hidden-xs hides the username on small devices so only the image appears. -->
								<span class="">
								<?php
								    if (isset($_SESSION["user"])) {
								        $datos = UsuarioData::getById($_SESSION["user"])->nombres." ".UsuarioData::getById($_SESSION["user"])->apellidos;
								        $sede = SedeData::getById($_SESSION["sede"])->nombre;
								        $perfil = PerfilData::getById($_SESSION["perfil"])->nombre;
								        echo $datos . " - " . $perfil . " - " . $sede;
								    }
							    ?>
                                </span>
                                <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<!-- The user image in the menu -->
								<!-- Menu Footer-->
								<li class="user-footer">
									<div class="pull-right">
                          				<a href="./logout.php" class="btn btn-default btn-flat">Salir</a>
									</div>
								</li>
							</ul>
						</li>
						<!-- Control Sidebar Toggle Button -->
					</ul>
				</div>
			</nav>
		</header>
		<!-- Left side column. contains the logo and sidebar -->
		<aside class="main-sidebar">
			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">
				<!-- Sidebar Menu -->
				<ul class="sidebar-menu">
					<li class="header">ADMINISTRACION</li>
            		<?php
            		      if (isset($_SESSION["user"])) {
            		          $lstModuloXPerfil = ModuloPerfilData::getAllByPerfil($_SESSION["perfil"]);
            		          foreach ($lstModuloXPerfil as $objModuloXPerfil) {
            		              $objModulo = $objModuloXPerfil->getModulo();
            		              if ($objModulo->id_padre == 0) {
            		                  if ($objModulo->url != "") {
            		?>
            		<li><a href="<?php echo $objModulo->url; ?>"><?php echo $objModulo->icono; ?><span><?php echo $objModulo->nombre; ?></span></a></li>
            		<?php         
            		                  } else {
            		                      $lstModuloHijos = ModuloPerfilData::getAllModuloHijos($_SESSION["perfil"],$objModulo->id);
            		                      if (count($lstModuloHijos) > 0) {
                    ?>
                    <li class="treeview"><a href="#home"><?php echo $objModulo->icono; ?><span><?php echo $objModulo->nombre; ?></span>
						<i class="fa fa-angle-left pull-right"></i></a>
						<ul class="treeview-menu">
                    <?php
                                              foreach ($lstModuloHijos as $objModuloHijo) {
                                                  $objModulo = $objModuloHijo->getModulo();
                    ?>
                    <li><a href="<?php echo $objModulo->url; ?>"><?php echo $objModulo->icono; ?><span style="font-size: 12px;"><?php echo $objModulo->nombre; ?></span></a></li>
                    <?php
                                              }
                    ?>
                    	</ul>
                   	</li>
                    <?php                                              
            		                      } else {
                    ?>
                    <li><a href="<?php echo $objModulo->url; ?>"><?php echo $objModulo->icono; ?><span style="font-size: 12px;"><?php echo $objModulo->nombre; ?></span></a></li>
                    <?php
            		                      }
            		                  }
            		              }
            		          }
            		      }            		
            		?>
          		</ul>
				<!-- /.sidebar-menu -->
			</section>
			<!-- /.sidebar -->
		</aside>
		<?php endif; ?>
        <!-- Content Wrapper. Contains page content -->
      	<?php if(isset($_SESSION["user"])):?>
      	<div class="content-wrapper">
        <?php View::load("index");?>
      	</div>
		<!-- /.content-wrapper -->
		<footer class="main-footer">
			<div class="pull-right hidden-xs">
				<b>Version</b> v1.0
			</div>
			<strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#" target="_blank">SW RESTAURANT</a></strong>
		</footer>
      	<?php else:?>
		<div class="login-box">
			<div class="login-logo">
				<img src="logo.png" border="0" alt="Logo" title="Logo" width="240" height="160"/>
			</div>
			<!-- /.login-logo -->
			<div class="login-box-body">
				<form action="./?action=processlogin" method="post">
					<div class="form-group has-feedback">
						<input type="text" name="username" required class="form-control" placeholder="Usuario"/>
						<span class="glyphicon glyphicon-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="password" required class="form-control" placeholder="Password" />
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<button type="submit" class="btn btn-primary btn-block btn-flat">Acceder</button>
						</div>
						<!-- /.col -->
					</div>
				</form>
			</div>
			<!-- /.login-box-body -->
		</div>
		<!-- /.login-box -->
    <?php endif;?>






    </div>
	<!-- ./wrapper -->



	<!-- REQUIRED JS SCRIPTS -->



	<!-- jQuery 2.1.4 -->

	<!-- Bootstrap 3.3.2 JS -->

	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

	<!-- AdminLTE App -->

	<script src="dist/js/app.min.js" type="text/javascript"></script>

	<script type="text/javascript">

      $(document).ready(function(){
      })

    </script>

	<script src="plugins/datatables/jquery.dataTables.min.js"></script>

	<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
	
	<script type="text/javascript">

      $(document).ready(function(){

        $(".datatable").DataTable({
         "order": [[ 0, "desc" ], [1, "desc"]],
         
         "pageLength": 100,

          "language": {

        "sProcessing":    "Procesando...",

        "sLengthMenu":    "Mostrar _MENU_ registros",

        "sZeroRecords":   "No se encontraron resultados",

        "sEmptyTable":    "Ningún dato disponible en esta tabla",

        "sInfo":          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",

        "sInfoEmpty":     "Mostrando registros del 0 al 0 de un total de 0 registros",

        "sInfoFiltered":  "(filtrado de un total de _MAX_ registros)",

        "sInfoPostFix":   "",

        "sSearch":        "Buscar:",

        "sUrl":           "",

        "sInfoThousands":  ",",

        "sLoadingRecords": "Cargando...",

        "oPaginate": {

            "sFirst":    "Primero",

            "sLast":    "Último",

            "sNext":    "Siguiente",

            "sPrevious": "Anterior"

        },

        "oAria": {

            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",

            "sSortDescending": ": Activar para ordenar la columna de manera descendente"

        }

    }

        });

      });

    </script>

	<!-- Optionally, you can add Slimscroll and FastClick plugins.

          Both of these plugins are recommended to enhance the

          user experience. Slimscroll is required when using the

          fixed layout. -->

</body>

</html>
