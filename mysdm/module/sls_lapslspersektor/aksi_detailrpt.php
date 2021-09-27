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
    $ikry=$_GET['pkry'];
    $pdiv=$_GET['idiv'];
    $pval=$_GET['qval'];
    $pjns=$_GET['jns'];
    $pncpoth=$_GET['incpoth'];
    $piddist=$_GET['niddist'];
    
    
    $ppilihrpt="";
    if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Rincian Laporan Sales Per Sektor.xls");
    }
    
    
    
    include("config/koneksimysqli_ms.php");
    $cnmy=$cnms;
    
    
    $printdate= date("d/m/Y");
    
    $pidgroup=$_SESSION['GROUP'];
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
    $pidamkry=$_GET["pkry"];
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
    
    $query = "select nama from sls.icabang where icabangid='$pidcabang'";
    $tampil= mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampil);
    $pnamacabang_p=$nr['nama'];
    
    $query = "select nama from hrd.karyawan where karyawanid='$pidamkry'";
    $tampil= mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampil);
    $pnamaam_p=$nr['nama'];
    
    
    $pnmdistirbutor="";
    $query = "select nama from MKT.distrib0 where distid='$piddist'";
    $tampil= mysqli_query($cnms, $query);
    $rd= mysqli_fetch_array($tampil);
    $pnmdistirbutor=$rd['nama'];
    if (empty($piddist)) $pnmdistirbutor="All";
    
    
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
   
    
    $query = "select * from sls.ispv0 WHERE 1=1 ";
    if (!empty($pidamkry)) $query .=" AND karyawanid='$pidamkry' ";
    if (!empty($pdivisiid)) $query .=" AND divisiid='$pdivisiid' ";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $filtercabangarea="";
    $filterdivisicabarea="";
    $query ="select * from $tmp02";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu==0) {
        echo "data area tidak ada....";
        goto hapusdata;
    }
    while ($row= mysqli_fetch_array($tampil)) {
        $picabangid=$row['icabangid'];
        $pareaid=$row['areaid'];
        $pdividare=$row['divisiid'];
        
        if (strpos($filtercabangarea, $picabangid.$pareaid)==false) $filtercabangarea .="'".$picabangid.$pareaid."',";
        if (strpos($filterdivisicabarea, $picabangid.$pareaid.$pdividare)==false) $filterdivisicabarea .="'".$picabangid.$pareaid.$pdividare."',";
        
        
    }
    
    if (!empty($filtercabangarea)) $filtercabangarea="(".substr($filtercabangarea, 0, -1).")";
    if (!empty($filterdivisicabarea)) $filterdivisicabarea="(".substr($filterdivisicabarea, 0, -1).")";
    
    $query ="select icabangid, areaid, icustid, iprodid, sum(qty) as qty, round(sum(hna * qty), 0) ttotal FROM sls.mr_sales2 where "
            . " tgljual BETWEEN '$pbulan1' AND '$pbulan2' and icabangid='$pidcabang' ";
    $query .=" AND CONCAT(icabangid,areaid, divprodid) IN $filterdivisicabarea ";
    if ($pprodoth=="Y") {
    }else{
        $query .= " AND iprodid NOT IN (select IFNULL(iprodid,'') iprodid from sls.othproduk WHERE divprodid='PEACO')";
    }
    if (!empty($pidprod)) $query .= " AND iprodid='$pidprod'";
    if (!empty($piddist)) $query .= " AND distid='$piddist' ";
    $query .=" GROUP BY icabangid, areaid, icustid, iprodid ";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $pnamasektorpilih= "";
    $filidsektor="";
    $filidsektor_p="";
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
        }else{
            if ($ppilihsektor=="99") {
                $filidsektor="('99', '')";
            }
        }

        $pnamasektorpilih= "";
        if ($pjenissektor=="G") {
            $query = "select distinct '' as isektorid, nama_pvt as nama_sektor from MKT.isektor WHERE nama_pvt IN $filidsektor";

        }else{
            $query = "select distinct isektorid, nama as nama_sektor from MKT.isektor WHERE IFNULL(isektorid,'') IN $filidsektor";
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
        $filidsektor_=$filidsektor;
        
        $filidsektor= " AND IFNULL(isektorid,'') IN $filidsektor ";
        $filidsektor_p= " AND IFNULL(b.isektorid,'') IN $filidsektor_ ";
    }
    
    
    $query = "select * from mkt.icust WHERE CONCAT(icabangid,areaid,IFNULL(icustid,'')) "
            . " IN (select distinct CONCAT(IFNULL(icabangid,''),IFNULL(areaid,''),IFNULL(icustid,'')) FROM $tmp01) $filidsektor";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct a.iCabangId, a.areaId, a.iCustId, b.nama, b.alamat1, b.alamat2, b.kodepos, b.contact, b.telp, b.fax, "
            . " b.iKotaId, b.kota, b.iSektorId, b.aktif, b.dispen, b.User1, b.oldFlag, b.scode, b.grp, b.grp_spp, b.istatus, b.iCustId_old "
            . " from $tmp01 as a LEFT JOIN mkt.icust as b on a.icustid=b.iCustId "
            . " WHERE 1=1 $filidsektor_p";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp03 SET iSektorId='99' WHERE IFNULL(iSektorId,'')=''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $lcfieldpil=" b.isektorid, ise.nama nama_sektor ";
    if ($pjenissektor=="G") {
        $lcfieldpil=" ise.grp_pvt as isektorid, ise.nama_pvt nama_sektor ";
    }else{
    }
    
    $query = "UPDATE $tmp03 SET iSektorId='99' WHERE IFNULL(iSektorId,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "select a.icabangid, c.nama nama_cabang, a.areaid, d.nama nama_area, a.iprodid, e.nama nama_produk, "
            . " a.icustid, b.nama, b.alamat1, b.alamat2, b.kodepos, b.contact, b.telp, b.fax, b.ikotaid, b.kota, "
            . " $lcfieldpil, "
            . " sum(qty) as qty, sum(ttotal) as ttotal "
            . " FROM $tmp03 b JOIN $tmp01 a on a.icustid=b.icustid "
            . " LEFT JOIN sls.icabang c on a.icabangid=c.icabangid "
            . " LEFT JOIN sls.iarea d on a.icabangid=d.icabangid AND a.areaid=d.areaid "
            . " LEFT JOIN sls.iproduk e on a.iprodid=e.iprodid "
            . " LEFT JOIN MKT.isektor ise on b.iSektorId = ise.iSektorId ";
    $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18";
    //echo $query;
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
            . " a.icustid=b.icustid SET "
            . " a.qty=b.qty, a.ttotal=b.ttotal"; 
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //$query = "DELETE FROM $tmp04 WHERE IFNULL(qty,0)=0 AND IFNULL(ttotal,0)=0";
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
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
            
            .btn_1 {
              background-color: #cc6600;
              border: none;
              color: white;
              padding: 3px 8px;
              text-align: center;
              text-decoration: none;
              display: inline-block;
              font-size: 11px;
              margin: 2px 1px;
              cursor: pointer;
              box-shadow: 0 5px 5px 0 rgba(0,0,0,0.2), 0 3px 7px 0 rgba(0,0,0,0.19);
            }
            .btn_1:hover {
                background-color: #ffff99;
              color: #000;
            }
            
            .button1 {background-color: #4CAF50;}
            
            @media print
            {    
                .no-print, .no-print *
                {
                    display: none !important;
                }
            }
        </style>
        
        <?PHP } ?>
