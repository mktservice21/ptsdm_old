<?php
    //error_reporting(E_ALL);
    $expire=time()-3600;
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
?>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Backup Data</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
<?PHP
    $mysqlHostNameK = "new.sdm-mkt.com";
    $mysqlHostNameK = "192.168.88.188";
    $mysqlUserNameK = "root";
    $mysqlPasswordK = "Ganteng123456";
    /*
    $mysqlUserNameK      = "root";
    $mysqlPasswordK     = "";
    $mysqlHostNameK      = "localhost";
     * 
     */
    
    $cnmy=mysqli_connect($mysqlHostNameK, $mysqlUserNameK, $mysqlPasswordK);
    
$query = "SELECT schema_name FROM information_schema.schemata WHERE schema_name
    NOT IN ('information_schema', 'mysql', 'performance_schema', 'phpmyadmin') order by schema_name";

$result = mysqli_query($cnmy, $query) or die(mysqli_error($link));
$dbs = array();
$no=1;
echo "<table id='datatable' class='table table-striped table-bordered' width='50%'>";
echo "<thead>
        <tr><th>No</th>
            <th>Tabel Name</th>
        </tr>
    </thead><tbody>";
while($db = mysqli_fetch_row($result)){
    $DbName             = $db[0];
    echo "<tr><td width=20>$no</td>";
    echo "<td>";
    echo "<a class='btnx btn-primaryx' href='module/mod_tools_backdbmysql/backup.php?tabgetnya=$DbName'>$DbName</a>";
    echo "</td></tr>";
    $no++;
}
echo "</tbody></table>";
?>
            </div>
        </div>
    </div>
    <!--end row-->
</div>