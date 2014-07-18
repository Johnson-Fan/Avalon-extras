<?php
$file = file_get_contents("json/farm.json");
$farm = json_decode($file,true);
$zones = $farm["zone"];
$farm_map = array();
foreach($zones as $zone){
	$zone_map = array();
	$miner_per_table = $zone["layers"] * $zone["plot_split"];
	for($i = 0; $i < ceil( count($zone["miner"]) / $miner_per_table) ; $i ++) {
		$split_map = array();
		for($j = 0; $j < $zone["layers"]; $j ++) $split_map[] = array_fill(0, $zone["plot_split"], ' ');
		$zone_map[] = $split_map;
	}
	for($i=0; $i < count($zone["miner"]); $i ++){
		$n = floor($i / $miner_per_table);
		$x = floor(($i % $miner_per_table) / $zone["layers"]);
		$y = ($i % $miner_per_table) % $zone["layers"];
		$ports = array();
		foreach($zone["miner"][$i]["cgminer"] as $cgminer) $ports[] = $cgminer["port"];
		$zone_map[$n][$y][$x] = "<a href=\"cgminer.php?ip=" .
		       	$zone["miner"][$i]["ip"] . "&port=" . join(",",$ports) . "\">" . explode(".",$zone['miner'][$i]["ip"])[3] . "</a>";
	}	
	$farm_map[] = $zone_map;
}
#
$file = file_get_contents("json/status.json");
$status = json_decode($file,true);
?>

<html>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/comm.js"></script>
<body>
	<div class="row">
		<!--Left Start-->
		<div class="col-md-6">
			<div class="jumbotron">
			<h2>Generated <?php echo $status["time"] ?></h2>
			</div>
			<div class="jumbotron">
				<a href="#" class="thumbnail">
					<img data-src="images/fox2.png" src="images/fox2.png" alt="...">
				</a>
			</div>
			<div class="jumbotron">
<?php
foreach($farm_map as $zone_map){
	for($n = 0; $n < count($zone_map); $n ++){
		echo "<table class=\"table table-bordered table-striped\"><tbody>";
		for($y = 0; $y < count($zone_map[$n]);$y ++){
			echo "<tr>";
			for($x = 0; $x < count($zone_map[$n][$y]);$x ++) echo "<td>" . $zone_map[$n][$y][$x] . "</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
	}
}
?>
			</div>
		</div>
		<!--Left End-->
		<!--Right Start-->
		<div class="col-md-6">
			<div class="jumbotron">
			<h3><strong>Active IP</strong>:     <?php echo $status["active_ip_num"]; ?></h2>
			<h3><strong>Alive Modules</strong>:     <?php echo $status["alive_mod_num"]; ?></h2>
				<h3>Error List:</h3>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<td><strong>IP</strong></td>
								<td><strong>Error</strong></td>
							</tr>
						</thead>
						<tbody>
<?php
if(count($status["err_miner_list"]) === 0) echo "<tr><td>None</td><td>None</td></tr>";
else{
	foreach($status["err_miner_list"] as $err_miner){
		echo "<tr><td>" . $err_miner["id"] . "</td><td>";
		foreach($err_miner["error"] as $err) echo "<font color=\"" . $err["color"] . "\">" . $err["msg"] . "</font>";
		echo "</td></tr>";
	}
}
?>
						</tbody>
					</table>
			</div>
				<div class="jumbotron">
					<a href="#" class="thumbnail">
						<img data-src="images/fox1.png" src="images/fox1.png" alt="...">
					</a>
				</div>
		</div>
		<!--Right end-->
	</div>
</body>
</html>
