<?
	
	include "config.php";
	include "layout.class.php";
	include "bartlby-ui.class.php";
	
	include "extensions/BnR/BnR.class.php";
	
	
	
	$btl=new BartlbyUi($Bartlby_CONF);
	$sg = new BnR();
	$servers=$btl->GetSVCMap();
	
	
	
	$layout= new Layout();
	$layout->setTitle("BnR: Restore!!");
	
	$layout->set_menu("BnR");
	
	$layout->Table("100%");
	

$layout->Tr(
	$layout->Td(
			Array(
				array("colspan" => 2, "show" => "<b>Status.....</b>")
			)
		)

);	
$backup_name=$_GET[backup] . "/";
$bdir="extensions/BnR/store/" . $backup_name;




while(list($k,$v) = @each($servers)) {
	$o .= "deleting server: " . $v[0][server_name] . "<br>";	
	bartlby_delete_server($btl->CFG, $k);	
}
$btl->doReload();
$o .= ".... reloaded<br>";


foreach(glob($bdir . "/*.srv") as $fname) {
	$dmp = unserialize(file_get_contents($fname));
	$add_server = bartlby_add_server($btl->CFG, $dmp[0][server_name], $dmp[0][client_ip], $dmp[0][client_port], $dmp[0][server_icon]);	
	$orig_servers[$dmp[0][server_id]] = $add_server;
	$btl->installPackage(basename($fname), $add_server, $_GET[force_plugin], $_GET[force_perf], $bdir);
	$o .= "restored.... " . $dmp[0][server_name] . "<br>";
	
}


$wrkmp = @$btl->GetWorker();

for($x=0; $x<count($wrkmp); $x++) {
	$o .= "delete worker: " . $wrkmp[$x][worker_id] . "<br>";	
	bartlby_delete_worker($btl->CFG, $wrkmp[$x][worker_id]);
}

$wrkmap = unserialize(file_get_contents($bdir . "/worker.ser"));

	
for($x=0; $x<count($wrkmap); $x++) {
	
	$o .= "adding worker: " . $wrkmap[$x][name] .  "<br>";	
	//bartlby_delete_worker($btl->CFG, $wrkmp[$x][worker_id]);
	$add=bartlby_add_worker($btl->CFG, $wrkmap[$x][mail], $wrkmap[$x][icq], $wrkmap[$x][services], $wrkmap[$x][notify_levels], $wrkmap[$x][active], $wrkmap[$x][name],$wrkmap[$x][password], $wrkmap[$x][enabled_triggers]);
			
}
$dtmap = bartlby_downtime_map($btl->CFG);

for($x=0; $x<count($dtmap); $x++) {
	
	
	bartlby_delete_downtime($btl->CFG, $dtmap[$x][downtime_id]);
	
			
}

$dtmap = unserialize(file_get_contents($bdir . "/downtime.ser"));

for($x=0; $x<count($dtmap); $x++) {
	
	
	bartlby_add_downtime($btl->CFG, $dtmap[$x][downtime_from], $dtmap[$x][downtime_to], $dtmap[$x][downtime_type], $dtmap[$x][downtime_notice], $dtmap[$x][service_id]);
	
			
}

$btl->doReload();
$o .= ".... reloaded<br>";
$o .= "<b>done ($bdir)</b><br>";


$o .= "adding backup info: '" .  $_GET[package_with_comment] . "'<br>";
$fp=fopen($bdir . "/info.txt", "w");
fwrite($fp, $_GET[package_with_comment]);
fclose($fp);


$o .= "<b>Asking extensions</b><br>";

$btl->getExtensionsReturn("_restore", false);


$layout->Tr(
	$layout->Td(
			Array(
				array("colspan" => 2, "show" => $o)
			)
		)

);	

$layout->TableEnd();
$layout->display();
exit;



if($_GET[package_with_config]) {
	@copy("ui-extra.conf", $bdir . "/ui-extra.conf");
	@copy($btl->CFG, $bdir . "/" . basename($btl->CFG));
	$o .= "ui-extra.conf, bartlby.cfg saved<br>";
}





	$layout->TableEnd();
	$layout->display();