<!doctype html>
<?php 
    include '../../settings.cfg';  // Settings

    $dt = new DateTime();
    $from = htmlspecialchars( $_GET["from"] );
    trim($from);
    if ( 0 == strlen($from) ) {
        $from = $dt->format('Y-m-d\T00:00:00');
    }

    $to = htmlspecialchars( $_GET["to"] );
    trim($to);
    if ( 0 == strlen($to) ) {
        $to = $dt->format('Y-m-d\T23:59:59');    
    }
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>VSCP Measurement Visualize</title>

    <style>
		.chart-container {
		    width: 70%;
		    height: auto;
		}
	</style>

    <!-- Bootstrap core CSS -->
    <link href="js/bootstrap-4.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="measurements.css" rel="stylesheet">
  </head>

  <body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">VSCP mv</a>
      <input class="form-control form-control-dark w-100" type="text" placeholder="Date+time range" aria-label="Search">
      <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
          <a class="nav-link" href="#">Reload</a>
        </li>
      </ul>
    </nav>

    <!--  Hidden on sm devices -->
    <div class="container-fluid">
      <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
          <div class="sidebar-sticky">

            <ul class="nav flex-column" >

              <li class="nav-item">
                <a class="nav-link active" href="#">
                  <span data-feather="activity"></span>
                  Measurements (current)<span class="sr-only"></span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="activity"></span>
                  Current temperatures 
                </a>
              </li>
              
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="bar-chart-2"></span>
                  All diagrams
                </a>
              </li>
              
              <div id="guid_links"></div>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="settings"></span>
                  Settings
                </a>
              </li>

            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Available measurements</span>
              <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="plus-circle"></span>
              </a>
            </h6>
            <ul class="nav flex-column mb-2" id="side-seco-menu">
            </ul>
          </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            
            <h1 class="h2">Measurement</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
              
              <div class="btn-group mr-2">
                <button class="btn btn-sm btn-outline-secondary">Share</button>
                <button class="btn btn-sm btn-outline-secondary">Export</button>
              </div>
              
              <div class="btn-group mr-2">
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="secoDropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span data-feather="activity"></span>
                      Office 1 temperature                  
                  </button>
                  <div class="dropdown-menu" aria-labelledby="top-seco-menu">
                    <ul class="nav flex-column " id="top-seco-menu">
                    </ul>  
                  </div>   
                </div>
              </div>
              
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="rangeDropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span data-feather="calendar"></span>
                  This day                  
                </button>
                <div class="dropdown-menu" aria-labelledby="rangeDropdownMenuButton">
                  <a class="dropdown-item" href="#">Set date+time</a>
                  <a class="dropdown-item" href="#">This hour</a>
                  <a class="dropdown-item" href="#">Previous hour</a>
                  <a class="dropdown-item" href="#">This day</a>
                  <a class="dropdown-item" href="#">Yesterday</a>
                  <a class="dropdown-item" href="#">This week</a>
                  <a class="dropdown-item" href="#">Last week</a>
                  <a class="dropdown-item" href="#">This month</a>
                  <a class="dropdown-item" href="#">Last month</a>
                  <a class="dropdown-item" href="#">This quarter</a>
                  <a class="dropdown-item" href="#">Last quarter</a>
                  <a class="dropdown-item" href="#">This year</a>
                  <a class="dropdown-item" href="#"><span data-feather="activity"></span> Last year</a>
                </div>
              </div>
            </div>

          </div>

          <canvas class="my-4" id="mycanvas" width="900" height="380"></canvas>

            <div class="container">         
  		        <table class="table table-striped">	
		            <thead>
			            <tr><td class="text-success" ><b>Data for selected range</b></td></tr>
		            </thead>
		            <tbody>	  
		              <tr><td><div class="text-muted" id="updateTime"></div></td></tr>
		              <tr><td><div class="text-muted" id="lastReading"></div></td></tr>
		              <tr><td><div class="text-muted" id="minReading"></div></td></tr>
		              <tr><td><div class="text-muted" id="maxReading"></div></td></tr>		
		              <tr><td><div class="text-muted" id="meanReading"></div></td></tr>
		              <tr><td><div class="text-muted" id="countReading"></div></td></tr>
		            </tbody>
              </table>
            </div>
            
            <div>
					    <div class="text-muted text-center" id="copyright"><br><br>Copyright &copy; 2018 Åke Hedman, <a href="http://www.grodansparadis.com">Grodans Paradis AB</a><br>
			    	  Part of the <a href="https://www.vscp.org">vscp.org</a> project. MIT Licens
					    </div>
				    </div>  

        </main>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	  <script type="text/javascript" src="js/moment.min.js"></script>
    <script src="js/bootstrap-4.0.0/assets/js/vendor/popper.min.js"></script>
    <script src="js/bootstrap-4.0.0/dist/js/bootstrap.min.js"></script>

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
      feather.replace()
    </script>

    <!-- Graphs -->
    <script src="js/Chart.min.js"></script>
    <script>

      var label = "";
      var guid;
			var sensorindex = 0;
			var sensorname;
      var data;
      var options;
      var LineGraph;
      var measurement_time = [];
      var measurement_value = [];

      /////////////////////////////////////////////////////////////////////////
      // drawGraphics
      //

      function drawGraphics() {

        data = {
          labels: measurement_time,
          datasets: [{
            label: label,
            fill: false,
            lineTension: 1,
            backgroundColor: "rgba(59, 89, 152, 0.75 )",
            borderColor: "rgba(59, 89, 152, 1)",
            pointRadius: 0,  // Don't draw points
            pointHoverBackgroundColor: "rgba(59, 89, 152, 1)",
            pointHoverBorderColor: "rgba(59, 89, 152, 1)",						    
            data: measurement_value
          }]
        };

        options = {
          type: 'line',
          animation: false,
          // http://www.chartjs.org/docs/latest/axes/cartesian/time.html#ticks-source
          options: {
            animation: false,
            scales: {
              xAxes: [{
                type: 'time',
                format: "HH:mm",
                unit: 'hour',
                unitStepSize: 2,
                time: {											
                    displayFormats: {
                        'millisecond': 'HH:mm',
                        'second': 'HH:mm',
                        'minute': 'HH:mm',
                        'hour': 'HH:mm',
                        'day': 'HH:mm',
                        'week': 'HH:mm',
                        'month': 'HH:mm',
                        'quarter': 'HH:mm',
                        'year': 'HH:mm',
                        min: '00:00',
                          max: '23:59'
                      }
                },
                scaleLabel: {
                  display: true,
                  labelString: 'hour'
                }
              }],
              yAxes: [{
                scaleLabel: {
                  display: true,
                  labelString: 'degrees Celsius'
                }
              }]
            }
          }
        };	

        var ctx = $("#mycanvas");
        options.data = data;
        LineGraph = new Chart( ctx, options );

      }     

      ///////////////////////////////////////////////////////////////////////
			// truncate
      //

      function truncate (num, places) {
  		  return Math.trunc(num * Math.pow(10, places)) / Math.pow(10, places);
			}

      ///////////////////////////////////////////////////////////////////////
			// Get statistics
      // 

      function getStatistics() {

        //
        // Get statistics
        //

			  $.ajax({
			    url : "<?php echo $MEASUREMENT_HOST;?>get_stats.php?guid=" + guid + 
											"&sensorindex=" + sensorindex,
			    type : "GET",
			    success : function(data) {

				    console.log(data);		

					  count = data[0].count;
					  max = data[0].max;
					  min = data[0].min;
					  mean = truncate( data[0].mean, 2 );

					  $("div#minReading").text( "Minimum value: " + min );
					  $("div#maxReading").text( "Maximum value: " + max );					
					  $("div#meanReading").text( "Mean value: " + mean );
					  $("div#countReading").text( "# sample points: " + count );					
				  }
			  });

        //
        // Get current measurement reading
        // 
        
			  $.ajax({
			    url : "<?php echo $MEASUREMENT_HOST;?>get_current.php?guid=" + guid + 
											"&sensorindex=" + sensorindex,
			    type : "GET",
			    success : function(data) {

				    console.log(data);		

					  datetime = data[0].date;
					  current_value = data[0].value;		    
				
					  $("div#lastReading").text( "Last reading: " + current_value );					
				  }
			  });
      } 

      ///////////////////////////////////////////////////////////////////////
      // Fetchdata
      //

      function fetchData( newguid, newsensorname, newsensorindex ) { 
          
        console.log( "GUID = " + newguid );
        console.log( "SENSORNAME = " + newsensorname );
        console.log( "SENSORINDEX = " + newsensorindex );

        guid = newguid;
        sensorname = newsensorname;
        sensorindex = newsensorindex;

        $.ajax({          
          url : "<?php echo $MEASUREMENT_HOST;?>get_measurement.php?from=<?php echo $from;?>&to=<?php echo $to;?>&guid=" + newguid + "&sensorindex=" + newsensorindex,
          type : "GET",
          success : function(data) {

            console.log(data);		

            measurement_time = [];
            measurement_value = [];		    

            for ( var i in data ) {
              measurement_time.push( data[i].date );
              measurement_value.push( data[i].value );
            }

            data = {
              labels: measurement_time,
              datasets: [{
                label: sensorname,
                fill: false,
                lineTension: 1,
                backgroundColor: "rgba(59, 89, 152, 0.75 )",
                borderColor: "rgba(59, 89, 152, 1)",
                pointRadius: 0,  // Don't draw points
                pointHoverBackgroundColor: "rgba(59, 89, 152, 1)",
                pointHoverBorderColor: "rgba(59, 89, 152, 1)",						    
                data: measurement_value
              }]
            };
        
            createChart( data );
            $("div#updateTime").text( "Last update: " + moment().format("YYYY-MM-DD HH:mm:ss") );	

            getStatistics();
                                   
          },
    
          error : function(data) {

          }

        }); 
      };        

      /////////////////////////////////////////////////////////////////////////
      // createChart
      //

      function createChart(d) {

        if ( LineGraph) { 
          LineGraph.destroy();
        }
      
        options.data = d;  
        var ctx = $("#mycanvas");
        LineGraph = new Chart( ctx, options );

      }     

      /////////////////////////////////////////////////////////////////////////
      // Get seco data
      // 

      $.ajax({
          url : "<?php echo $MEASUREMENT_HOST;?>get_seco.php",
          type : "GET",
          success : function(data) {

            console.log(data);		

            seco_name = [];
            seco_description = [];
            seco_guid = [];
            seco_sensorindex = [];

            if ( ( data != null ) && data.length ) {
              
              for ( var i in data ) {
                seco_name.push( data[i].name );
                seco_description.push( data[i].description );
                seco_guid.push( data[i].guid );
                seco_sensorindex.push( data[i].sensorindex );
                console.log('href="javascript:alert(\'' + data[i].guid + '\');">');  
                $("#side-seco-menu").append('<li class="nav-item"><a class="nav-link" ' +
                                    'href="javascript:fetchData(\'' + data[i].guid + '\',\'' + data[i].name +
					  			                                                  '\',\'' + data[i].sensorindex + '\' );">' +
                                    '<span data-feather="activity"></span> ' + data[i].name + '</a><span data-feather="activity"></span></li>');
                $("#top-seco-menu").append('<li class="nav-item"><a class="nav-link" ' +
                                    'href="javascript:fetchData(\'' + data[i].guid + '\',\'' + data[i].name +
					  			                                                  '\',\'' + data[i].sensorindex + '\' );">' +
                                    '<span data-feather="activity"> </span> ' + data[i].name + '</a></li>');                                  
              }

              if ( 0 == label.length ) {
                label = data[0].name;
                sensorname = data[0].name;
                guid = data[0].guid;
                sensorindex = data[0].sensorindex;
                fetchData( guid, sensorname, sensorindex );
              }

            }
                                   
          },
    
          error : function(data) {

          }

        }); 

      // Ready

      $(document).ready(function(){ 

        drawGraphics(); 

      });

    </script>
  </body>
</html>
