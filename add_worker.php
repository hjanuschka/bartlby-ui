<?
include "layout.class.php";
include "config.php";
include "bartlby-ui.class.php";
$btl=new BartlbyUi($Bartlby_CONF);

$layout= new Layout();



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
		//$state=$btl->getState($v1[current_state]);
		$servers[$optind][c]="";
		$servers[$optind][v]=$servs[$x][service_id];	
		$servers[$optind][k]="&nbsp;[ $state ]&nbsp;" .  $servs[$x][service_name];
		
		$optind++;
	}
}




$act[0][c]="";
$act[0][v]="0";
$act[0][k]="Inactive";

$act[1][c]="";
$act[1][v]="1";
$act[1][k]="Active";

$layout->DisplayHelp(array(0=>"INFO|Adding a new server to monitor cycle"));

$layout->Form("fm1", "bartlby_action.php");
$layout->Table("100%");

$layout->Tr(
	$layout->Td(
			Array(
				0=>Array(
					'colspan'=> 2,
					'class'=>'header',
					'show'=>'Add Worker'
					)
			)
		)

);

$layout->Tr(
	$layout->Td(
		array(
			0=>"Name",
			1=>$layout->Field("worker_name", "text", "") . $layout->Field("action", "hidden", "add_worker")
		)
	)
);
$layout->Tr(
	$layout->Td(
		array(
			0=>"Password:",
			1=>$layout->Field("worker_password", "password", "")
		)
	)
);
$layout->Tr(
	$layout->Td(
		array(
			0=>"Mail",
			1=>$layout->Field("worker_mail", "text", "")
		)
	)
);
$layout->Tr(
	$layout->Td(
		array(
			0=>"ICQ",
			1=>$layout->Field("worker_icq", "text", "")
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

$layout->Tr(
	$layout->Td(
		array(
			0=>"Notifys:",
			1=>"<input type=checkbox value=0 name=notify[]><font color=green>OK</font><input value=1 type=checkbox name=notify[]><font color=orange>Warning</font><input value=2 type=checkbox name=notify[]><font color=red>Critical</font>" 
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