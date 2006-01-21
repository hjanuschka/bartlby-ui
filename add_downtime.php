<?
include "layout.class.php";
include "config.php";
include "bartlby-ui.class.php";
$btl=new BartlbyUi($Bartlby_CONF);

$layout= new Layout();

$layout->setTitle("Add Downtime");



$layout->Form("fm1", "bartlby_action.php");
$layout->Table("100%");


if($_GET[service_id]{0} == 's') {
	$dt_type="Server";
	$cl=str_replace("s", "", $_GET[service_id]);
	$rr=bartlby_get_server_by_id($btl->CFG, $cl);
	$lappend=$rr[server_name];
	$dt_hidden=2;
} else {
	$rr=bartlby_get_service_by_id($btl->CFG, $_GET[service_id]);
	
	$lappend=$rr[server_name] . "/" . $rr[service_name];
	
	$dt_type="service";
	$dt_hidden=1;
}

$map = $btl->GetSVCMap();
$optind=0;
//$res=mysql_query("select srv.server_id, srv.server_name from servers srv, rights r where r.right_value=srv.server_id and r.right_key='server' and r.right_user_id=" . $poseidon->user_id);



$layout->Tr(
	$layout->Td(
		array(
			0=>"Reason",
			1=>$layout->Field("downtime_notice", "text", "") . $layout->Field("action", "hidden", "add_downtime") . $layout->Field("service_id", "hidden", $_GET[service_id])
		)
	)
);
$layout->Tr(
	$layout->Td(
		array(
			0=>"From",
			1=>$layout->Field("downtime_from", "text", date("d.m.Y H:i", time())) . $layout->Field("downtime_type", "hidden", $dt_hidden)
		)
	)
);

$layout->Tr(
	$layout->Td(
		array(
			0=>"To",
			1=>$layout->Field("downtime_to", "text", date("d.m.Y H:i", time()+1024))
		)
	)
);
$layout->Tr(
	$layout->Td(
			Array(
				0=>"Type",
				1=>$dt_type . $layout->Field("lappend", "hidden", $lappend)
			)
		)

);

$layout->Tr(
	$layout->Td(
			Array(
				0=>Array(
					'colspan'=> 2,
					"align"=>"right",
					'show'=>$layout->Field("Subm", "submit", "next->")
					)
			)
		)

);


$layout->TableEnd();
$layout->FormEnd();
$layout->display();