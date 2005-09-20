<?php
/*
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2005 by the INDEPNET Development Team.
 
 http://indepnet.net/   http://glpi.indepnet.org
 ----------------------------------------------------------------------

 LICENSE

	This file is part of GLPI.

    GLPI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    GLPI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GLPI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ------------------------------------------------------------------------
*/

// ----------------------------------------------------------------------
// Original Author of file: Julien Dombre
// Purpose of file:
// ----------------------------------------------------------------------


include ("_relpos.php");
include ($phproot."/glpi/includes.php");
include ($phproot . "/glpi/includes_tracking.php");
include ($phproot . "/glpi/includes_computers.php");
include ($phproot . "/glpi/includes_printers.php");
include ($phproot . "/glpi/includes_monitors.php");
include ($phproot . "/glpi/includes_peripherals.php");
include ($phproot . "/glpi/includes_networking.php");
include ($phproot . "/glpi/includes_software.php");
include ($phproot . "/glpi/includes_enterprises.php");
include ($phproot . "/glpi/includes_users.php");


	// Make a select box with preselected values
	$db = new DB;

	if($_POST['table'] == "glpi_dropdown_netpoint") {
		$query = "select t1.ID as ID, t1.name as netpname, t2.ID as locID from glpi_dropdown_netpoint as t1";
		$query .= " left join glpi_dropdown_locations as t2 on t1.location = t2.ID";
		$query .= $SEARCH;
		$query .= " order by t1.name,t2.name "; 
		$result = $db->query($query);
		// Get Location Array
		$query2="SELECT ID, completename FROM glpi_dropdown_locations";
		$result2 = $db->query($query2);
		$locat=array();
		if ($db->numrows($result2)>0)
		while ($a=$db->fetch_array($result2)){
			$locat[$a["ID"]]=$a["completename"];
		}

		echo "<select name=\"$myname\">";
		$i = 0;
		$number = $db->numrows($result);
		if ($number > 0) {
			while ($i < $number) {
				$output = $db->result($result, $i, "netpname");
				//$loc = getTreeValueCompleteName("glpi_dropdown_locations",$db->result($result, $i, "locID"));
				$loc=$locat[$db->result($result, $i, "locID")];
				$ID = $db->result($result, $i, "ID");
				echo "<option value=\"$ID\"";
				if ($ID==$value) echo " selected ";
				echo ">$output ($loc)</option>";
				$i++;
			}
		}
		echo "</select>";
	}	else {

	$where="WHERE '1'='1' ";
	if (in_array($_POST['table'],$deleted_tables))
		$where.="AND deleted='N'";
	if (in_array($_POST['table'],$template_tables))
		$where.="AND is_template='0'";
		
//print_r($_POST);

$where .=" AND  (ID <> '".$_POST['value']."' ";

	if (!empty($_POST['searchText'])&&$_POST['searchText']!="?")
		if (in_array($_POST['table'],$dropdowntree_tables))
		$where.=" AND completename LIKE '%".$_POST['searchText']."%' ";
		else $where.=" AND name LIKE '%".$_POST['searchText']."%' ";

$where.=")";


	$NBMAX=30;
	$LIMIT="LIMIT 0,$NBMAX";
	if ($_POST['searchText']=="?") $LIMIT="";


	if (in_array($_POST['table'],$dropdowntree_tables))
		$query = "SELECT ID, completename as name FROM ".$_POST['table']." $where ORDER BY completename $LIMIT";
	else $query = "SELECT ID, name FROM ".$_POST['table']." $where ORDER BY name $LIMIT";
	
//echo $query;
	$result = $db->query($query);

	
	echo "<select name=\"".$_POST['myname']."\" size='1'>";

	if ($_POST['searchText']!="?"&&$db->numrows($result)==$NBMAX)
	echo "<option value=\"0\">--Affichage limite--</option>";

	if ($table=="glpi_dropdown_kbcategories")
	echo "<option value=\"0\">--".$lang["knowbase"][12]."--</option>";
	else echo "<option value=\"0\">-----</option>";

	$output=getDropdownName($_POST['table'],$_POST['value']);
	if (!empty($output)&&$output!="&nbsp;")
	echo "<option selected value='".$_POST['value']."'>".$output."</option>";
	
	$i = 0;
	$number = $db->numrows($result);
	if ($number > 0) {
		while ($i < $number) {
			$output = $db->result($result, $i, "name");
			if (empty($output)) $output="&nbsp;";
			$ID = $db->result($result, $i, "ID");
//			if ($ID === $_POST['value']) {
//				echo "<option value=\"$ID\" selected>$output</option>";
//			} else {
				echo "<option value=\"$ID\">$output</option>";
//			}
			$i++;
		}
	}
	echo "</select>";
	
//	if ($_POST['table']=="glpi_enterprises")	{
//	echo getEnterpriseLinks($_POST['value']);
//	}

	}


?>