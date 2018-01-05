<?php
class Dashboard24hrStats {
	public $settings = array(
		'name' => 'Dashboard 24hr Stats',
		'description' => 'Displays the number of new orders and users over the past 24 hours.',
	);
	function dashboard_submodule() {
		global $billic, $db;
		$users_24hr = $db->q('SELECT HOUR(FROM_UNIXTIME(`datecreated`)) as `hour`, COUNT(*) as `num_rows` FROM `users` WHERE `status` = \'Active\' AND `datecreated` > \'' . (time() - (86400)) . '\' GROUP BY HOUR(FROM_UNIXTIME(`datecreated`))');
		$orders_24hr = $db->q('SELECT HOUR(FROM_UNIXTIME(`regdate`)) as `hour`, COUNT(*) as `num_rows` FROM `services` WHERE (`domainstatus` = \'Active\' OR `domainstatus` = \'Pending\') AND `regdate` > \'' . (time() - (86400)) . '\' GROUP BY HOUR(FROM_UNIXTIME(`regdate`))');
		$html = '<div id="graph-24hrStats" chartID="24hrStats" style="width: 100%; height:150px"></div><script>
addLoadEvent(function() {
g = new Dygraph(
      document.getElementById("graph-24hrStats"),
	  "Date,New Users,New Orders\n"+';
		$out = '';
		$hour = date('G', time());
		$hour++;
		for ($i = 0;$i < 24;$i++) {
			$hour++;
			if ($hour > 23) {
				$hour = 0;
			}
			// New Users
			$found = false;
			foreach ($users_24hr as $hr) {
				if ($hr['hour'] == $hour) {
					$new_users = $hr['num_rows'];
					$found = true;
					break;
				}
			}
			if (!$found) {
				$new_users = '0';
			}
			// New Orders
			$found = false;
			foreach ($orders_24hr as $hr) {
				if ($hr['hour'] == $hour) {
					$new_orders = $hr['num_rows'];
					$found = true;
					break;
				}
			}
			if (!$found) {
				$new_orders = '0';
			}
			$out = '"' . date('Y/m/d H:i', time() - ($i * 60 * 60)) . ',' . $new_users . ',' . $new_orders . '\n"+' . $out;
		}
		$html.= substr($out, 0, -1);
		$html.= ',
		{
			 axes: {
				y: {
					//drawAxis: false,
					//drawGrid: false,
					valueFormatter: function(x) {
						return x.toFixed(0);
					},
				},
				x: {
					//drawAxis: false,
					//drawGrid: false,
				}
			},
			interactionModel: {},
		}
    );
});
</script>';
		/*$html = '<div id="graph-24hrStats" chartID="24hrStats"></div><script>
		addLoadEvent(function() {
		  var data = {
		      labels: [';
		      $hour = date('G', time());
		      $hour++;
		      for($i=0;$i<23;$i++) {
		          $hour++;
		          if ($hour>23) {
		              $hour = 0;
		          }
		          $html .= '"'.$hour.($hour<12?' AM':' PM').'", ';
		      }
		      $html .= '"Now"';
		      //$html = substr($html, 0, -2);
		      $html .= '],
		      datasets: [';
		
		      $html .= '{label: "New Users",fillColor: "rgba(220,220,220,0.2)",strokeColor: "rgba(220,220,220,1)",pointColor: "rgba(220,220,220,1)",pointStrokeColor: "#fff",pointHighlightFill: "#fff",pointHighlightStroke: "rgba(220,220,220,1)",data: [';
		      $dataset = '';
		      $hour = date('G', time());
		      $hour++;
		      for($i=0;$i<24;$i++) {
		          $hour++;
		          if ($hour>23) {
		              $hour = 0;
		          }
		          $found = false;
		          foreach($users_24hr as $hr) {
		              if ($hr['hour']==$hour) {
		                  $dataset .= $hr['num_rows'].', ';
		                  $found = true;
		                  break;
		              }
		          }
		          if (!$found) {
		              $dataset .= '0, ';
		          }
		      }
		      $dataset = substr($dataset, 0, -2);
		      $html .= $dataset;
		      $html .= ']}, ';
		
		      $html .= '{label: "New Orders",fillColor: "rgba(220,220,220,0.2)",strokeColor: "rgba(220,220,220,1)",pointColor: "rgba(220,220,220,1)",pointStrokeColor: "#fff",pointHighlightFill: "#fff",pointHighlightStroke: "rgba(220,220,220,1)",data: [';
		      $dataset = '';
		      $hour = date('G', time());
		      $hour++;
		      for($i=0;$i<24;$i++) {
		          $hour++;
		          if ($hour>23) {
		              $hour = 0;
		          }
		          $found = false;
		          foreach($orders_24hr as $hr) {
		              if ($hr['hour']==$hour) {
		                  $dataset .= $hr['num_rows'].', ';
		                  $found = true;
		                  break;
		              }
		          }
		          if (!$found) {
		              $dataset .= '0, ';
		          }
		      }
		      $dataset = substr($users, 0, -2);
		      $html .= $dataset;
		      $html .= ']}, ';
		
		      $html = substr($html, 0, -2);
		      $html .= '
		      ]
		  };
		  var ctx = document.getElementById("graph-24hrStats").getContext("2d");
		  Charts["24hrStats"] = new Chart(ctx).Line(data, {
		      maintainAspectRatio: false,
		      scaleShowLabels: false,
		      pointHitDetectionRadius: 1,
		  });
		});
		</script>';
		
		*/
		/*
		      $html = '';
		      $html .= '<table class="table table-striped">';
		//echo '<tr><th colspan="2">24 Hour Statistics</th></tr>';
		$users_24hr = $db->q('SELECT COUNT(*) FROM `users` WHERE `status` = \'Active\' AND `datecreated` > \''.(time()-(86400)).'\'');
		$users_24hr = $users_24hr[0]['COUNT(*)'];
		      $html .= '<tr><td>New Users</td><td>'.$users_24hr.'</td></tr>';
		
		$orders_24hr = $db->q('SELECT COUNT(*) FROM `services` WHERE (`domainstatus` = \'Active\' OR `domainstatus` = \'Pending\') AND `regdate` > \''.(time()-(86400)).'\'');
		$orders_24hr = $orders_24hr[0]['COUNT(*)'];
		      $html .= '<tr><td>New Orders</td><td>'.$orders_24hr.'</td></tr>';
		      $html .= '</table>';
		*/
		return array(
			'header' => '24 Hour Statistics',
			'html' => $html,
		);
	}
}
