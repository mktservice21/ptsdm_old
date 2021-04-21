<?PHP
include("config/koneksimysqli.php");
include "config/fungsi_ubahget_id.php";

$pidinput=$_GET['brid'];
$pidbr = decodeString($pidinput);

?>
<HTML>
<HEAD>
  <TITLE>Lihat Kartu Status</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>

<BODY onload="">
    <?PHP
    $query = "select brId as brid, aktivitas1, aktivitas2, tgl FROM hrd.br0 where brId='$pidbr'";
    $tampil=mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $ptanggal=$row['tgl'];
    $paktivitas1=$row['aktivitas1'];
    $paktivitas2=$row['aktivitas2'];
    
    $ptanggal=date("d/m/Y", strtotime($ptanggal));
    
    //echo $pidbr;
    echo "<table>";
        echo "<tr>";
            echo "<td nowrap>Tgl. </td> <td> : </td> <td>$ptanggal</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td nowrap>Aktivitas 1 </td> <td> : </td> <td>$paktivitas1</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td nowrap>Aktivitas 2 </td> <td> : </td> <td>$paktivitas2</td>";
        echo "</tr>";
    echo "</table>";
    ?>
</BODY>

</HTML>

<?PHP
hapusdata:
    mysqli_close($cnmy);
?>