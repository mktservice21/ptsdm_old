<?PHP
date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if ($pmodule=="viewdatajenis") {
    
    include "../../../config/koneksimysqli.php";
    include("../../../config/fungsi_sql.php");
    include("../../../config/common.php");

    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    $pmobile=$_SESSION['MOBILE'];
    
    $psemuadep=false;
    $pbolehpilihdep=false;
    $ppilihlini_produk="";
    $query = "select * from dbtest.maping_karyawan_dep WHERE karyawanid='$fkaryawan' AND iddep='ALL'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $psemuadep=true;
        $pbolehpilihdep=true;
    }
    
    $pilihregion="";
    if ($fjbtid=="05") {
        $query = "select region FROM dbmaster.t_karyawan_posisi WHERE karyawanid='$fkaryawan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        $row= mysqli_fetch_array($tampil);
        $pilihregion=$row['region'];
    }
    
    
    $ppilformat=1;
    $ppilihrpt="";

    if ($ppilihrpt=="excel") {
        $ppilformat=3;
    }
    
    
    
    $pcoa_pilih=$_POST['ucoa'];
    
    $query = "select NAMA4 FROM dbmaster.coa_level4 WHERE COA4='$pcoa_pilih'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pnamacoa=$row['NAMA4'];
    
    
    $ptahun = $_POST['utahun'];
    $piddep = $_POST['udep'];
    $pidpengajuan = $_POST['upengajuan'];
    $pregion = $_POST['uregion'];
    $pidkrysm = $_POST['ukrysm'];
    $pliniproduk = $_POST['ulproduk'];
    $pncabdiviid = $_POST['ucabangiddivid'];
    $pncabid = $_POST['ucabangid'];
    $pndivcb = $_POST['udivcb'];
    $pnallcoa = $_POST['uall_coa'];
    $pnpilsls = $_POST['upilsls'];
    $pnpilslsgsm = $_POST['upilslsgsm'];
    $pnpilslssm = $_POST['upilslssm'];
    $pnpilmkt = $_POST['upilmkt'];
    
    $pnamadep = $_POST['unmdep'];
    $filternamacabang = $_POST['unmcabang'];

    
    $ppilihsales=false;
    $ppilihsales_gsm=false;
    $ppilihsales_sm=false;
    $ppilihmarketing=false;

    if ($piddep=="SLS" OR $piddep=="SLS01") {
        $ppilihsales=true;
    }

    if ($piddep=="SLS02") {
        $ppilihsales_gsm=true;
    }

    if ($piddep=="SLS03") {
        $ppilihsales_sm=true;
    }

    if ($piddep=="MKT") {
        $ppilihmarketing=true;
    }
    
    $pcabangdivisi="";
    $filtercabang="";
    $filterdivisi="";
    $filter_coa="";
    
    if (!empty($pncabdiviid)) {
        $pcabangdivisi_ = explode(",", $pncabdiviid);
        foreach ($pcabangdivisi_ as $idcabdiv) {
            $pcabangdivisi .="'".$idcabdiv."',";
        }
        if (!empty($pcabangdivisi)) $pcabangdivisi="(".substr($pcabangdivisi, 0, -1).")";
    }
    
    if (!empty($pncabid)) {
        $pncabid_ = explode(",", $pncabid);
        foreach ($pncabid_ as $idcabdiv) {
            $filtercabang .="'".$idcabdiv."',";
        }
        if (!empty($filtercabang)) $filtercabang="(".substr($filtercabang, 0, -1).")";
    }
    
    if (!empty($pndivcb)) {
        $pndivcb_ = explode(",", $pndivcb);
        foreach ($pndivcb_ as $niddiv) {
            $filterdivisi .="'".$niddiv."',";
        }
        if (!empty($filterdivisi)) $filterdivisi="(".substr($filterdivisi, 0, -1).")";
    }
    
    if (!empty($pnallcoa)) {
        $pnallcoa_ = explode(",", $pnallcoa);
        foreach ($pnallcoa_ as $nidcoa) {
            $filter_coa .="'".$nidcoa."',";
        }
        if (!empty($filter_coa)) $filter_coa="(".substr($filter_coa, 0, -1).")";
    }
    
    
    $pkaryawanid_user=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpprosbgtexpjns01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpprosbgtexpjns02_".$puserid."_$now ";


    $query = "SELECT * FROM dbtest.proses_budget_expenses WHERE "
            . " tahun='$ptahun' AND coa4='$pcoa_pilih' ";
    if (!empty($piddep)) $query .=" AND iddep='$piddep' ";
    else{
        
        if ($psemuadep==true) {

        }else{
            $query .=" AND iddep IN (select DISTINCT IFNULL(iddep,'') from dbtest.maping_karyawan_dep WHERE karyawanid='$pkaryawanid_user') ";
            if ($fjbtid=="36") {
                $query .=" AND divisi_pengajuan IN ('OTC', 'OT', 'CHC') ";
            }elseif ($fjbtid=="20") {
                $query .=" AND divisi_pengajuan IN ('ETH') ";
            }elseif ($fjbtid=="05") {
                if ($pkaryawanid_user=="0000000158") $query .=" AND region='B' ";
                elseif ($pkaryawanid_user=="0000000159") $query .=" AND region='T' ";
            }
        }
        
    }
    
    if (!empty($filter_coa)) $query .=" AND coa4 IN $filter_coa ";

    if ($ppilihsales == true) {

        if (empty($pidpengajuan)) {
            if (!empty($filterdivisi)) $query .=" AND divisi_pengajuan IN $filterdivisi ";
        }else{
            $query .=" AND divisi_pengajuan='$pidpengajuan' ";
        }
        //if (!empty($filtercabang)) $query .=" AND icabangid IN $filtercabang ";
        if (!empty($pcabangdivisi)) $query .=" AND CONCAT(icabangid, '|', divisi_pengajuan) IN $pcabangdivisi ";
    }elseif ($ppilihsales_gsm==true) {
        if (!empty($pregion)) {
            $query .=" AND region='$pregion' ";
        }
    }elseif ($ppilihmarketing == true) {
        /*
        if (!empty($pidpengajuan)) {
            $query .=" AND divisi_pengajuan='$pidpengajuan' ";
        }

        if ($pidpengajuan=="ETH") {
            //if (!empty($filtercabang)) $query .=" AND icabangid IN $filtercabang ";
        }elseif ($pidpengajuan=="OTC" OR $pidpengajuan=="OT" OR $pidpengajuan=="CHC") {
            if (!empty($filtercabang)) $query .=" AND icabangid IN $filtercabang ";
        }
        */
        
        if ($pliniproduk=="EAGLE") $query .=" AND karyawanid='0000000257' ";
        elseif ($pliniproduk=="PEACO") $query .=" AND karyawanid='0000000910' ";
        elseif ($pliniproduk=="PIGEO") $query .=" AND karyawanid='0000000157' ";
        elseif ($pliniproduk=="OT" OR $pliniproduk=="OTC" OR $pliniproduk=="CHC") $query .=" AND karyawanid='0000001556' ";
                
    }elseif ($ppilihsales_sm == true) {
        if (!empty($pidkrysm)) {
            $query .=" AND karyawanid='$pidkrysm' ";
        }else{
            if ($fjbtid=="05" AND !empty($pilihregion)) {
                $query .= " AND karyawanid IN (select distinct IFNULL(karyawanid,'') from mkt.ism0 as a "
                        . " JOIN mkt.icabang as b on a.icabangid=b.iCabangId WHERE region='$pilihregion') ";
            }
        }
    }
    
    
    //echo "$query<br/>";
    
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); 
    if (!empty($erropesan)) {
        echo $erropesan;
        mysqli_close($cnmy);
        exit;
    }

    $query = "UPDATE $tmp01 SET kodeid='NONE' WHERE IFNULL(kodeid,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); 
    if (!empty($erropesan)) {
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
        echo $erropesan;
        mysqli_close($cnmy);
        exit;
    }

    $query = "UPDATE $tmp01 SET nama_kode='NONE' WHERE IFNULL(nama_kode,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); 
    if (!empty($erropesan)) {
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
        echo $erropesan;
        mysqli_close($cnmy);
        exit;
    }
    
    
    
    $query = "SELECT DISTINCT kodeid, nama_kode FROM $tmp01";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); 
    if (!empty($erropesan)) {
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
        echo $erropesan;
        mysqli_close($cnmy);
        exit;
    }
    
    
    $nadd_filed="";

    for ($ix=1;$ix<=12;$ix++) {
        $nadd_filed .=" ADD COLUMN b".$ix." DECIMAL(20,2), ADD COLUMN e".$ix." DECIMAL(20,2), ";
    }
    $nadd_filed .=" ADD COLUMN b_total DECIMAL(20,2), ADD COLUMN e_total DECIMAL(20,2)";
    $query = "ALTER table $tmp02 $nadd_filed ";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan)) {
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
        echo $erropesan;
        mysqli_close($cnmy);
        exit;
    }

    for ($ix=1;$ix<=12;$ix++) {
        $b_field="b".$ix;
        $e_field="e".$ix;

        $n_bln = str_pad($ix, 2, '0', STR_PAD_LEFT);

        $nbulan=$ptahun."-".$n_bln;

        $query = "UPDATE $tmp02 as a JOIN (SELECT kodeid, nama_kode, LEFT(bulan,7) as bulan, SUM(jumlah) as jumlah FROM $tmp01 WHERE LEFT(bulan,7)='$nbulan' AND tipe='BUDGET' GROUP BY 1,2,3) as b "
                . " on a.kodeid=b.kodeid AND a.nama_kode=b.nama_kode SET a.".$b_field."=b.jumlah";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy);
        if (!empty($erropesan)) {
            mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
            mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
            echo $erropesan;
            mysqli_close($cnmy);
            exit;
        }

        $query = "UPDATE $tmp02 as a JOIN (SELECT kodeid, nama_kode, LEFT(bulan,7) as bulan, SUM(jumlah) as jumlah FROM $tmp01 WHERE LEFT(bulan,7)='$nbulan' AND tipe='EXPENSES' GROUP BY 1,2,3) as b "
                . " on a.kodeid=b.kodeid AND a.nama_kode=b.nama_kode SET ".$e_field."=b.jumlah";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy);
        if (!empty($erropesan)) {
            mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
            mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
            echo $erropesan;
            mysqli_close($cnmy);
            exit;
        }


    }
    
    
    
    if (!empty($pnamadep)) {
        echo "<small><b>Departemen : $pnamadep</b></small><br/>";
    }else{
        echo "<small><b>Departemen : All</b><small><br/>";
    }

    if ($ppilihsales == true OR $ppilihmarketing == true) {

        if (!empty($filternamacabang)) {
            echo "<small>$filternamacabang</small><br/>";
        }
        
    }
    
    echo "<br/><b>COA : $pcoa_pilih - $pnamacoa</b><br/>";
    
    echo "<hr/><br/>";
    
    echo "<div id='div-konten2'>";
    
        echo "<table id='tbltable_jenis' border='1' cellspacing='0' cellpadding='1'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th align='center' rowspan='2'><small>No</small></th>";
                    echo "<th align='center' rowspan='2'><small>&nbsp;</small></th>";
                    echo "<th align='center' rowspan='2'><small>Jenis</small></th>";

                    echo "<th align='center' colspan='3'><small>Total</small></th>";

                    for ($ix=1;$ix<=12;$ix++) {
                        $pnamabulan_="";
                        if ((INT)$ix==1) $pnamabulan_="Januari";
                        elseif ((INT)$ix==2) $pnamabulan_="Februari";
                        elseif ((INT)$ix==3) $pnamabulan_="Maret";
                        elseif ((INT)$ix==4) $pnamabulan_="April";
                        elseif ((INT)$ix==5) $pnamabulan_="Mei";
                        elseif ((INT)$ix==6) $pnamabulan_="Juni";
                        elseif ((INT)$ix==7) $pnamabulan_="Juli";
                        elseif ((INT)$ix==8) $pnamabulan_="Agustus";
                        elseif ((INT)$ix==9) $pnamabulan_="September";
                        elseif ((INT)$ix==10) $pnamabulan_="Oktober";
                        elseif ((INT)$ix==11) $pnamabulan_="November";
                        elseif ((INT)$ix==12) $pnamabulan_="Desember";
                        
                        echo "<th align='center' colspan='3'><small>$pnamabulan_</small></th>";
                    }
                    
                echo "</tr>";
                
                echo "<tr>";

                    echo "<th align='center'><small>Budget</small></th>";
                    echo "<th align='center'><small>Expenses</small></th>";
                    echo "<th align='center'><small>%</small></th>";
                    
                    for ($ix=1;$ix<=12;$ix++) {
                        echo "<th align='center' class='th2'><small>Budget</small></th>";
                        echo "<th align='center' class='th2'><small>Expenses</small></th>";
                        echo "<th align='center' class='th2'><small>%</small></th>";
                    }

                echo "</tr>";
                
            echo "</thead>";
            

            echo "<tbody>";


                for ($ix=1;$ix<=12;$ix++) {
                    $nb_subtotal[$ix]=0;
                    $ne_subtotal[$ix]=0;

                    $nb_grndtotal[$ix]=0;
                    $ne_grndtotal[$ix]=0;
                }


                $no=1;
                $query = "select * from $tmp02 ORDER BY nama_kode, kodeid";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $nkodeid=$row['kodeid'];
                    $nnamakode=$row['nama_kode'];

                    $ptomboljenis="";
                    if ($ppilihrpt=="excel") {
                    }else{
                        //$ptomboljenis = "<button type='button' id='btn_jenis' name='btn_jenis' class='btn btn-dark btn-xs' onclick=\"LihatDataJenis('$nkodeid')\"><i class=\"fa fa-archive\"></i> Jenis</button>";
                    }


                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ptomboljenis</td>";
                    echo "<td nowrap>$nnamakode</td>";

                    $pb_totalsub=0;
                    $pe_totalsub=0;

                    for ($ix=1;$ix<=12;$ix++) {
                        $b_field="b".$ix;
                        $e_field="e".$ix;

                        $nbudgetrp=$row[$b_field];
                        $nexpensesrp=$row[$e_field];

                        if (empty($nbudgetrp)) $nbudgetrp=0;
                        if (empty($nexpensesrp)) $nexpensesrp=0;


                        $nb_subtotal[$ix]=(DOUBLE)$nb_subtotal[$ix]+(DOUBLE)$nbudgetrp;
                        $ne_subtotal[$ix]=(DOUBLE)$ne_subtotal[$ix]+(DOUBLE)$nexpensesrp;

                        $nb_grndtotal[$ix]=(DOUBLE)$nb_grndtotal[$ix]+(DOUBLE)$nbudgetrp;
                        $ne_grndtotal[$ix]=(DOUBLE)$ne_grndtotal[$ix]+(DOUBLE)$nexpensesrp;

                        $pb_totalsub=(DOUBLE)$pb_totalsub+(DOUBLE)$nbudgetrp;
                        $pe_totalsub=(DOUBLE)$pe_totalsub+(DOUBLE)$nexpensesrp;

                    }

                    if (empty($pb_totalsub)) $pb_totalsub=0;
                    if (empty($pe_totalsub)) $pe_totalsub=0;

                    $nach=0;
                    if ((DOUBLE)$pb_totalsub<>0) {
                        $nach=(DOUBLE)$pe_totalsub/(DOUBLE)$pb_totalsub*100;
                    }

                    if (empty($nach)) $nach=0;

                    $pb_totalsub=BuatFormatNumberRp($pb_totalsub, $ppilformat);//1 OR 2 OR 3
                    $pe_totalsub=BuatFormatNumberRp($pe_totalsub, $ppilformat);//1 OR 2 OR 3

                    $nach=ROUND($nach,2);

                    echo "<td nowrap align='right' style='font-weight:bold;'>$pb_totalsub</td>";
                    echo "<td nowrap align='right' style='font-weight:bold;'>$pe_totalsub</td>";
                    echo "<td nowrap align='right' style='font-weight:bold;'>$nach</td>";

                    
                    
                    for ($ix=1;$ix<=12;$ix++) {
                        
                        $n_blnthn_pl = $ptahun."-".str_pad($ix, 2, '0', STR_PAD_LEFT);
                        
                        $b_field="b".$ix;
                        $e_field="e".$ix;

                        $nbudgetrp=$row[$b_field];
                        $nexpensesrp=$row[$e_field];

                        if (empty($nbudgetrp)) $nbudgetrp=0;
                        if (empty($nexpensesrp)) $nexpensesrp=0;

                        $nach=0;
                        if ((DOUBLE)$nbudgetrp<>0) {
                            $nach=(DOUBLE)$nexpensesrp/(DOUBLE)$nbudgetrp*100;
                        }

                        if (empty($nach)) $nach=0;
                        
                        
                        $nbudgetrp=BuatFormatNumberRp($nbudgetrp, $ppilformat);//1 OR 2 OR 3
                        $nexpensesrp=BuatFormatNumberRp($nexpensesrp, $ppilformat);//1 OR 2 OR 3

                        $nach=ROUND($nach,2);
                        
                        $ptombol_jumlah_e=$nexpensesrp;
                        
                        if ($nexpensesrp<>"0") {
                            $ptombol_jumlah_e = "<button type='button' id='btn_jmle' name='btn_jmle' class='btn btn-info btn-xs' onclick=\"LihatDataJumlahBgt('$pcoa_pilih', '$nkodeid', '$nnamakode', '$n_blnthn_pl')\">$nexpensesrp</button>";
                        }
                        
                        echo "<td nowrap align='right'>$nbudgetrp</td>";
                        echo "<td nowrap align='right'>$ptombol_jumlah_e</td>";
                        echo "<td nowrap align='right' style='font-weight:bold;'>$nach</td>";

                    }
                    
                    echo "</tr>";

                    $no++;

                }


                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap></td>";
                echo "<td nowrap>Grand Total </td>";

                $pb_totalsub=0;
                $pe_totalsub=0;

                for ($ix=1;$ix<=12;$ix++) {
                    $nbudgetrp=$nb_subtotal[$ix];
                    $nexpensesrp=$ne_subtotal[$ix];

                    if (empty($nbudgetrp)) $nbudgetrp=0;
                    if (empty($nexpensesrp)) $nexpensesrp=0;

                    $pb_totalsub=(DOUBLE)$pb_totalsub+(DOUBLE)$nbudgetrp;
                    $pe_totalsub=(DOUBLE)$pe_totalsub+(DOUBLE)$nexpensesrp;


                }


                if (empty($pb_totalsub)) $pb_totalsub=0;
                if (empty($pe_totalsub)) $pe_totalsub=0;

                $nach=0;
                if ((DOUBLE)$pb_totalsub<>0) {
                    $nach=(DOUBLE)$pe_totalsub/(DOUBLE)$pb_totalsub*100;
                }

                if (empty($nach)) $nach=0;

                $pb_totalsub=BuatFormatNumberRp($pb_totalsub, $ppilformat);//1 OR 2 OR 3
                $pe_totalsub=BuatFormatNumberRp($pe_totalsub, $ppilformat);//1 OR 2 OR 3

                $nach=ROUND($nach,2);

                echo "<td nowrap align='right' style='font-weight:bold;'>$pb_totalsub</td>";
                echo "<td nowrap align='right' style='font-weight:bold;'>$pe_totalsub</td>";
                echo "<td nowrap align='right' style='font-weight:bold;'>$nach</td>";

                $pb_totalsub=0;
                $pe_totalsub=0;
                for ($ix=1;$ix<=12;$ix++) {
                    $nbudgetrp=$nb_subtotal[$ix];
                    $nexpensesrp=$ne_subtotal[$ix];

                    if (empty($nbudgetrp)) $nbudgetrp=0;
                    if (empty($nexpensesrp)) $nexpensesrp=0;

                    $nach=0;
                    if ((DOUBLE)$nbudgetrp<>0) {
                        $nach=(DOUBLE)$nexpensesrp/(DOUBLE)$nbudgetrp*100;
                    }

                    if (empty($nach)) $nach=0;

                    $nbudgetrp=BuatFormatNumberRp($nbudgetrp, $ppilformat);//1 OR 2 OR 3
                    $nexpensesrp=BuatFormatNumberRp($nexpensesrp, $ppilformat);//1 OR 2 OR 3

                    $nach=ROUND($nach,2);

                    echo "<td nowrap align='right'>$nbudgetrp</td>";
                    echo "<td nowrap align='right'>$nexpensesrp</td>";
                    echo "<td nowrap align='right'>$nach</td>";

                }
                
                
                echo "</tr>";



            echo "</tbody>";
            
            
        echo "</table>";
    
            
    echo "</div>";
    
    
    
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_close($cnmy);
    
    
    ?>

    <style>
        #btn_jmle {
            border: 1px solid #cccccc;
            border-radius: 3px;
            background-color: white;
        }
        #btn_jmle:hover {
            cursor:pointer;
            background-color: #cccccc;
        }
        #btn_jmle:focus {
            border: 1px solid #cc0000;
            background-color: #fff;
        }
    </style>
    
    <style>
        #tbltable_jenis {
            border-collapse: collapse;
        }
        #tbltable_jenis th {
            font-size : 16px;
            padding:5px;
            background-color: #ccccff;
        }
        #tbltable_jenis tr #tbltable_jenis td {
            font-size : 12px;
        }
        #tbltable_jenis tr td {
            padding : 3px;
        }
        #tbltable_jenis tr:hover {background-color:#f5f5f5;}
        #tbltable_jenis thead tr:hover {background-color:#cccccc;}
    </style>
    
    <script>
        
        function LihatDataJumlahBgt(icoa, ikodeid, ikodeidnm, ibln) {
            //alert(icoa+" "+ikodeid);
            
            $("#div-detail").html("");
            
            var idep = document.getElementById('cb_dept').value;
            var itahun = document.getElementById('e_tahun').value;
            var ipengajuan = document.getElementById('cb_pengajuan').value;//divisi
            var iregion = document.getElementById('cb_region').value;
            var ikrysm = document.getElementById('cb_karyawansm').value;
            var ilproduk = document.getElementById('cb_liniproduk').value;
            var icabangiddivid = document.getElementById('txt_cabangiddivisi').value;
            var icabangid = document.getElementById('txt_cabangid').value;
            var idivcb = document.getElementById('txt_divisicb').value;
            var iall_coa = document.getElementById('txt_coa').value;
            var ipilsls = document.getElementById('txt_pilsales').value;
            var ipilslsgsm = document.getElementById('txt_pilsalesgsm').value;
            var ipilslssm = document.getElementById('txt_pilsalessm').value;
            var ipilmkt = document.getElementById('txt_pilmkt').value;
            var inmdep = document.getElementById('cb_nmdept').value;
            var inmcabang = document.getElementById('txt_namacabang').value;
            
            
            $.ajax({
                type:"post",
                url:"module/laporan_gl/demo_mod_gl_expenvsbudget/aksi_viewdataexpbgt_exp.php?module=viewdatadetail",
                data:"ucoa="+icoa+"&ukodeid="+ikodeid+"&ukodeidnm="+ikodeidnm+"&ubln="+ibln+"&udep="+idep+"&utahun="+itahun+"&upengajuan="+ipengajuan+"&uregion="+iregion+"&ucabdivisi="+iall_coa+"&ukrysm="+ikrysm+
                        "&ucabangiddivid="+icabangiddivid+"&ucabangid="+icabangid+"&udivcb="+idivcb+"&uall_coa="+iall_coa+
                        "&upilsls="+ipilsls+"&upilslsgsm="+ipilslsgsm+"&upilslssm="+ipilslssm+"&upilmkt="+ipilmkt+
                        "&unmdep="+inmdep+"&unmcabang="+inmcabang+"&ulproduk="+ilproduk,
                success:function(data){
                    $("#div-detail").html(data);
                    
                    //window.scrollTo(0,document.body.scrollHeight);
                    //window.scrollTo(0,document.querySelector("#div-jenis").scrollHeight);
                }
            });
            
        }
        
    </script>
    <?PHP
    
}