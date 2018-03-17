<!DOCTYPE html>
<?php 
	/*
	ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	*/

    include '../../settings.cfg';  // Settings

	// Get label
	$label = $_GET["label"];
	trim($label);
    if ( 0 == strlen($label) ) {
		$label = "Measurement";
	}
	
	// Get sensor GUID
    $guid = $_GET["guid"];
    trim($guid);
    if ( 0 == strlen($guid) ) {
        $guid = 'FF:FF:FF:FF:FF:FF:FF:FF:61:00:08:01:92:AF:A8:10';    
	}

	// Get sensor index
    $sensorindex = $_GET["sensorindex"];
    trim($sensorindex);
    if ( 0 == strlen($sensorindex) ) {
        $sensorindex = 0;    
    }

?>
<html>
	<head>
		<meta http-equiv="refresh" content="1000">
		<meta charset="utf-8">
		<title>VSCP Temperature</title>
		<style>
			.chart-container {
				width: 90%;
				height: auto;
			}
		</style>

		<!-- Bootstrap core CSS -->
		<link href="js/bootstrap-4.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="measurements.css" rel="stylesheet">

		<script src="js/gauge.min.js"></script>

		<script language="Javascript"> 
			console.log("Available Height: " + window.screen.availHeight);
			console.log("Available Width: " + window.screen.availWidth);
		
			var gauge_size = 800;
			//if ( window.screen.availWidth < 380 ) {
				//gauge_size = window.screen.availWidth - 200;
			//}
		</script> 
	</head>
	<body>

		<div class="container">
			
			<div class="row">	
				
				<div class="mx-auto w-90">

					<!-- Injecting linear gauge -->
					<canvas class="mw-100 h-100" id="canvas_gauge"></canvas>

				</div>
			</div>	

			<div class="row">
				<div class="text-muted mx-auto" id="idInfoText"></div>
			</div>

			<div class="row">			
				<div class="col-sm-3"></div>
				<div class="col-sm-6">
					 <div class="dropdown mx-auto">
						<button class="btn btn-block btn-outline-secondary dropdown-toggle" type="button" 
								id="secoDropdownMenuButton" data-toggle="dropdown" 	
								aria-haspopup="true" aria-expanded="false">
					  	<span data-feather="activity"></span>
					  	Select source                  
						</button>
						<div class="dropdown-menu" aria-labelledby="top-seco-menu">
							<ul class="nav flex-column " id="top-seco-menu">
					  	</ul>  
					</div>   
			  	</div>
				</div>  
				<div class="col-sm-3"></div>
			</div>

			<div class="row">
				<div class="col-sm-3"></div>
				<div class="col-sm-6">
					<div class="text-muted text-center" id="copyright"><br><br>Copyright &copy; 2018 Åke Hedman, <a href="http://www.grodansparadis.com">Grodans Paradis AB</a><br>
			    	Part of the <a href="https://www.vscp.org">vscp.org</a> project.  MIT Licens
					</div>
				</div>  
				<div class="col-sm-3"></div>
			</div>

		</div>

		<!-- javascript -->
		<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/moment.min.js"></script>

		<script src="js/bootstrap-4.0.0/assets/js/vendor/popper.min.js"></script>
    	<script src="js/bootstrap-4.0.0/dist/js/bootstrap.min.js"></script>

		<!-- Icons -->		
    	<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    	<script>
      		feather.replace()
		</script>

		<!-- Page load handler -->
		<script type="text/javascript">

			var guid = "<?php echo $guid;?>";
			var sensorindex = "<?php echo $sensorindex;?>";
			var sensorname = "";

			var gauge1 = new RadialGauge({ 
					renderTo: 'canvas_gauge', 
        			width: 800,
        		    height: 800,
					glow: true,
					valueBox: true,
        			borderRadius: 20,
        			borders: 0,
        			barstrokeWidth: 20,
        			minorTicks: 10,
        			majorTicks: [-40,-30,-20,-10,0,10,20,30,40,50],
					highlights: [ 
						{ from: -40, to: 0, color: 'rgba(0, 50, 200, .2)'},
						{ from: 30, to: 50, color: 'rgba(200, 0, 0, .2)'} 
					],
					colors: {
						plate: '#fff'
					},
					minValue: -40,
        			maxValue: 50,
        			value: 0,
					title: 'Temperature',
        			units: '°C',
        			colorValueBoxShadow: true
			});

			///////////////////////////////////////////////////////////////////////
        	// Fetchdata
        	//

			function fetchData(newguid, newsensorname, newsensorindex) {
					
				console.log( newguid, newsensorname, newsensorindex );
	
				guid = newguid;
				sensorname = newsensorname;
				sensorindex = newsensorindex;
	
				// Get current measurement reading
				$.ajax({
					url : "<?php echo $MEASUREMENT_HOST;?>get_current.php?guid=" + newguid + 
												"&sensorindex=" + newsensorindex,
					type : "GET",
					success : function(data) {
	
						console.log(data);		
	
						datetime = data[0].date;
						current_value = data[0].value;	
	
						// Update gauge
						gauge1.value = current_value;	    
	
						if ( newsensorname.length ) {	
							$("div#idInfoText").html( "<h2>" + newsensorname + "</h2>" );
						}
										
					}
				});
			};
	
	
			///////////////////////////////////////////////////////////////////
			// Fetch seco's
			//
	
				$.ajax({
					  url : "<?php echo $MEASUREMENT_HOST;?>get_seco.php",
					  type : "GET",
					  success : function(data) {
	
						console.log(data.length, data);	
	
						if ( data.length )	{
	
							seco_name = [];
							seco_description = [];
							seco_guid = [];
							seco_sensorindex = [];
	
							for ( var i in data ) {
	
								seco_name.push( data[i].name );
								seco_description.push( data[i].description );
								seco_guid.push( data[i].guid );
								seco_sensorindex.push( data[i].sensorindex ); 
	
								$("#top-seco-menu").append('<li class="nav-item"><a class="nav-link" ' +
									  'href="javascript:fetchData(\'' + data[i].guid + '\',\'' + data[i].name +
									  '\',\'' + data[i].sensorindex + '\' );">' +
									  '<span data-feather="activity"> </span> ' + data[i].name + '</a></li>');
	
								var setguid = "<?php echo $guid; ?>";
								if ( !setguid.localeCompare( data[i].guid ) && 
									( sensorindex == data[i].sensorindex ) ) {
									$("div#idInfoText").html( "<h2>" + data[i].name + "</h2>" );
									sensorname = data[i].name;
									guid = data[i].guid;
									sensorindex = data[i].sensorindex;
								}
	
							}
						}
						else {
							sensorname = "no data available!"
						}
					   
					  },
	
					  error : function(data) {
	
					  }
			});

			$(document).ready(function(){

				gauge1.draw();

				fetchData(guid, sensorname, sensorindex);
				setInterval( function() { fetchData(guid,sensorname,sensorindex); }, 10000 );
		
			});

		</script>

	</body>

</html>