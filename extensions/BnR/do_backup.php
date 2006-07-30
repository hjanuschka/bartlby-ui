<?
	
	include "config.php";
	include "layout.class.php";
	include "bartlby-ui.class.php";
	
	include "extensions/BnR/BnR.class.php";
	
	
	
	$btl=new BartlbyUi($Bartlby_CONF);
	$sg = new BnR();
	$servers=$btl->GetSVCMap();
	
	$layout= new Layout();
	$layout->setTitle("BnR: Backup!!");
	
	$layout->set_menu("BnR");
	
	$layout->Table("100%");
	

$layout->Tr(
	$layout->Td(
			Array(
				array("colspan" => 2, "show" => "<b>Status.....</b>")
			)
		)

);	
$backup_name=date("d.m.Y-H_i_s") . "/";
$bdir="extensions/BnR/store/" . $backup_name;
if(!is_dir($bdir)) {
	mkdir($bdir);
}
while(list($k,$v) = @each($servers)) {
	$o .= "creating package for server: " . $v[0][server_name] . "<br>";
	$serv = array();
	for($x=0; $x<count($v); $x++) {
		array_push($serv, $v[$x][service_id]);
	}
	$btl->create_package($v[0][server_id] . ".srv", $serv, $_GET[package_with_plugins], $_GET[package_with_perf], $bdir);
	
}

$wrkmp = $btl->GetWorker();

$fp=fopen($bdir . "/worker.ser", "w");
fwrite($fp, serialize($wrkmp));
fclose($fp);

$o .= "Workers saved<br>";

$dtmap = bartlby_downtime_map($btl->CFG);
$fp=fopen($bdir . "/downtime.ser", "w");
fwrite($fp, serialize($dtmap));
fclose($fp);

$o .= "Downtimes saved<br>";

if($_GET[package_with_config]) {
	@copy("ui-extra.conf", $bdir . "/ui-extra.conf");
	@copy($btl->CFG, $bdir . "/" . basename($btl->CFG));
	$o .= "ui-extra.conf, bartlby.cfg saved<br>";
}


$o .= "adding backup info: '" .  $_GET[package_with_comment] . "'<br>";
$fp=fopen($bdir . "/info.txt", "w");
fwrite($fp, $_GET[package_with_comment]);
fclose($fp);

$o .= "<b>Asking extensions</b><br>";

$btl->getExtensionsReturn("_backup", false);

$o .= "<b>done ($bdir)</b><br>";

$layout->Tr(
	$layout->Td(
			Array(
				array("colspan" => 2, "show" => $o)
			)
		)

);	
	$layout->TableEnd();
	$layout->display();