<?
include "layout.class.php";
include "config.php";
include "bartlby-ui.class.php";
$btl=new BartlbyUi($Bartlby_CONF);

$layout= new Layout();

$defaults=bartlby_get_worker_by_id($btl->CFG, $_GET[worker_id]);
$map = $btl->GetSVCMap();
$optind=0;
while(list($k, $servs) = @each($map)) {

	for($x=0; $x<count($servs); $x++) {
		//$v1=bartlby_get_service_by_id($btl->CFG, $servs[$x][service_id]);
		
		if($x == 0) {
			//$isup=$btl->isServerUp($v1[server_id]);
			//if($isup == 1 ) { $isup="UP"; } else { $isup="DOWN"; }
			$servers[$optind][c]="";
			$servers[$optind][v]="";	
			$servers[$optind][k]="[ $isup ]&raquo;" . $servs[$x][server_name] . "&laquo;";
			$optind++;
		} else {
			
		}
		$state=$btl->getState($v1[current_state]);
		$servers[$optind][c]="";
		$servers[$optind][v]=$servs[$x][service_id];	
		$servers[$optind][k]="&nbsp;[ $state ]&nbsp;" .  $servs[$x][service_name];
		
		
		if(strstr((string)$defaults[services],"|" . $servs[$x][service_id] . "|")) {
			$servers[$optind][s]=1;	
		}
		
		$optind++;
	}
}

$act[0][c]="";
$act[0][v]="0";
$act[0][k]="Inactive";
if($defaults[active] == 0) {
	$act[0][s]=1;
}

$act[1][c]="";
$act[1][v]="1";
$act[1][k]="Active";
if($defaults[active] == 1) {
	$act[1][s]=1;
}

$layout->DisplayHelp(array(0=>"INFO|Adding a new server to monitor cycle"));

$layout->Form("fm1", "bartlby_action.php");
$layout->Table("100%");

$layout->Tr(
	$layout->Td(
			Array(
				0=>Array(
					'colspan'=> 2,
					'class'=>'header',
					'show'=>'Modify Worker'
					)
			)
		)

);

$layout->Tr(
	$layout->Td(
		array(
			0=>"Name",
			1=>$layout->Field("worker_name", "text", $defaults[name]) . $layout->Field("action", "hidden", "modify_worker")
		)
	)
);
$layout->Tr(
	$layout->Td(
		array(
			0=>"Password:",
			1=>$layout->Field("worker_password", "password", $defaults[password])
		)
	)
);
$layout->Tr(
	$layout->Td(
		array(
			0=>"Mail",
			1=>$layout->Field("worker_mail", "text", $defaults[mail])
		)
	)
);
$layout->Tr(
	$layout->Td(
		array(
			0=>"ICQ",
			1=>$layout->Field("worker_icq", "text", $defaults[icq])
		)
	)
);
$layout->Tr(
	$layout->Td(
		array(
			0=>"Active?:",
			1=>$layout->DropDown("worker_active", $act)
		)
	)
);
$layout->Tr(
	$layout->Td(
		array(
			0=>"Services:",
			1=>$layout->DropDown("worker_services[]", $servers, "multiple")
		)
	)
);
if(strstr((string)$defaults[notify_levels], "|0|")) {
	$chk0="checked";	
}
if(strstr((string)$defaults[notify_levels], "|1|")) {
	$chk1="checked";	
}
if(strstr((string)$defaults[notify_levels], "|2|")) {
	$chk2="checked";	
}
$layout->Tr(
	$layout->Td(
		array(
			0=>"Notifys:",
			1=>"<input type=checkbox value=0 name=notify[] $chk0><font color=green>OK</font><input value=1 type=checkbox name=notify[] $chk1><font color=orange>Warning</font><input value=2 type=checkbox name=notify[] $chk2><font color=red>Critical</font>" 
		)
	)
);



$layout->Tr(
	$layout->Td(
			Array(
				0=>Array(
					'colspan'=> 2,
					"align"=>"right",
					'show'=>$layout->Field("Subm", "submit", "next->") . $layout->Field("worker_id", "hidden", $_GET[worker_id])
					)
			)
		)

);


$layout->TableEnd();
$layout->FormEnd();
$layout->display();