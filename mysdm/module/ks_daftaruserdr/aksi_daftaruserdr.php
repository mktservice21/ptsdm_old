<?php

    date_default_timezone_set('Asia/Jakarta');
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $ppilihrpt="";
    
    if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Daftar Dokter.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    include("config/common.php");
    $cnit=$cnmy;
    
    $printdate= date("d/m/Y");
    
    
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    
?>

<?PHP
$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmpusrpldatadf01_".$puserid."_$now ";

$pstatus=$_POST['cb_status'];
$pidkaryawan=$_POST['cb_karyawan'];
$nmkaryawan= getfield("select nama as lcfields from hrd.karyawan where karyawanId='$pidkaryawan'");

$ppilihsts="All";
if ($pstatus=="Y") $ppilihsts="Aktif";
elseif ($pstatus=="N") $ppilihsts="Non Aktif";



$query = "SELECT 
    dokter.nsdoctorid, mr_dokt.dokterid, mr_dokt.karyawanid, mr_dokt.aktif, dokter.nama, dokter.aktif as aktif2, dokter.bagian, dokter.alamat1,
    dokter.alamat2, dokter.kota, dokter.tgllahir, dokter.telp, dokter.telp2, dokter.hp, spesial.initial as sp_nama
    FROM hrd.mr_dokt as mr_dokt 
    LEFT JOIN hrd.dokter as dokter ON mr_dokt.dokterid = dokter.dokterid
    LEFT JOIN hrd.spesial as spesial ON dokter.spid=spesial.spid
    WHERE mr_dokt.karyawanid = '".$pidkaryawan."'";

if ($pstatus=="Y") {
    $query .=" AND IFNULL(dokter.aktif,'')<>'N' AND IFNULL(mr_dokt.aktif,'')<>'N' ";
}elseif ($pstatus=="N") {
    $query .=" AND IFNULL(dokter.aktif,'')='N' AND IFNULL(mr_dokt.aktif,'')='N' ";
}
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


?>

<HTML>
<HEAD>
    <title>Daftar Dokter</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        

        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        

        
    <?PHP } ?>
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>


<BODY>
    
<?PHP
    echo '<strong>'.$nmkaryawan.'</strong><br>';
    
    $query2 = "select * from $tmp01 order by nama";
    $result2 = mysqli_query($cnit, $query2);
    $num_results = mysqli_num_rows($result2);
    echo 'Jumlah Dokter : '.$num_results.'<br>';

    $header_ = add_space('Nama Dokter',40);
    echo '<table border="1" cellspacing="0" cellpadding="1">';
        echo "<tr>\n";
            echo "<th style=background-color:LightSkyBlue><small>NUC</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>ID</small></th>";
            echo '<th style=background-color:LightSkyBlue align="left"><small>'.$header_."</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>RS/Bagian</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>Spesialisasi</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>Tempat Lahir</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>Tanggal Lahir</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>Cabang 1</small></th>";
            echo "<th><small>Cabang 2</small></th>";
            echo "<th><small>Cabang 3</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>Area 1</small></th>";
            echo "<th><small>Area 2</small></th>";
            echo "<th><small>Area 3</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>Alamat 1</small></th>";
            echo "<th><small>Kota</small></th>";
            echo "<th><small>Alamat 2</small></th>";
            echo "<th><small>Alamat 3</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>Telp 1</small></th>";
            echo "<th><small>Telp 2</small></th>";
            echo "<th><small>Telp 3</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>Divisi 1</small></th>";
            echo "<th><small>Divisi 2</small></th>";
            echo "<th><small>Divisi 3</small></th>";
            echo "<th style=background-color:LightSkyBlue><small>Nama MR 1</small></th>";
            echo "<th><small>Nama MR 2</small></th>";
            echo "<th><small>Nama MR 3</small></th>";
        echo "</tr>";

        for ($i=0; $i < $num_results; $i++)
        {
            $row = mysqli_fetch_array($result2);
            $dokter_id = substr($row['dokterid'],-6);
            if ($dokter_id <> '0000000000') {
                $dokter_nama = add_space($row['nama'],40);
                //echo '<small>'.$dokter_id.'&nbsp;&nbsp;'.$dokter_nama.
                //      '&nbsp;&nbsp;'.$row['bagian'].'</small><br>';
                echo "<tr>";
                if ($row['aktif']=='N') {
                    echo '<td align="right"><small><del>'.$row['nsdoctorid']."</del></small></td>";	
                    echo '<td align="right"><small><del>'.$dokter_id."</del></small></td>";
                    if (!empty($row['nama'])) {
                        echo '<td align="left"><small><del>'.$row['nama']."</del></small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['bagian'])) {
                        echo '<td align="left"><small><del>'.$row['bagian']."</del></small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['sp_nama'])) {
                        echo '<td align="left"><small><del>'.$row['sp_nama']."</del></small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    if (!empty($row['tgllahir'])) {
                        echo '<td align="left"><small><del>'.$row['tgllahir']."</del></small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }

                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';

                    if (!empty($row['alamat1'])) {
                        echo '<td align="left"><small><del>'.$row['alamat1']."</del></small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['kota'])) {
                        echo '<td align="left"><small><del>'.$row['kota']."</del></small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['alamat2'])) {
                        echo '<td align="left"><small>'.$row['alamat2']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    if (!empty($row['telp'])) {
                        echo '<td align="left"><small>'.$row['telp']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['telp2'])) {
                        echo '<td align="left"><small>'.$row['telp2']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['hp'])) {
                        echo '<td align="left"><small>'.$row['hp']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }


                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                } else {
                    echo '<td align="right"><small>'.$row['nsdoctorid']."</small></td>";		   
                    echo '<td align="right"><small>'.$dokter_id."</small></td>";
                    if (!empty($row['nama'])) {
                        echo '<td align="left"><small>'.$row['nama']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['bagian'])) {
                        echo '<td align="left"><small>'.$row['bagian']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['sp_nama'])) {
                        echo '<td align="left"><small>'.$row['sp_nama']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    if (!empty($row['alamat1'])) {
                        echo '<td align="left"><small>'.$row['alamat1']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['kota'])) {
                        echo '<td align="left"><small>'.$row['kota']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['alamat2'])) {
                        echo '<td align="left"><small>'.$row['alamat2']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    if (!empty($row['telp'])) {
                        echo '<td align="left"><small>'.$row['telp']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['telp2'])) {
                        echo '<td align="left"><small>'.$row['telp2']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }
                    if (!empty($row['hp'])) {
                        echo '<td align="left"><small>'.$row['hp']."</small></td>";
                    } else {
                        echo '<td align="left"><small>&nbsp;</small></td>';
                    }


                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';
                    echo '<td align="left"><small>&nbsp;</small></td>';

                }
                echo "</tr>";

            }
        }
    echo "</tr>";
echo "</table>\n";
?>
<br/><br/><br/><br/><br/><br/>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
        <style>
            #myBtn {
                display: none;
                position: fixed;
                bottom: 20px;
                right: 30px;
                z-index: 99;
                font-size: 18px;
                border: none;
                outline: none;
                background-color: red;
                color: white;
                cursor: pointer;
                padding: 15px;
                border-radius: 4px;
                opacity: 0.5;
            }

            #myBtn:hover {
                background-color: #555;
            }

        </style>

    <?PHP } ?>
        
        
        
        
</BODY>

    <script>
        // SCROLL
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
        // END SCROLL
    </script>
    
    

</HTML>

<?PHP
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");

    mysqli_close($cnit);
?>