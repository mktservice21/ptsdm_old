<?php
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    $ppilih=$_GET['ipilih'];
    $pprod=$_GET['iprd'];
    $iper1=$_GET['pper1'];
    $iper2=$_GET['pper2'];
    $icab=$_GET['pcb'];
    $iregi=$_GET['pregi'];
    $pdiv=$_GET['idiv'];
    $pval=$_GET['qval'];
    $pjns=$_GET['jns'];
    $pncpoth=$_GET['incpoth'];
    
    
    
    $ppilihrpt="";
    if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Rincian Laporan Sales Per Sektor Region.xls");
    }
    
    
    include("config/koneksimysqli_ms.php");
    $cnmy=$cnms;
    
    
    $printdate= date("d/m/Y");
    
    $karyawanid=$_SESSION['IDCARD'];
    $ptgl1=$_GET['pper1'];
    $ptgl2=$_GET['pper2'];
    
    $pbulan1 = date("Y-m-01", strtotime($ptgl1));
    $pbulan2 = date("Y-m-t", strtotime($ptgl2));
    
    $ptgl1 = date("F Y", strtotime($ptgl1));
    $ptgl2 = date("F Y", strtotime($ptgl2));
    
    $date1=date_create($pbulan1);
    $date2=date_create($pbulan2);
    $pidcabang=$_GET["pcb"];
    $pidregion=$_GET["pregi"];
    $pdivisiid=$_GET["idiv"];
    $pjenissektor=$_GET["jns"];
    $pplhothpea=$_GET["incpoth"];
    $ppilhqtyval=$_GET["qval"];
    
    $ppilihsektor=$_GET["ipilih"];//jenis sektor atau group seperti APOTIK DLL
    $pidprod=$_GET["iprd"];
    
    $pnamaprod="";
    if (!empty($pidprod)) {
        $query = "select nama from sls.iproduk where iprodid='$pidprod'";
        $tampilkan= mysqli_query($cnmy, $query);
        $nro= mysqli_fetch_array($tampilkan);
        $pnamaprod=$nro['nama'];
    }
    
    
    $pprodoth = "";
    $pplhothpea = "";
    if (isset($_GET["incpoth"])) $pprodoth=$_GET["incpoth"];
    
    $pplhothpea=$pprodoth;
    if ($pprodoth!="Y") $pplhothpea = "N";
    
    $pnamacabang_p = "ALL";
    if (!empty($pidcabang)) {
        $query = "select nama from sls.icabang where icabangid='$pidcabang'";
        $tampil= mysqli_query($cnmy, $query);
        $nr= mysqli_fetch_array($tampil);
        $pnamacabang_p=$nr['nama'];
    }
    
    $pnamaam_p="BARAT";
    if ($pidregion=="T") $pnamaam_p="TIMUR";
    
    $pnamadivisi_p="ALL";
    if (!empty($pdivisiid)) {
        $query = "select nama from ms.divprod where divprodid='$pdivisiid'";
        $tampilv= mysqli_query($cnmy, $query);
        $nv= mysqli_fetch_array($tampilv);
        $pnamadivisi_p=$nv['nama'];
    }
    
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp01 =" dbtemp.tmplapslssektor01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplapslssektor02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmplapslssektor03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmplapslssektor04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmplapslssektor05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmplapslssektor06_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmplapslssektor07_".$puserid."_$now ";
    $tmp08 =" dbtemp.tmplapslssektor08_".$puserid."_$now ";
    $tmp09 =" dbtemp.tmplapslssektor09_".$puserid."_$now ";
    $tmp10 =" dbtemp.tmplapslssektor10_".$puserid."_$now ";
   
    $pidcard=$_SESSION['IDCARD'];
    $pjabatanid=$_SESSION['JABATANID'];
    
    if ((INT)$pjabatanid==20 OR $pjabatanid=="20") {
        $query = "select a.* from sls.ism0 a JOIN sls.icabang b on a.icabangid=b.icabangid WHERE "
                . " a.karyawanid='$pidcard' AND b.region='$pidregion'";
    }else{
        $query = "select distinct a.icabangid from sls.icabang a WHERE a.region='$pidregion'";
    }
    if (!empty($pidcabang)) $query .=" AND a.icabangid='$pidcabang' ";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $filtercabang="";
    $query ="select * from $tmp02";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu==0) {
        echo "data area tidak ada....";
        goto hapusdata;
    }
    while ($row= mysqli_fetch_array($tampil)) {
        $picabangid=$row['icabangid'];
        
        if (strpos($filtercabang, $picabangid)==false) $filtercabang .="'".$picabangid."',";
        
        
    }
    
    if (!empty($filtercabang)) $filtercabang="(".substr($filtercabang, 0, -1).")";
    else $filtercabang="('')";
    
    $query ="select icabangid, areaid, icustid, iprodid, sum(qty) as qty, round(sum(hna * qty), 0) ttotal from sls.mr_sales2 where "
            . " tgljual BETWEEN '$pbulan1' AND '$pbulan2' ";
    $query .=" AND icabangid IN $filtercabang ";
    if (!empty($pdivisiid)) $query .=" AND divprodid ='$pdivisiid' ";
    if ($pprodoth=="Y") {
    }else{
        $query .= " AND iprodid NOT IN (select IFNULL(iprodid,'') iprodid from sls.othproduk WHERE divprodid='PEACO')";
    }
    if (!empty($pidprod)) $query .= " AND iprodid='$pidprod'";
    $query .=" GROUP BY icabangid, areaid, icustid, iprodid ";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $pnamasektorpilih= "";
    $filidsektor="";
    if (!empty($ppilihsektor)) {
    
        $pplsektor = explode(",", $ppilihsektor);
        foreach ($pplsektor as $sektorid) {
            if (!empty($sektorid)) $filidsektor .="'".$sektorid."',";
        }
        if (!empty($filidsektor)) $filidsektor="(".substr($filidsektor, 0, -1).")";
        else $filidsektor="('')";


        $filsektor2="";
        if ($pjenissektor=="G") {
            if ($ppilihsektor==="02,06") {
                $query = "select distinct isektorid, nama as nama_sektor from MKT.isektor WHERE nama_pvt IN ('LAIN - LAIN')";
            }else{
                $query = "select distinct isektorid, nama as nama_sektor from MKT.isektor WHERE nama_pvt IN $filidsektor";
            }
            $tampil= mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pnmsektor=$row['nama_sektor'];
                $pidsektor=$row['isektorid'];

                if (!empty($pidsektor)) $filsektor2 .="'".$pidsektor."',";
            }


            if (!empty($filsektor2)) $filsektor2="(".substr($filsektor2, 0, -1).")";
            else $filsektor2="('')";
        }

        $pnamasektorpilih= "";
        if ($pjenissektor=="G") {
            $query = "select distinct '' as isektorid, nama_pvt as nama_sektor from MKT.isektor WHERE nama_pvt IN $filidsektor";

        }else{
            $query = "select distinct isektorid, nama as nama_sektor from MKT.isektor WHERE isektorid IN $filidsektor";
        }
        $tampil= mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pnmsektor=$row['nama_sektor'];
            $pidsektor=$row['isektorid'];

            if (!empty($pnmsektor)) $pnamasektorpilih .=$pnmsektor.", ";
        }
        if (!empty($pnamasektorpilih)) $pnamasektorpilih=substr($pnamasektorpilih, 0, -2);


        if ($ppilihsektor==="02,06" AND $pjenissektor=="G") {
            $pnamasektorpilih = "LAIN - LAIN";
        }

        if ($pjenissektor=="G") $filidsektor=$filsektor2;
    
        
    }
    
    
    if (!empty($ppilihsektor)) {
        $filidsektor= " AND isektorid IN $filidsektor ";
    }
    
    
    
    $query = "select * from sls.icust WHERE CONCAT(icabangid,areaid,IFNULL(icustid,'')) "
            . " IN (select distinct CONCAT(IFNULL(icabangid,''),IFNULL(areaid,''),IFNULL(icustid,'')) FROM $tmp01) $filidsektor";
    
    $query = "select distinct a.* from sls.icust a JOIN $tmp01 b on a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.icustid=b.icustid WHERE 1=1 $filidsektor";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $lcfieldpil=" b.isektorid, ise.nama nama_sektor ";
    if ($pjenissektor=="G") {
        $lcfieldpil=" ise.grp_pvt as isektorid, ise.nama_pvt nama_sektor ";
    }
    $query = "UPDATE $tmp03 SET iSektorId='99' WHERE IFNULL(iSektorId,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.icabangid, c.nama nama_cabang, a.areaid, d.nama nama_area, a.iprodid, e.nama nama_produk, "
            . " a.icustid, b.nama, b.alamat1, b.alamat2, b.kodepos, b.contact, b.telp, b.fax, b.ikotaid, b.kota, "
            . " $lcfieldpil, "
            . " sum(qty) as qty, sum(ttotal) as ttotal "
            . " FROM $tmp03 b JOIN $tmp01 a on a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.icustid=b.icustid "
            . " LEFT JOIN sls.icabang c on b.icabangid=c.icabangid "
            . " LEFT JOIN sls.iarea d on b.icabangid=d.icabangid AND b.areaid=d.areaid "
            . " LEFT JOIN sls.iproduk e on a.iprodid=e.iprodid "
            . " LEFT JOIN MKT.isektor ise on b.iSektorId = ise.iSektorId ";
    $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.icabangid, b.nama nama_cabang, a.areaid, c.nama nama_area, "
            . " a.icustid, a.nama, a.alamat1, a.alamat2, a.kodepos, a.contact, a.telp, a.fax, a.ikotaid, a.kota, "
            . " CAST(0 as DECIMAL(20,2)) as qty, CAST(0 as DECIMAL(20,2)) as ttotal "
            . " from $tmp03 a LEFT JOIN sls.icabang b on a.icabangid=b.icabangid "
            . " LEFT JOIN sls.iarea c on a.icabangid=c.icabangid AND a.areaid=c.areaid";
    //$query = "create TEMPORARY table $tmp04 ($query)"; 
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp04 a JOIN (select icabangid, areaid, icustid, sum(qty) as qty, sum(ttotal) as ttotal from $tmp01 GROUP BY 1,2,3) b on "
            . " a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.icustid=b.icustid SET "
            . " a.qty=b.qty, a.ttotal=b.ttotal"; 
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp04 WHERE IFNULL(qty,0)=0 AND IFNULL(ttotal,0)=0";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>


