<?php

backup_tables("localhost","DB_Username","DB_Password",'DB_Name','*');
only_table_structure("localhost","DB_Username","DB_Password",'DB_Name','*');
only_view_structure("localhost","DB_Username","DB_Password",'DB_Name','*');



/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$dbname,$tables)
{
	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($dbname,$link);

	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);

		//$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";

		for ($i = 0; $i < $num_fields; $i++)
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j < $num_fields; $j++)
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j < ($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}

	//save file
    date_default_timezone_set('Asia/kolkata');
                $curtime=date('H:i:s');
    $foldername=date('d-M-Y')."_backup";
    mkdir($foldername);
	$handle = fopen($foldername."/TABLE_WITH_ALL_DATA_".$dbname.'_'.date('d-m-Y').'_'.$curtime.'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}

function only_table_structure($host,$user,$pass,$dbname,$tables)
{

	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($dbname,$link);

	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);

		//$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";

		$return.="\n\n\n";
	}

	//save file
    date_default_timezone_set('Asia/kolkata');
                $curtime=date('H:i:s');
    $foldername=date('d-M-Y')."_backup";
    mkdir($foldername);
	$handle = fopen($foldername."/ONLY_TABLE_Structure_".$dbname.'_'.date('d-m-Y').'_'.$curtime.'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}

function only_view_structure($host,$user,$pass,$dbname,$tables)
{

	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($dbname,$link);

	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query("SHOW FULL TABLES WHERE Table_Type != 'BASE TABLE'");
		while($row = mysql_fetch_row($result))
		{
			 $tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);

		//$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
        $maindata=$row2[1];
        $pos1=strpos($maindata,"VIEW");
        $maindata="CREATE ".substr($maindata,$pos1);
        
		$return.= "\n\n".$maindata.";\n\n";
		$return.="\n\n\n";
	}

	//save file
    date_default_timezone_set('Asia/kolkata');
                $curtime=date('H:i:s');
    $foldername=date('d-M-Y')."_backup";
    mkdir($foldername);
	$handle = fopen($foldername."/ONLY_VIEW_Structure_".$dbname.'_'.date('d-m-Y').'_'.$curtime.'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}
?>