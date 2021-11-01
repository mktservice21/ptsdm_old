<?PHP

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$ppilihrpt="";
$pmodule=$_GET['module'];


$ppilformat=1;
$ppilihrpt="";
if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];

if ($ppilihrpt=="excel") {
    $ppilformat=3;
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=Report Progres Budget Request.xls");
}


include("config/koneksimysqli.php");
include("config/fungsi_sql.php");
include("config/common.php");

$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];
$fregion=$_SESSION['REGION'];
$fnama_karyawan=$_SESSION['NAMALENGKAP'];
        
$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmprptprogres01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptprogres02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptprogres03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptprogres04_".$puserid."_$now ";
$tmp05 =" dbtemp.tmprptprogres05_".$puserid."_$now ";
$tmp06 =" dbtemp.tmprptprogres06_".$puserid."_$now ";


$pkryid = $_POST['cb_karyawan']; 
$pcabangid = $_POST['cb_cabang']; 
$pbln = $_POST['e_bulan'];
$pbln2 = $_POST['e_bulan2'];
$ptanggal01 = date('Y-m-01', strtotime($pbln));
$ptanggal02 = date('Y-m-t', strtotime($pbln2));
$pper01 = date('F Y', strtotime($pbln));
$pper02 = date('F Y', strtotime($pbln2));

$query = "SELECT * FROM dbproses.proses_expenses WHERE tanggal BETWEEN '$ptanggal01' AND '$ptanggal02' AND IFNULL(biaya,'')<>'N' ";
$query .= " AND divisi<>'OTC' ";
if ($fjbtid=="08") {
    $query .= " AND ( iddep IN ('SLS01') OR karyawanid='$fkaryawan' ) ";
}elseif ($fjbtid=="20") {
    $query .= " AND ( iddep IN ('SLS01', 'SLS03') OR karyawanid='$fkaryawan' ) ";
}elseif ($fjbtid=="05") {
    $query .= " AND ( iddep IN ('SLS01', 'SLS02', 'SLS03') OR karyawanid='$fkaryawan' ) ";
}else{
    
}

//if (!empty($pkryid)) $query .= " AND karyawanid='$pkryid' ";

if (!empty($pcabangid)) $query .= " AND icabangid='$pcabangid' ";
else{
    if ($fjbtid=="08") {
        $query .= " AND ( icabangid IN (select distinct IFNULL(icabangid,'') FROM sls.idm0 where karyawanid='$fkaryawan') OR karyawanid='$fkaryawan') ";
    }elseif ($fjbtid=="20") {
        $query .= " AND ( icabangid IN (select distinct IFNULL(icabangid,'') FROM sls.ism0 where karyawanid='$fkaryawan') OR karyawanid='$fkaryawan') ";
    }elseif ($fjbtid=="05") {
        
        $ppilregion="";
        if ($fregion=="B") $ppilregion="B";
        elseif ($fregion=="T") $ppilregion="T";
        
        $query .= " AND ( icabangid IN (select distinct IFNULL(icabangid,'') FROM mkt.icabang where region='$ppilregion') OR karyawanid='$fkaryawan') ";
    }else{
        
    }
}
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select kodeinput, keterangan_proses, divisi, idkodeinput, tanggal, tglinput, tgltrans, bulan, "
        . " tglmintadana, tgldanasby, "
        . " periode1, periode2, karyawanid, nama_pengaju as nama_karyawan, icabangid, "
        . " distid, dokterid, nama_realisasi, pcm, keterangan1, SUM(kredit) as jumlah "
        . " FROM $tmp01 ";
$query .="GROUP BY kodeinput, keterangan_proses, divisi, idkodeinput, tanggal, tglinput, tgltrans, bulan, "
        . " tglmintadana, tgldanasby, "
        . " periode1, periode2, karyawanid, nama_pengaju, icabangid, distid, dokterid, nama_realisasi, pcm, keterangan1";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }




//INSERT DARI BR0

$query = "select * from hrd.br0 WHERE tgl BETWEEN '$ptanggal01' AND '$ptanggal02' AND IFNULL(batal,'')<>'Y' AND IFNULL(retur,'')<>'Y' "
        . " AND brid NOT IN (select distinct IFNULL(brid,'') from hrd.br0_reject) ";