<HTML>
<HEAD>
    <title>Laporan Sales Per Sektor (Rincial Sektor)</title>
    
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
    <?PHP if ($ppilihrpt!="excel") { ?>
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
		
		
		
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
        <style>
            .btn {
              background-color: #4CAF50; /* Green */
              border: none;
              color: white;
              padding: 7px 17px;
              text-align: center;
              text-decoration: none;
              display: inline-block;
              font-size: 14px;
              margin: 2px 1px;
              cursor: pointer;
              box-shadow: 0 5px 5px 0 rgba(0,0,0,0.2), 0 3px 7px 0 rgba(0,0,0,0.19);
            }
            .btn:hover {
              background-color: #e7e7e7;
              color: #000;
            }
            .button1 {background-color: #4CAF50;}
        </style>
        
    <?PHP } ?>
</HEAD>


<BODY>
    
    
<div id='n_content'>
    
    <center><div class='h1judul'>Rincian Laporan Sales Per Sektor</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Region</td><td>:</td><td><?PHP echo "$pnamaam_p"; ?></td></tr>
            <tr><td>Cabang</td><td>:</td><td><?PHP echo "$pnamacabang_p"; ?></td></tr>
            <tr><td>Divisi</td><td>:</td><td><?PHP echo "$pnamadivisi_p"; ?></td></tr>
            <tr><td>Periode</td><td>:</td><td><?PHP echo "$ptgl1 s/d. $ptgl2"; ?></td></tr>
            <?PHP
            if (!empty($pnamaprod)) {
                //echo "<tr><td>Produk</td><td>:</td><td>$pnamaprod</td></tr>";
            }
            if (!empty($pnamasektorpilih)) {
                //if ($pjenissektor=="G") echo "<tr><td>Grp. Sektor</td><td>:</td><td>$pnamasektorpilih</td></tr>";
                //else echo "<tr><td>Sektor</td><td>:</td><td>$pnamasektorpilih</td></tr>";
            }
            if ($pprodoth=="Y") {
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>Include Produk Other Peacock</td></tr>";
            }else{
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>Tanpa Produk Other Peacock</td></tr>";
            }
            ?>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    
    <?PHP
    if ($ppilihrpt!="excel") {
        echo "<table>";
        echo "<tr>";
            echo "<td>";
                echo "<a class='btn button1' href='eksekusi3.php?module=$pmodule&act=input&idmenu=$pidmenu&ket=excel&ipilih=$ppilih&iprd=$pprod&pper1=$iper1&iprd=$pprod&pper2=$iper2&pper2=$iper2&pcb=$icab&pregi=$iregi&idiv=$pdiv&qval=$pval&jns=$pjns&incpoth=$pncpoth' target='_blank'>EXCEL</a>";
            echo "</td>";
            echo "<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>";
            echo "<td>";
            echo "</td>";
        echo "</tr>";
        echo "</table>";
    }
    ?>
    <hr/>
    
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
        <tr>
            <th align='center'>No</th>
            <th align='center'>Area</th>
            <th align='center'>Kode Cust.</th>
            <th align='center'>Nama Customer</th>
            <th align='center'>Alamat 1</th>
            <th align='center'>Alamat 2</th>
            <th align='center'>Kode Pos</th>
            <th align='center'>Telp.</th>
            <th align='center'>Kota</th>
            <th align='center'>Produk</th>
            <th align='center'>Total</th>
        </tr>
        </thead>
        <tbody>
            <?PHP
            $pigtotalqty=0;
            $pigtotalval=0;
                
            $query = "select distinct isektorid, nama_sektor from $tmp04 ORDER BY nama_sektor, isektorid";
            $tampil1= mysqli_query($cnmy, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $pidsektor=$row1['isektorid'];
                $pnmsektor=$row1['nama_sektor'];
                
                echo "<tr>";
                echo "<td nowrap ><b>&nbsp;</b></td>";
                echo "<td nowrap colspan='10'> <b>$pnmsektor<b></td>";
                echo "</tr>";
                    
                    
                $pgtotalqty=0;
                $pgtotalval=0;
                $no=1;
                $query = "select * from $tmp04 WHERE isektorid='$pidsektor' ORDER BY nama_cabang, nama_area, nama, nama_produk";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pnmcab=$row['nama_cabang'];
                    $pnmarea=$row['nama_area'];
                    $pidcust=$row['icustid'];
                    $pnmcust=$row['nama'];
                    $palamat1=$row['alamat1'];
                    $palamat2=$row['alamat1'];
                    $pkodepos=$row['kodepos'];
                    $ptelp=$row['telp'];
                    $pkota=$row['kota'];
                    $pnmproduk=$row['nama_produk'];

                    $ptqty=$row['qty'];
                    $ptval=$row['ttotal'];

                    $pgtotalqty=(double)$pgtotalqty+(double)$ptqty;
                    $pgtotalval=(double)$pgtotalval+(double)$ptval;
                    
                    $pigtotalqty=(double)$pigtotalqty+(double)$ptqty;
                    $pigtotalval=(double)$pigtotalval+(double)$ptval;

                    if ($ppilhqtyval=="Q") {
                        $prjumlah=$ptqty;
                    }else{
                        $prjumlah=$ptval;
                    }

                    $ptqty=number_format($ptqty,0,",",",");
                    $ptval=number_format($ptval,0,",",",");

                    $prjumlah=number_format($prjumlah,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmarea</td>";
                    echo "<td nowrap>$pidcust</td>";
                    echo "<td nowrap>$pnmcust</td>";
                    echo "<td nowrap>$palamat1</td>";
                    echo "<td nowrap>$palamat2</td>";
                    echo "<td nowrap>$pkodepos</td>";
                    echo "<td nowrap>$ptelp</td>";
                    echo "<td nowrap>$pkota</td>";
                    echo "<td nowrap>$pnmproduk</td>";
                    echo "<td nowrap align='right'>$prjumlah</td>";
                    echo "</tr>";

                    $no++;
                }

                if ($ppilhqtyval=="Q") {
                    $prjumlah=$pgtotalqty;
                }else{
                    $prjumlah=$pgtotalval;
                }


                $pgtotalqty=number_format($pgtotalqty,0,",",",");
                $pgtotalval=number_format($pgtotalval,0,",",",");
                $prjumlah=number_format($prjumlah,0,",",",");


                if ($ppilhqtyval=="Q" AND !empty($pidprod)) {
                    echo "<tr>";
                    echo "<td nowrap colspan='10' align='right'> <b>Total Qty : <b></td>";
                    echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                    echo "</tr>";

                    echo "<tr>";
                    echo "<td nowrap colspan='10' align='right'> <b>Total Value : <b></td>";
                    echo "<td nowrap align='right'><b>$pgtotalval</b></td>";
                    echo "</tr>";
                }else{
                    echo "<tr>";
                    echo "<td nowrap colspan='10' align='right'> <b>Total Value : <b></td>";
                    echo "<td nowrap align='right'><b>$pgtotalval</b></td>";
                    echo "</tr>";
                }
                
                
            }
            
                echo "<tr>";
                echo "<td nowrap colspan='11' align='right'>&nbsp;</td>";
                echo "</tr>";
                    
            if ($ppilhqtyval=="Q") {
                $prjumlah=$pigtotalqty;
            }else{
                $prjumlah=$pigtotalval;
            }


            $pigtotalqty=number_format($pigtotalqty,0,",",",");
            $pigtotalval=number_format($pigtotalval,0,",",",");
            $prjumlah=number_format($prjumlah,0,",",",");


            if ($ppilhqtyval=="Q" AND !empty($pidprod)) {
                echo "<tr>";
                echo "<td nowrap colspan='10' align='right'> <b>Grand Total Qty : <b></td>";
                echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td nowrap colspan='10' align='right'> <b>Grand Total Value : <b></td>";
                echo "<td nowrap align='right'><b>$pigtotalval</b></td>";
                echo "</tr>";
            }else{
                echo "<tr>";
                echo "<td nowrap colspan='10' align='right'> <b>Grand Total Value : <b></td>";
                echo "<td nowrap align='right'><b>$pigtotalval</b></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
        </table>
    
    
    
</div>
    
    <?PHP if ($ppilihrpt!="excel") { ?>
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

        <style>
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td { 
                font-size: 11px;
            }
        </style>
        
        <style>

        table {
            text-align: left;
            position: relative;
            border-collapse: collapse;
            background-color:#FFFFFF;
        }

        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }

        .th2 {
            background: white;
            position: sticky;
            top: 35;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            border-top: 1px solid #000;
        }
        </style>
        
    <?PHP } ?>
</BODY>
</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp08");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp09");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
    mysqli_close($cnmy);
?>