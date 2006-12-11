<?

function NoDB()  {
 echo "<H4>Keine Verbindung zum Datenbankserver</H4>";
 echo "Versuchen sie es zu einem späteren Zeitpunkt nocheinmal.";
 }

$db_Database = "diverse";
$db_UserName = "divuser";//"jan";//
$db_Password = "D1VuS3rpAsS";//"pups"; //
$db_Hostname = "localhost";

@mysql_connect($db_Hostname, $db_UserName, $db_Password) || die(NoDB());
mysql_select_db($db_Database);

if (isset($_GET['id'])) {
$results = mysql_query("SELECT * FROM links WHERE id = '$_GET[id]'");
 if($row = mysql_fetch_array($results) OR DIE(mysql_error())) {
   $row["clicks"] = $row["clicks"]+1;
   mysql_query("UPDATE links SET clicks = '$row[clicks]' WHERE id = '$_GET[id]'");
   header("Location: $row[url]");
   }
 else {
   header("Location: http://www.traum-projekt.com"); }

}
else {
   header("Location: http://www.traum-projekt.com"); }

?>