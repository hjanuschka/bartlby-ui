<?
	include "config.php";
	include "layout.class.php";
	include "bartlby-ui.class.php";
	$btl=new BartlbyUi($Bartlby_CONF);
	$info=$btl->getInfo();
	$layout= new Layout();
	$layout->MetaRefresh(10);
	$layout->Table("100%");
	$lib=bartlby_lib_info($btl->CFG);
	$exi=-1;
	$plugin_dir=bartlby_config($btl->CFG, "agent_plugin_dir");
	
	$arges=str_replace("\\\"", "\"", $_GET[args]);
	$arges=str_replace("\\\\", "\\", $arges);
	$server=bartlby_get_service($btl->CFG, $_GET[server]);	
	@$fp=fsockopen($server[client_ip], $server[client_port], $errno, $errstr, 10);
	if(!$fp) {
		$cmd_out = "$errstr ($errno)<br />\n";	
	} else {
		$str=$_GET[plugin] . "| " . $arges . "|\n";
		
		$estr=bartlby_encode($str);
		fwrite($fp, $estr);
		$vers=fread($fp, 1024);
		$reply=fread($fp, 1024);
		fclose($fp);
		$dreply=bartlby_decode($reply);
		$ex=explode("|", $dreply);
		$cmd_out = $ex[1];
		$exi=$ex[0];
		
	}
	
	
	
	$layout->Tr(
		$layout->Td(
				Array(
					0=>Array(
						'colspan'=> 1,
						'class'=>'header',
						'show'=>"Check:&nbsp;<b>" . $server[server_name] . ":" . $server[client_port] . "/" . $server[service_name] . " Version: $vers</b>"
						)
				)
			)

	);
	$layout->Tr(
	$layout->Td(
			Array(
				0=>$cmd_out . "<br><br><hr noshade>Exited with: <font color='" . $btl->getColor($exi) . "'>" . $btl->getState($exi) . "</font>"
				
			)
		)

	);
	$layout->TableEnd();
	$layout->display("no");
	
?>