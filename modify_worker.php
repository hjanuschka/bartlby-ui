<?
include "layout.class.php";
include "config.php";
include "bartlby-ui.class.php";



$btl=new BartlbyUi($Bartlby_CONF);




$layout= new Layout();

$layout->set_menu("worker");
$layout->setTitle("Modify Worker");
$defaults=@bartlby_get_worker_by_id($btl->CFG, $_GET[worker_id]);


$fm_action="modify_worker";
if($_GET["copy"] == "true") {
	$btl->hasRight("action.copy_worker");
	$fm_action="add_worker";
	$layout->setTitle("Copy Worker");
}
if($_GET["new"] == "true") {
	$fm_action="add_worker";
	$layout->setTitle("Add Worker");
	$defaults="";
	
	
}

if($btl->user_id != $_GET[worker_id]) {
	$btl->hasRight("action." . $fm_action);
}
if(!$btl->isSuperUser() && $btl->user_id != $_GET[worker_id]) {
	$btl->hasRight("modify_all_workers");
}

if($defaults == false && $_GET["new"] != "true") {
	$btl->redirectError("BARTLBY::OBJECT::MISSING");
	exit(1);	
}
if(!$defaults) {
	$defaults[escalation_limit]=50;
	$defaults[escalation_minutes]=3;
}

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
		$state=$btl->getState($servs[$x][current_state]);
		$servers[$optind][c]="";
		$servers[$optind][v]=$servs[$x][service_id];	
		$servers[$optind][k]="&nbsp;[ $state ]&nbsp;" .  $servs[$x][service_name];
		
		
		if(strstr((string)$defaults[services],"|" . $servs[$x][service_id] . "|")) {
			$servers[$optind][s]=1;	
		}
		
		$optind++;
	}
}
$optind=0;
$plgs=bartlby_config($btl->CFG, "trigger_dir");
$dh=opendir($plgs);
while ($file = readdir ($dh)) { 
   if ($file != "." && $file != "..") { 
   	clearstatcache();
   	if(is_executable($plgs . "/" . $file) && !is_dir($plgs . "/" . $file)) {
   		
       		$triggers[$optind][c]="";
       		$triggers[$optind][v]=$file;
       		$triggers[$optind][k]=$file;
       		/*if($defaults[plugin] == $file) {
       			$plugins[$optind][s]=1;	
       		}*/
       		
       		if(strstr((string)$defaults[enabled_triggers],"|" . $file . "|")) {
				$triggers[$optind][s]=1;	
			}
       		
       		$optind++;
       	}
   } 
}
closedir($dh); 

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

$layout->OUT .= "<script>
		function simulateTriggers() {
			wname=document.fm1.worker_name.value;
			wmail=document.fm1.worker_mail.value;
			wicq=document.fm1.worker_icq.value;
			TRR=document.fm1['worker_triggers[]'];
			wstr='|';
			for(x=0; x<=TRR.length-1; x++) {
				
				if(TRR.options[x].selected) {
					
					wstr =  wstr +  TRR.options[x].value + '|';	
				}
				
			}
			window.open('trigger.php?user='+wname+'&mail='+wmail+'&icq='+wicq+'&trs=' + wstr, 'tr', 'width=600, height=600, scrollbars=yes');
		}
		</script>
";


$ov .= $layout->Form("fm1", "bartlby_action.php", "GET", true);
$layout->Table("100%");




$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"Name",
			1=>$layout->Field("worker_name", "text", $defaults[name]) . $layout->Field("action", "hidden", $fm_action)
		)
	)
, true);
$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"Password:",
			1=>$layout->Field("worker_password", "password", "")
		)
	)
,true);

$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"Repeat password:",
			1=>$layout->Field("worker_password1", "password", "")
		)
	)
,true);


$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"Mail",
			1=>$layout->Field("worker_mail", "text", $defaults[mail])
		)
	)
,true);
$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"ICQ",
			1=>$layout->Field("worker_icq", "text", $defaults[icq])
		)
	)
,true);

$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"Escalation",
			1=>"<font size=1>" . $layout->Field("escalation_limit", "text", $defaults[escalation_limit]) . "notify's  per " . $layout->Field("escalation_minutes", "text", $defaults[escalation_minutes]) .  " minutes</font>"
		)
	)
,true);

$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"Active?:",
			1=>$layout->DropDown("worker_active", $act)
		)
	)
,true);
$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"Services:",
			1=>$layout->DropDown("worker_services[]", $servers, "multiple")
		)
	)
, true);

if(strstr((string)$defaults[notify_levels], "|0|")) {
	$chk0="checked";	
}
if(strstr((string)$defaults[notify_levels], "|1|")) {
	$chk1="checked";	
}
if(strstr((string)$defaults[notify_levels], "|2|")) {
	$chk2="checked";	
}
if(strstr((string)$defaults[notify_levels], "|7|")) {
	$chk7="checked";	
}


$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"Notifys:",
			1=>"<input type=checkbox value=0 name=notify[] $chk0><font color=green>OK</font><input value=1 type=checkbox name=notify[] $chk1><font color=orange>Warning</font><input value=2 type=checkbox name=notify[] $chk2><font color=red>Critical</font> <input type=checkbox value=7 name=notify[] $chk7><font color=gray>Sirene</font>" 
		)
	)
,true);

$ov .= $layout->Tr(
	$layout->Td(
		array(
			0=>"Triggers:",
			1=>$layout->DropDown("worker_triggers[]", $triggers, "multiple") . " <a href='javascript:simulateTriggers();'>Simulate</A>"
		)
	)
,true);

$ov .= $layout->Tr(
	$layout->Td(
			Array(
				0=>Array(
					'colspan'=> 2,
					"align"=>"left",
					'show'=>"<a href='modify_worker.php?copy=true&worker_id=" . $_GET[worker_id] . "'><img src='images/edit-copy.gif' title='Copy (Create a similar) this worker' border=0></A>"
					)
			)
		)

,true);

$title="";  
$content = "<table>" . $ov . "</table>";
$layout->push_outside($layout->create_box($layout->BoxTitle, $content));

$r=$btl->getExtensionsReturn("_PRE_" . $fm_action, $layout);
	

$layout->Tr(
	$layout->Td(
			Array(
				0=>Array(
					'colspan'=> 2,
					"align"=>"right",
					'show'=>$layout->Field("Subm", "button", "next->", "", " onClick='xajax_AddModifyWorker(xajax.getFormValues(\"fm1\"))'") . $layout->Field("worker_id", "hidden", $_GET[worker_id])
					)
			)
		)

);


$layout->TableEnd();
$layout->FormEnd();
$layout->display();