$query .=" AND karyawanid NOT IN (select distinct IFNULL(karyawanId,'') FROM hrd.karyawan WHERE divisiid='OTC')";
if (!empty($pcabangid)) $query .= " AND icabangid='$pcabangid' ";
else{
    if ($fjbtid=="08") {
        $query .= " AND ( icabangid IN (select distinct IFNULL(icabangid,'') FROM sls.idm0 where karyawanid='$fkaryawan') OR karyawanid='$fkaryawan') ";
    }elseif ($fjbtid=="20") {
        $query .= " AND ( icabangid IN (select distinct IFNULL(icabangid,'') FROM sls.ism0 where karyawanid='$fkaryawan') OR karyawanid='$fkaryawan') ";
    }elseif ($fjbtid=="05") {
        
        $ppilregion="";
        if ($fregion=="B") $ppilregion="B";
        elseif ($fregion=="T") $ppilregion="T";
        
        $query .= " AND ( icabangid IN (select distinct IFNULL(icabangid,'') FROM mkt.icabang where region='$ppilregion') OR karyawanid='$fkaryawan') ";
    }else{
        
    }
}
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "DELETE FROM $tmp03 WHERE brid IN (select distinct IFNULL(idkodeinput,'') FROM $tmp01 WHERE kodeinput='1')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "ALTER TABLE $tmp03 ADD COLUMN kodeinput VARCHAR(2), ADD COLUMN keterangan_proses VARCHAR(200), ADD COLUMN tanggal DATE DEFAULT '0000-00-00'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 SET kodeinput='1'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 as a JOIN (select DISTINCT kodeinput, keterangan_proses FROM $tmp01 WHERE kodeinput='1') as b on a.kodeinput=b.kodeinput "
        . " SET a.keterangan_proses=b.keterangan_proses";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 SET keterangan_proses='BUDGET REQUEST (ETHICAL)' WHERE kodeinput='1' AND IFNULL(keterangan_proses,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 SET tanggal=tgltrans";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
$query = "UPDATE $tmp03 SET tanggal=tgl WHERE IFNULL(tgltrans,'') in ('', '0000-00-00')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "INSERT INTO $tmp02 (kodeinput, keterangan_proses, divisi, idkodeinput, tanggal, tglinput, tgltrans, "
        . " karyawanid, dokterid, icabangid, nama_realisasi, keterangan1, pcm, jumlah)"
        . "SELECT kodeinput, keterangan_proses, divprodid, brid, tanggal, tgl, tgltrans, "
        . " karyawanid, dokterid, icabangid, realisasi1, aktivitas1, ca, jumlah "
        . " FROM $tmp03";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "DROP TEMPORARY TABLE $tmp03";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//END ISERT DARI BR0




$query = "ALTER TABLE $tmp02 ADD COLUMN nama_cabang VARCHAR(200), ADD COLUMN nama_dokter VARCHAR(200)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp02 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId SET a.nama_karyawan=b.nama WHERE "
        . " IFNULL(a.karyawanid,'') NOT IN ('', '0000002083', '0000002200')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


//ethical
$query = "UPDATE $tmp02 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid SET a.nama_cabang=b.nama WHERE "
        . " IFNULL(a.icabangid,'') NOT IN ('') and a.divisi NOT IN ('OTC')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//chc
$query = "UPDATE $tmp02 as a JOIN mkt.icabang_o as b on a.icabangid=b.icabangid_o SET a.nama_cabang=b.nama WHERE "
        . " IFNULL(a.icabangid,'') NOT IN ('') and a.divisi IN ('OTC')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//BR Ethical
$query = "UPDATE $tmp02 as a JOIN hrd.dokter as b on a.dokterid=b.dokterid SET a.nama_dokter=b.nama WHERE "
        . " IFNULL(a.dokterid,'') NOT IN ('')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//BR Ethical
$query = "UPDATE $tmp02 as a JOIN sls.distrib0 as b on a.distid=b.distid SET a.nama_dokter=b.nama WHERE "
        . " IFNULL(a.distid,'') NOT IN ('') AND IFNULL(nama_dokter,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "DELETE FROM $tmp02 WHERE IFNULL(jumlah,0)=0";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
?>


<HTML>
<HEAD>
  <TITLE>Report Progres Budget Request</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <?PHP
    if ($ppilihrpt=="excel") {
    }else{
        echo "<script src=\"vendors/jquery/dist/jquery.min.js\"></script>";
        echo "<link href=\"vendors/font-awesome/css/font-awesome.min.css\" rel=\"stylesheet\">";
    }
    ?>
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>
<script>
</script>

<BODY onload="initVar()">
    
    <?PHP
    if ($ppilihrpt=="excel") {
    }else{
        echo "<button onclick=\"topFunction()\" id=\"myBtn\" title=\"Go to top\">Top</button>";
    }
    ?>
    
    
    <?PHP
    
    echo "<div id='div_konten'>";
        
    
        echo "<b>Report Progres Budget Request</b><br/>";
        echo "<b>Periode : $pper01 s/d. $pper02</b><br/>";
        echo "<b>View By : $fnama_karyawan</b><br/>";

        $printdate= date("d/m/Y H:i");
        echo "<br/><i><small>view date : $printdate</small></i><br/>";

        echo "<hr/><br/>";
            
            
            
        echo "<table id='tbltable' border='1' class='table customerTable' cellspacing='0' cellpadding='1'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th align='center'><small>Divisi</small></th>";
                    echo "<th align='center'><small>ID</small></th>";
                    echo "<th align='center'><small>Tgl. Input BR</small></th>";
                    echo "<th align='center'><small>Tgl. Minta Dana Sby.</small></th>";
                    echo "<th align='center'><small>Tgl. Terima Dana Sby.</small></th>";
                    echo "<th align='center'><small>Tgl. Transfer Ke Realisasi</small></th>";
                    echo "<th align='center'><small>Nama Realisasi</small></th>";
                    echo "<th align='center'><small>Karyawan</small></th>";
                    echo "<th align='center'><small>User</small></th>";
                    echo "<th align='center'><small>Jumlah</small></th>";
                    echo "<th align='center'><small>Cabang</small></th>";
                    echo "<th align='center'><small>Keterangan</small></th>";
                    echo "<th align='center'><small>Status Dana</small></th>";
                echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

                $query = "select distinct IFNULL(kodeinput,'') as kodeinput, IFNULL(keterangan_proses,'') as keterangan_proses FROM $tmp02 ORDER BY 1,2";
                $tampil=mysqli_query($cnmy, $query);
                while ($row=mysqli_fetch_array($tampil)) {
                    $nkodeinput=$row['kodeinput'];
                    $nnamainput=$row['keterangan_proses'];

                    echo "<tr>";
                    echo "<td nowrap colspan='4'><b>$nnamainput</b></td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "</tr>";

                    $query = "select * FROM $tmp02 WHERE kodeinput='$nkodeinput' AND IFNULL(keterangan_proses,'')='$nnamainput' ORDER BY 1";
                    $tampil2=mysqli_query($cnmy, $query);
                    while ($row2=mysqli_fetch_array($tampil2)) {
                        $ndivisiid=$row2['divisi'];
                        $nidkodeinput=$row2['idkodeinput'];
                        $ntglinput=$row2['tglinput'];
                        $ntgltrans=$row2['tgltrans'];
                        $nblnaju=$row2['bulan'];
                        $nperiode1=$row2['periode1'];
                        $nperiode2=$row2['periode2'];
                        $nnmreal=$row2['nama_realisasi'];
                        $nnmkaryawan=$row2['nama_karyawan'];
                        $nnmuser=$row2['nama_dokter'];
                        $njumlah=$row2['jumlah'];
                        $nnmcabang=$row2['nama_cabang'];
                        $nket=$row2['keterangan1'];
                        $npcmket=$row2['pcm'];
                        
                        $ntglmintadana=$row2['tglmintadana'];//pengajuan nodivisi atau pengajuan dana ke SBY
                        $ntgldanasby=$row2['tgldanasby'];//transfer dana dari surabaya

                        $nnmdivisi=$ndivisiid;
                        if ($ndivisiid=="CAN" OR $ndivisiid=="CANAR") $nnmdivisi="CANARY";
                        elseif ($ndivisiid=="PEACO") $nnmdivisi="PEACOCK";
                        elseif ($ndivisiid=="PIGEO") $nnmdivisi="PIGEON";
                        elseif ($ndivisiid=="OTC") $nnmdivisi="CHC";
                        
                        if ($ntglinput=="0000-00-00" OR $ntglinput=="0000-00-00 00:00:00") $ntglinput="";
                        if ($ntgltrans=="0000-00-00" OR $ntgltrans=="0000-00-00 00:00:00") $ntgltrans="";
                        if ($nblnaju=="0000-00-00" OR $nblnaju=="0000-00-00 00:00:00") $nblnaju="";
                        if ($nperiode1=="0000-00-00" OR $nperiode1=="0000-00-00 00:00:00") $nperiode1="";
                        if ($nperiode2=="0000-00-00" OR $nperiode2=="0000-00-00 00:00:00") $nperiode2="";
                        if ($ntglmintadana=="0000-00-00" OR $ntglmintadana=="0000-00-00 00:00:00") $ntglmintadana="";
                        if ($ntgldanasby=="0000-00-00" OR $ntgldanasby=="0000-00-00 00:00:00") $ntgldanasby="";
                        
                        if (!empty($ntglinput)) $ntglinput = date('d/m/Y', strtotime($ntglinput));
                        if (!empty($ntgltrans)) $ntgltrans = date('d/m/Y', strtotime($ntgltrans));
                        if (!empty($nblnaju)) $nblnaju = date('F Y', strtotime($nblnaju));
                        if (!empty($nperiode1)) $nperiode1 = date('d/m/Y', strtotime($nperiode1));
                        if (!empty($nperiode2)) $nperiode2 = date('d/m/Y', strtotime($nperiode2));
                        if (!empty($ntglmintadana)) $ntglmintadana = date('d/m/Y', strtotime($ntglmintadana));
                        if (!empty($ntgldanasby)) $ntgldanasby = date('d/m/Y', strtotime($ntgldanasby));
                        $njumlah=BuatFormatNumberRp($njumlah, $ppilformat);//1 OR 2 OR 3
                        
                        if ($nkodeinput=="4" OR $nkodeinput=="11" OR ($nkodeinput=="5" AND $ndivisiid=="OTC")) {
                            if (!empty($nket)) $nket="Bulan : ".$nblnaju." - ".$nket;
                            else $nket="Bulan : ".$nblnaju;
                        }elseif ($nkodeinput=="5") {
                            if ($ndivisiid<>"OTC") {
                                if (!empty($nket)) $nket="Bulan : ".$nblnaju." (".$nperiode1." s/d. ".$nperiode2.") - ".$nket;
                                else $nket="Bulan : ".$nblnaju." (".$nperiode1." s/d. ".$nperiode2.")";
                            }
                        }
                        
                        $pnamapc="";
                        if ($npcmket=="Y") $pnamapc="Cash Advance (PCM)";
                        
                        echo "<tr>";
                        echo "<td nowrap>$nnmdivisi</td>";
                        echo "<td nowrap class='str'>$nidkodeinput</td>";
                        echo "<td nowrap>$ntglinput</td>";
                        echo "<td nowrap>$ntglmintadana</td>";
                        echo "<td nowrap>$ntgldanasby</td>";
                        echo "<td nowrap>$ntgltrans</td>";
                        echo "<td nowrap>$nnmreal</td>";
                        echo "<td nowrap>$nnmkaryawan</td>";
                        echo "<td >$nnmuser</td>";
                        echo "<td nowrap align='right'>$njumlah</td>";
                        echo "<td nowrap>$nnmcabang</td>";
                        echo "<td class='str'>$nket</td>";
                        echo "<td class='str'>$pnamapc</td>";
                        echo "</tr>";

                    }

                }

            echo "</tbody>";
        echo "</table>";
            

        
        
        echo "<br/>&nbsp;<br/>&nbsp;";
        echo "<br/>&nbsp;<br/>&nbsp;";
    
    
    echo "</div>";
    ?>
    
    
</BODY>


<?PHP
if ($ppilihrpt=="excel") {
}else{
?>
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
    
    <style>
        #btn_jenis {
            border: 1px solid #4CAF50;
            border-radius: 6px;
            background-color: white;
        }
        #btn_jenis:hover {
            cursor:pointer;
            background-color: #cccccc;
        }
        #btn_jenis:focus {
            border: 1px solid #cc0000;
            background-color: #fff;
        }
        #div_konten{
            
        }
    </style>
    
    <script>
        

    </script>
    
<?PHP
}
?>
    
    <style>
        #tbltable {
            border-collapse: collapse;
        }
        
        th {
            background-color: #ccccff;
            font-size : 16px;
            padding:5px;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }
        .th2 {
            background-color: #ccccff;
            position: sticky;
            top: 23;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            border-top: 0px solid #000;
        }
        tr td {
            font-size : 12px;
        }
        tr td {
            padding : 3px;
        }
        tr:hover {background-color:#f5f5f5;}
        thead tr:hover {background-color:#cccccc;}
    </style>
    
</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp05");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp06");
    mysqli_close($cnmy);
?>