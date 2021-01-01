<?php
    function BuatFormatNum($prp, $ppilih) {
        if (empty($prp)) $prp=0;
        
        $numrp=$prp;
        if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
        elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
            
        return $numrp;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
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
        header("Content-Disposition: attachment; filename=Laporan Rincian Biaya Rutin CHC.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    include("config/common.php");
    
    $printdate= date("d/m/Y");
    
    
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $ppilformat="1";
    if (($picardid=="0000000143" OR $picardid=="0000000329") AND $ppilihrpt=="excel") {
        $ppilformat="2";
    }
    
?>


<?PHP

$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmprptrtnrincichc01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptrtnrincichc02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptrtnrincichc03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptrtnrincichc04_".$puserid."_$now ";


$filterjenis=('');
if (!empty($_POST['chkbox_jnsobat'])){
    $filterjenis=$_POST['chkbox_jnsobat'];
    $filterjenis=PilCekBoxAndEmpty($filterjenis);
}



$pidkaryawan=$_POST['cb_karyawan'];
$nmkaryawan= getfield("select nama as lcfields from hrd.karyawan where karyawanId='$pidkaryawan'");
$ppilihkry="";
$pfilterkry="";
if (!empty($pidkaryawan)) {
    $pfilterkry=" AND karyawanid='$pidkaryawan' ";
    $ppilihkry="($nmkaryawan)";
}
        
        
$tgl01 = $_POST['e_tgl1'];
$tgl02 = $_POST['e_tgl2'];
$psortby = $_POST['cb_sortby'];


$pperiode1 = date("Y-m", strtotime($tgl01));
$pperiode2 = date("Y-m", strtotime($tgl02));

$myperiode1 = date("F Y", strtotime($tgl01));
$myperiode2 = date("F Y", strtotime($tgl02));
        

$query = "select divisi, idrutin, kodeperiode, bulan, karyawanid, nama_karyawan as nmkaryawan "
        . " FROM dbmaster.t_brrutin0 WHERE IFNULL(stsnonaktif,'')<>'Y' "
        . " AND IFNULL(kode,0)=1 AND IFNULL(divisi,'') = 'OTC' "
        . " $pfilterkry "
        . " AND DATE_FORMAT(bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select b.divisi, b.kodeperiode, b.bulan, b.karyawanid, b.nmkaryawan, "
        . " a.nourut, a.idrutin, a.nobrid, a.coa, a.obat_untuk, a.tgl1, a.tgl2, a.notes, a.alasanedit_fin, "
        . " a.qty, a.rp, a.rptotal  "
        . " from dbmaster.t_brrutin1 as a JOIN $tmp01 as b on a.idrutin=b.idrutin WHERE 1=1 "
        . " AND a.nobrid in $filterjenis";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "DELETE FROM $tmp02 WHERE IFNULL(rptotal,0)=0";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select a.divisi, a.kodeperiode, a.bulan, a.karyawanid, a.nmkaryawan, d.nama as nama_karyawan, "
        . " a.nourut, a.idrutin, a.nobrid, b.nama as nama_id, a.coa, c.NAMA4 as nama_coa4, a.obat_untuk, a.tgl1, a.tgl2, a.notes, a.alasanedit_fin, "
        . " a.qty, a.rp, a.rptotal "
        . " from $tmp02 as a "
        . " LEFT JOIN dbmaster.t_brid as b on a.nobrid=b.nobrid "
        . " LEFT JOIN dbmaster.coa_level4 as c on a.coa=c.COA4 "
        . " LEFT JOIN hrd.karyawan as d on a.karyawanid=d.karyawanid";
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        
$query = "UPDATE $tmp03 a set a.nama_karyawan=a.nmkaryawan, karyawanid=idrutin WHERE karyawanid IN ('0000002200', '0000002083')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



$arriddiv[]="";
$arridcoa[]="";
$arrnmcoa[]="";
$query = "select c.DIVISI2, a.COA4, a.NAMA4 from dbmaster.coa_level4 as a "
        . " JOIN dbmaster.coa_level3 as b on a.COA3=b.COA3 "
        . " JOIN dbmaster.coa_level2 as c on b.COA2=c.COA2 "
        . " WHERE IFNULL(c.DIVISI2,'') IN ('OTC', 'CHC') "
        . " ORDER BY c.DIVISI2, a.COA4";
$tampilk= mysqli_query($cnmy, $query);
while ($zr= mysqli_fetch_array($tampilk)) {
    $ziddiv=$zr['DIVISI2'];
    $zidcoa=$zr['COA4'];
    $znmcoa=$zr['NAMA4'];

    $arriddiv[]=$ziddiv;
    $arridcoa[]=$zidcoa;
    $arrnmcoa[]=$znmcoa;
}

//echo "$myperiode1 s/d. $myperiode2<br/>";
//goto hapusdata;

?>
<HTML>
<HEAD>
    <title>Laporan Rincian Biaya Rutin CHC</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
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
        

        
    <?PHP } ?>
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>


<BODY>
    
<div class='modal fade' id='myModal' role='dialog'></div>
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>

<div id='n_content'>

    <center><div class='h1judul'>Laporan Rincian Biaya Rutin CHC</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Periode</td><td>:</td><td><?PHP echo "<b>$myperiode1 s/d. $myperiode2</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th align="center" nowrap>No</th>
                <?PHP
                if ($psortby=="periode") {
                    echo "<th align='center' nowrap>PERIODE</th>";
                    echo "<th align='center' nowrap>ID KARYAWAN</th>";
                    echo "<th align='center' nowrap>KARYAWAN</th>";
                }else{
                    echo "<th align='center' nowrap>ID KARYAWAN</th>";
                    echo "<th align='center' nowrap>KARYAWAN</th>";
                    echo "<th align='center' nowrap>PERIODE</th>";
                }
                ?>
                
                <th align="center" nowrap>ID</th>
                <th align="center" nowrap>NAMA ID</th>
                <th align="center" nowrap>COA</th>
                <th align="center" nowrap>NAMA PERKIRAAN</th>
                <th align="center" nowrap>TGL. RINCI</th>
                <th align="center" nowrap>ATAS NAMA</th>
                <th align="center" nowrap>JUMLAH</th>
                <th align="center" nowrap>KETERANGAN</th>
                <th align="center" nowrap>COA EDIT</th>
                <th align="center" nowrap></th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $ptotal=0;
            $no=1;
            $query = "select * from $tmp03 ";
            if ($psortby=="periode") {
                $query .= " order by DATE_FORMAT(bulan,'%Y%m'), kodeperiode, nama_karyawan, idrutin, nobrid";
            }else{
                $query .= " order by nama_karyawan, DATE_FORMAT(bulan,'%Y%m'), kodeperiode, idrutin, nobrid";
            }
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                
                $pidurut=$row['nourut'];
                $piddivisi=$row['divisi'];
                $pidrutin=$row['idrutin'];
                $pnoid=$row['nobrid'];
                $pidkary=$row['karyawanid'];
                $pnmkary=$row['nama_karyawan'];
                $pblnp=$row['bulan'];
                $pkodper=$row['kodeperiode'];
                $pnamaid=$row['nama_id'];
                $pcoakode=$row['coa'];
                $pcoanama=$row['nama_coa4'];
                $puntuk=$row['obat_untuk'];
                $pket=$row['notes'];
                $palasandin=$row['alasanedit_fin'];
                if (!empty($palasandin)) $pket=$palasandin;
                $pobatuntuk="";
                if ($puntuk=="1") $pobatuntuk="Istri";
                if ($puntuk=="2") $pobatuntuk="Anak";
                $prptotal=$row['rptotal'];
                $ptotal=(double)$ptotal+(double)$prptotal;
                $ptgl="";
                if (!empty($row['tgl1']) AND $row['tgl1']<>"0000-00-00") $ptgl = date("d/m/Y", strtotime($row['tgl1']));
                
                if ($ppilihrpt=="excel") {
                    $pblnp = date("d F Y", strtotime($pblnp));
                }else{
                    $pblnp = date("F Y", strtotime($pblnp));
                }

                $prptotal=BuatFormatNum($prptotal, $ppilformat);


                $pcbselect = "<select class='soflow' id='cb_coa[$pidurut]' name='cb_coa[$pidurut]'>";
                $pcbselect .="<option value=''>--Pilih--</option>";
                for($ix=1;$ix<count($arridcoa);$ix++) {
                    $ziddiv=$arriddiv[$ix];
                    
                    if ($ziddiv==$piddivisi) {
                        $zidcoa=$arridcoa[$ix];
                        $znmcoa=$arrnmcoa[$ix];
                        if ($zidcoa==$pcoakode)
                            $pcbselect .="<option value='$zidcoa' selected>$zidcoa $znmcoa</option>";
                        else
                            $pcbselect .="<option value='$zidcoa'>$zidcoa $znmcoa</option>";
                    }

                }
                
                $pcbselect .="</select>";
                
                $psimpan="<input type='button' id='btnsave[]' name='btnsave[]' value='Save' "
                        . " onclick=\"SimpanData('$pidurut', '$pidrutin', '$pnoid', 'cb_coa[$pidurut]')\">";
                
                if ($pidgrouppil=="28" OR $pidgrouppil=="23" OR $pidgrouppil=="40" OR $pidgrouppil=="26" OR $pidgrouppil=="1" OR $pidgrouppil=="24") {
                }else{
                    $pcbselect="";
                    $psimpan="";
                }



                echo "<tr>";
                echo "<td nowrap>$no</td>";
                if ($psortby=="periode") {
                    echo "<td nowrap>$pblnp</td>";
                    echo "<td nowrap class='str'>$pidkary</td>";
                    echo "<td nowrap>$pnmkary</td>";
                }else{
                    echo "<td nowrap class='str'>$pidkary</td>";                
                    echo "<td nowrap>$pnmkary</td>";                
                    echo "<td nowrap>$pblnp</td>";
                }
                echo "<td nowrap class='str'>$pidrutin</td>";
                echo "<td nowrap>$pnamaid</td>";
                echo "<td nowrap class='str'>$pcoakode</td>";
                echo "<td nowrap>$pcoanama</td>";
                echo "<td nowrap>$ptgl</td>";
                echo "<td nowrap>$pobatuntuk</td>";
                echo "<td nowrap align='right'>$prptotal</td>";
                echo "<td>$pket</td>";
                echo "<td>$pcbselect</td>";
                echo "<td nowrap>$psimpan &nbsp; &nbsp; &nbsp; </td>";
                echo "</tr>";

                $no++;
            }

            
            $ptotal=BuatFormatNum($ptotal, $ppilformat);
            
            echo "<tr>";
            echo "<td colspan='10' align='center'><b>TOTAL</b></td>";
            if ($ppilihrpt=="excel") {
                
            }else{
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
            }
            echo "<td nowrap align='right'><b>$ptotal</b></td>";
            echo "<td></td>";
            
            echo "<td></td>";
            echo "<td></td>";
            
            echo "</tr>";
            ?>
        </tbody>
    </table>
    

    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    
</div>    
    
    <?PHP if ($ppilihrpt!="excel") { ?>

        
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
		
        <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="vendors/jszip/dist/jszip.min.js"></script>
        <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
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
                z-index:1;
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
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
        
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
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
    
    
    <script>
        function SimpanData(unourut, unoid, unoiddet, ucoa) {
        
            var icoa = document.getElementById(ucoa).value;

            if (unourut=="") {
                alert("ID kosong..."); return; false;
            }

            if (icoa=="") {
                alert("COA kosong..."); return; false;
            }
        
            var r=confirm("Apakah akan menyimpan data...???")
            if (r==true) {
                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/laporan/mod_lap_rincianbrrutin/simpandataeditbrrutin.php?module="+module+"&act=input&idmenu="+idmenu,
                    data:"unourut="+unourut+"&uidbrrutin="+unoid+"&uinoid="+unoiddet+"&ucoa="+icoa,
                    success:function(data){
                        alert(data);
                    }
                });

                return 1;
            }
            
        
        }
        
    </script>

</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");

    mysqli_close($cnmy);
?>