</HEAD>


<BODY>
    
<div class='modal fade' id='myModal' role='dialog' class='no-print'></div>

<div id='n_content'>
    
    <center><div class='h1judul'>Rincian Laporan Sales Per Sektor</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Cabang</td><td>:</td><td><?PHP echo "$pnamacabang_p"; ?></td></tr>
            <tr><td>Distributor</td><td>:</td><td><?PHP echo "$pnmdistirbutor"; ?></td></tr>
            <tr><td>AM</td><td>:</td><td><?PHP echo "$pnamaam_p"; ?></td></tr>
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
                echo "<a class='no-print btn button1' href='eksekusi3.php?module=$pmodule&act=input&idmenu=$pidmenu&ket=excel&ipilih=$ppilih&iprd=$pprod&pper1=$iper1&iprd=$pprod&pper2=$iper2&pper2=$iper2&pcb=$icab&pkry=$ikry&idiv=$pdiv&qval=$pval&jns=$pjns&incpoth=$pncpoth&niddist=$piddist' target='_blank'>EXCEL</a>";
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

                    $ptombolubahsektor="<span id='spn_ki' class='no-print'><button type='button' class='no-print btn_1 btn-info btn-xs' data-toggle='modal' "
                            . " data-target='#myModal' onClick=\"TampilUbahSektorCust('$pidcust')\">Update Sektor</button></span>";
                    if ($ppilihrpt=="excel" OR ($pidgroup <> "1" AND $pidgroup <> "24" AND $pidgroup <> "33") ) {
                        $ptombolubahsektor="";
                    }else{
                        
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmarea</td>";
                    echo "<td nowrap>$pidcust $ptombolubahsektor</td>";
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
    
    
    <?PHP
        if ($ppilihrpt=="excel" OR ($pidgroup <> "1" AND $pidgroup <> "24" AND $pidgroup <> "33") ) {
        }else{
            ?>
            <!-- jQuery -->
            <script src="vendors/jquery/dist/jquery.min.js"></script>
            <!-- Bootstrap -->
            <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

            <!-- Custom Theme Scripts -->
            <script src="build/js/custom.min.js"></script>
            
            <script>
                function TampilUbahSektorCust(eid){
                    $.ajax({
                        type:"post",
                        url:"module/sls_lapslspersektor/update_sektorcust.php?module=viewdatacust",
                        data:"uid="+eid,
                        success:function(data){
                            $("#myModal").html(data);
                        }
                    });
                }
            </script>
            
            
            <?PHP
        }
    ?>
            
            
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