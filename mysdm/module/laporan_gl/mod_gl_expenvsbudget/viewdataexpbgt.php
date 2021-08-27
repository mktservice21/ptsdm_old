<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if (!isset($_SESSION['IDCARD'])) {
    echo "Anda Harus Login Ulang";
    exit;
}

$fkaryawan=$_SESSION['IDCARD'];
$fstsadmin=$_SESSION['STSADMIN'];
$flvlposisi=$_SESSION['LVLPOSISI'];
$fdivisi=$_SESSION['DIVISI'];
$fgroupid=$_SESSION['GROUP'];
$fjbtid=$_SESSION['JABATANID'];
$pmobile=$_SESSION['MOBILE'];



if ($pmodule=="viewdataregion") {
    
    include "../../../config/koneksimysqli.php";
    $pilihregion="";
    if ($fjbtid=="05") {
        $query = "select region FROM dbmaster.t_karyawan_posisi WHERE karyawanid='$fkaryawan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        $row= mysqli_fetch_array($tampil);
        $pilihregion=$row['region'];
    }
    
    mysqli_close($cnmy);
    
    
    $ppengajuan=$_POST['upengajuan'];
    $pdept=$_POST['udep'];
    $pilproduk=$_POST['ulproduk'];
    
    
    $ppilihsales=false;
    $ppilihsales_gsm=false;
    $ppilihmarketing=false;
    
    if ($pdept=="SLS" OR $pdept=="SLS01") {
        $ppilihsales=true;
    }
    
    if ($pdept=="SLS02") {
        $ppilihsales_gsm=true;
    }
    
    if ($pdept=="MKT") {
        $ppilihmarketing=true;
    }
    
    
    if ($fjbtid=="05" AND !empty($pilihregion)) {
        
        if ($pilihregion=="B") echo "<option value='B' >Barat</option>";
        elseif ($pilihregion=="T") echo "<option value='T' >Timur</option>";
        
    }else{
    
        if ($ppilihsales_gsm==true) {

            echo "<option value='' selected>-- All --</option>";
            echo "<option value='B' >Barat</option>";
            echo "<option value='T' >Timur</option>";

        }else{
            if ($ppilihmarketing==true) {
                if ($pilproduk=="OTC" || $pilproduk=="OT" || $pilproduk=="CHC"){
                    echo "<option value='' selected>-- All --</option>";
                }else{
                    if (!empty($pilproduk)) {
                        echo "<option value='' selected>-- All --</option>";
                        echo "<option value='B' >Barat</option>";
                        echo "<option value='T' >Timur</option>";
                    }else{
                        echo "<option value='' selected>-- All Ethical & CHC --</option>";
                        echo "<option value='B_ETH' >Barat Ethical</option>";
                        echo "<option value='T_ETH' >Timur Ethical</option>";
                    }
                }
            }else{

                if ($ppengajuan=="ETH"){
                    echo "<option value='' selected>-- All --</option>";
                    echo "<option value='B' >Barat</option>";
                    echo "<option value='T' >Timur</option>";
                }elseif ($ppengajuan=="OTC" || $ppengajuan=="OT" || $ppengajuan=="CHC"){
                    echo "<option value='' selected>-- All --</option>";
                    if ($ppilihsales == true) {
                        echo "<option value='B' >Barat</option>";
                        echo "<option value='T' >Timur</option>";
                    }
                }else{
                    echo "<option value='' selected>-- All Ethical & CHC --</option>";
                    echo "<option value='B_ETH' >Barat Ethical</option>";
                    echo "<option value='T_ETH' >Timur Ethical</option>";
                    if ($ppilihsales == true) {
                        echo "<option value='B_OTC' >Barat CHC</option>";
                        echo "<option value='T_OTC' >Timur CHC</option>";
                    }
                }

            }

        }
        
    }
    
}elseif ($pmodule=="viewdatacabang") {
    
    $ppengajuan=$_POST['upengajuan'];
    $pregion=$_POST['uregion'];
    $pdept=$_POST['udep'];
    $pilproduk=$_POST['ulproduk'];
    
    $ppilihsales=false;
    $ppilihmarketing=false;
    
    if ($pdept=="SLS" OR $pdept=="SLS01") {
        $ppilihsales=true;
    }
    
    if ($pdept=="MKT") {
        $ppilihmarketing=true;
    }
    
    
    $ipengajuan=$ppengajuan;
    
    $query_cab="";
    $filter_region="";
    if (!empty($pregion)) {
        $nregion= substr($pregion, 0, 1);
        if (!empty($nregion)) $filter_region=" AND region='$nregion' ";
        
        if (empty($ppengajuan)) {
            $n_penajuan= substr($pregion, 2, 3);
            $ipengajuan=$n_penajuan;
        }
    }
    
    
    if ($ppilihmarketing==true) {
        if ($pilproduk=="OTC" || $pilproduk=="OT" || $pilproduk=="CHC"){
            $query_cab = "select icabangid, nama_cabang, divisi_pengajuan as iket, region FROM dbproses.proses_cabang WHERE divisi_pengajuan='OTC' AND IFNULL(sts,'')='PM' ";
            $query_cab .= " AND icabangid NOT IN ('JKT_MT', 'JKT_RETAIL', 'HO') ";
            $query_cab .= " ORDER BY nama_cabang";
        }else{
            if (!empty($pilproduk)) {
                $query_cab = "select icabangid, nama_cabang, divisi_pengajuan as iket, region FROM dbproses.proses_cabang WHERE divisi_pengajuan='ETH' $filter_region ";
                $query_cab .= " ORDER BY nama_cabang";
            }else{
                $query_cab = "select icabangid, nama_cabang, divisi_pengajuan as iket, region FROM dbproses.proses_cabang WHERE 1=1 $filter_region ";
                $query_cab .= " ORDER BY nama_cabang";
            }
        }
    }else{
    
        if ($ipengajuan=="ETH"){

            //$query_cab = "select iCabangId as icabangid, nama as nama_cabang, 'ETH' as iket, region from mkt.icabang WHERE IFNULL(aktif,'')<>'N' $filter_region ";
            //$query_cab .= " AND LEFT(nama,5) NOT IN ('PEA -', 'OTC -') ";
            //$query_cab .= " ORDER BY nama";

            $query_cab = "select icabangid, nama_cabang, divisi_pengajuan as iket, region FROM dbproses.proses_cabang WHERE divisi_pengajuan='ETH' $filter_region ";
            $query_cab .= " ORDER BY nama_cabang";

        }elseif ($ipengajuan=="OTC" || $ipengajuan=="OT" || $ipengajuan=="CHC"){
            if ($ppilihsales==true) {
                //$query_cab .= "select icabangid_o as icabangid, nama as nama_cabang, 'OTC' as iket, region from mkt.icabang_o WHERE IFNULL(aktif,'')<>'N' $filter_region ";
                $query_cab = "select icabangid, nama_cabang, divisi_pengajuan as iket, region FROM dbproses.proses_cabang WHERE divisi_pengajuan='OTC' AND IFNULL(sts,'')='SLS' $filter_region ";

            }else{
                //$query_cab .= "select icabangid_o as icabangid, nama as nama_cabang, 'OTC' as iket, region from dbmaster.v_icabang_o WHERE IFNULL(aktif,'')<>'N' $filter_region ";
                //$query_cab .= "select cabangid_ho as icabangid, nama as nama_cabang, 'OTC' as iket, region from dbmaster.cabang_otc WHERE IFNULL(aktif,'')<>'N' $filter_region ";
                //$query_cab .= " AND cabangid_ho NOT IN ('JKT_MT', 'JKT_RETAIL', 'HO') ";

                $query_cab = "select icabangid, nama_cabang, divisi_pengajuan as iket, region FROM dbproses.proses_cabang WHERE divisi_pengajuan='OTC' AND IFNULL(sts,'')='PM' ";
                $query_cab .= " AND icabangid NOT IN ('JKT_MT', 'JKT_RETAIL', 'HO') ";

            }
            //$query_cab .= " ORDER BY nama";
            $query_cab .= " ORDER BY nama_cabang";

        }else{
            /*
            $query_cab = "select iCabangId as icabangid, nama as nama_cabang, 'ETH' as iket, region from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
            $query_cab .= " AND LEFT(nama,5) NOT IN ('PEA -', 'OTC -') ";
            $query_cab .= " UNION ";
            if ($ppilihsales==true) {
                $query_cab .= "select icabangid_o as icabangid, nama as nama_cabang, 'OTC' as iket, region from mkt.icabang_o WHERE IFNULL(aktif,'')<>'N' ";
            }else{
                $query_cab .= "select cabangid_ho as icabangid, nama as nama_cabang, 'OTC' as iket, region from dbmaster.cabang_otc WHERE IFNULL(aktif,'')<>'N' ";
                $query_cab .= " AND cabangid_ho NOT IN ('JKT_MT', 'JKT_RETAIL', 'HO') ";
            }
            $query_cab = "SELECT * FROM ($query_cab) as ntabel WHERE 1=1 $filter_region ";
            $query_cab .= " ORDER BY nama_cabang";
            */

            $query_cab = "select icabangid, nama_cabang, divisi_pengajuan as iket, region FROM dbproses.proses_cabang WHERE 1=1 $filter_region ";
            $query_cab .= " ORDER BY nama_cabang";


        }
        
    }
    
    include "../../../config/koneksimysqli.php";
    
    if (!empty($query_cab)) {
        
        $tampil = mysqli_query($cnmy, $query_cab);
        while ($row= mysqli_fetch_array($tampil)) {
            $ncabid=$row['icabangid'];
            $ncabnm=$row['nama_cabang'];
            $niket=$row['iket'];

            $nnmket=$niket;
            if ($niket=="OTC") $nnmket="CHC";
            
            $pn_namacabang="$ncabnm - $nnmket";
            if (!empty($ipengajuan)) $pn_namacabang=$ncabnm;
            
            $pnid_kode=$ncabid."|".$niket;
            
            echo "&nbsp; <input type='checkbox' onClick=\"ShowCoaDariBudget()\" value='$pnid_kode' name='chkbox_cab[]' checked> $pn_namacabang<br/>";

        }
        
    }else{
        echo "";
    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdatacoadep") {
    include "../../../config/koneksimysqli.php";
    
    $pilihregion="";
    if ($fjbtid=="05") {
        $query = "select region FROM dbmaster.t_karyawan_posisi WHERE karyawanid='$fkaryawan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        $row= mysqli_fetch_array($tampil);
        $pilihregion=$row['region'];
    }
    
    
    $ppengajuan=$_POST['upengajuan'];//divisi
    $pdept=$_POST['udep'];
    $ptahun=$_POST['utahun'];
    $pregion=$_POST['uregion'];
    $pidkrysm=$_POST['ukrysm'];
    $pilproduk=$_POST['ulproduk'];
    
    $ppilihsales=false;
    $ppilihsales_gsm=false;
    $ppilihsales_sm=false;
    $ppilihmarketing=false;
    
    if ($pdept=="SLS" OR $pdept=="SLS01") {
        $ppilihsales=true;
    }
    
    if ($pdept=="SLS02") {
        $ppilihsales_gsm=true;
    }
    
    if ($pdept=="SLS03") {
        $ppilihsales_sm=true;
    }
    
    if ($pdept=="MKT") {
        $ppilihmarketing=true;
    }
    
    $pcabangdivisi="";
    $filtercabang="";
    $filterdivisi="";
    
    if ($ppilihsales == true OR $ppilihmarketing == true) {
        
        if (isset($_POST['ucabdivisi'])) $pcabangdivisi=$_POST['ucabdivisi'];
        if (!empty($pcabangdivisi)) $pcabangdivisi=substr($pcabangdivisi, 0, -1);

        if (!empty($pcabangdivisi)) {
            $pcabangdivisi_ = explode(",", $pcabangdivisi);

            foreach ($pcabangdivisi_ as $idcabdiv) {
                if (!empty($idcabdiv)) {
                    $idcabdiv_ = explode("|", $idcabdiv);
                    $pidcabang=$idcabdiv_[0];
                    $piddivisi=$idcabdiv_[1];

                    if (strpos($filtercabang, $pidcabang)==false) $filtercabang .="'".$pidcabang."',";
                    if (strpos($filterdivisi, $piddivisi)==false) $filterdivisi .="'".$piddivisi."',";

                    //echo "$pidcabang ... $piddivisi<br/>";
                    //echo "$filterdivisi<br/>";
                }
            }
        }


        if (!empty($filtercabang)) $filtercabang="(".substr($filtercabang, 0, -1).")";
        if (!empty($filterdivisi)) $filterdivisi="(".substr($filterdivisi, 0, -1).")";

    
    }
    
    //echo "$filtercabang <br/> $filterdivisi<br/>"; exit;
    
    $query_coa="";
    $query_coa01="";
    $query_coa02="";
    
    $query_coa02 = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
        join dbmaster.coa_level3 as b on a.COA3=b.COA3 
        join dbmaster.coa_level2 as c on b.COA2=c.COA2 WHERE 1=1 ";

    $query_coa02 .= " ORDER BY a.COA4";
    
    if (!empty($pdept)) {
        $query_coa01 = "select DISTINCT a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
            join dbmaster.coa_level3 as b on a.COA3=b.COA3 
            join dbmaster.coa_level2 as c on b.COA2=c.COA2 
            JOIN dbmaster.t_budget_divisi as d on a.COA4=d.coa4 ";

        $query_coa01 .=" WHERE 1=1  ";
        
        if ($ppilihsales_gsm == true AND !empty($pregion)) {
            if ($pregion=="B") $query_coa01 .=" AND karyawanid='0000000158' ";
            if ($pregion=="T") $query_coa01 .=" AND karyawanid='0000000159' ";
        }
        
        if ($ppilihsales_sm == true) {
            if (!empty($pidkrysm)) {
                $query_coa01 .=" AND karyawanid='$pidkrysm' ";
            }else{
                
                if ($fjbtid=="05" AND !empty($pilihregion)) {
                    $query_coa01 .= " AND karyawanid IN (select distinct IFNULL(karyawanid,'') from mkt.ism0 as a "
                            . " JOIN mkt.icabang as b on a.icabangid=b.iCabangId WHERE region='$pilihregion') ";
                }
                
            }
        }
        
        $query_coa01 .=" AND YEAR(d.bulan)='$ptahun' AND d.iddep='$pdept' ";
        
        if ($ppilihsales==true) {
            if ($ppengajuan=="OT" OR $ppengajuan=="OTC" OR $ppengajuan=="CHC") {
                if (!empty($filtercabang)) $query_coa01 .=" AND d.icabangid_o IN $filtercabang";
                if (!empty($filterdivisi)) $query_coa01 .=" AND d.divisi_pengajuan IN $filterdivisi";
            }else{
                //if (!empty($filtercabang)) $query_coa01 .=" AND d.icabangid IN $filtercabang";
                if (!empty($filterdivisi)) $query_coa01 .=" AND d.divisi_pengajuan IN $filterdivisi";
            }
        }elseif($ppilihmarketing==true) {
            if ($pilproduk=="OT" OR $pilproduk=="OTC" OR $pilproduk=="CHC") {
                $query_coa01 .=" AND (d.karyawanid='0000001556' OR (d.divisi_pengajuan='OTC' AND d.iddep='MKT') ) ";
            }else{
                //if ($pilproduk=="EAGLE") $query_coa01 .=" AND d.karyawanid='0000000257' ";
                //elseif ($pilproduk=="PEACO") $query_coa01 .=" AND d.karyawanid='0000000910' ";
                //elseif ($pilproduk=="PIGEO") $query_coa01 .=" AND d.karyawanid='0000000157' ";
                
                if ($pilproduk=="EAGLE") $query_coa01 .=" AND (d.karyawanid='0000000257' OR (d.divisi_pengajuan='ETH' AND d.iddep='MKT') ) ";
                elseif ($pilproduk=="PEACO") $query_coa01 .=" AND (d.karyawanid='0000000910' OR (d.divisi_pengajuan='ETH' AND d.iddep='MKT') ) ";
                elseif ($pilproduk=="PIGEO") $query_coa01 .=" AND (d.karyawanid='0000000157' OR (d.divisi_pengajuan='ETH' AND d.iddep='MKT') ) ";
            }
        }
        
        $query_coa01 .= " ORDER BY a.COA4";
        
        $query_coa=$query_coa01;
        
    }else{
        
        if ($fjbtid=="36" OR $fjbtid=="20") {
            
            $query_coa01 = "select DISTINCT a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                join dbmaster.coa_level3 as b on a.COA3=b.COA3 
                join dbmaster.coa_level2 as c on b.COA2=c.COA2 
                JOIN dbmaster.t_budget_divisi as d on a.COA4=d.coa4 ";

            $query_coa01 .=" WHERE 1=1 AND d.karyawanid='$fkaryawan' ";
            
            $query_coa01 .= " ORDER BY a.COA4";
            
            $query_coa=$query_coa01;
            
        }else{
            $query_coa=$query_coa02;
        }
        
    }
    
    if (!empty($query_coa)) {
        
        $tampil = mysqli_query($cnmy, $query_coa);
        $ketemu = mysqli_num_rows($tampil);
        
        if ((INT)$ketemu<=0) {
            //$query_coa=$query_coa02;
            //$tampil = mysqli_query($cnmy, $query_coa);
        }
        
        while ($z= mysqli_fetch_array($tampil)) {
            $pcoa4=$z['COA4'];
            $pnmcoa4=$z['NAMA4'];
            echo "&nbsp; <input type='checkbox' value='$pcoa4' name='chkbox_coa[]' checked> $pcoa4 - $pnmcoa4<br/>";
        }
        
    }else{
        echo "";
    }
    
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatacoadepproses") {
    include "../../../config/koneksimysqli.php";
    
    
    $psemuadep=false;
    $pbolehpilihdep=false;
    $ppilihlini_produk="";
    $query = "select * from dbproses.maping_karyawan_dep WHERE karyawanid='$fkaryawan' AND iddep='ALL'";
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
    
    
    
    $ppengajuan=$_POST['upengajuan'];//divisi
    $pdept=$_POST['udep'];
    $ptahun=$_POST['utahun'];
    $pregion=$_POST['uregion'];
    $pidkrysm=$_POST['ukrysm'];
    $pilproduk=$_POST['ulproduk'];
    $pjabatansm="";
    
    $ppilihsales=false;
    $ppilihsales_gsm=false;
    $ppilihsales_sm=false;
    $ppilihmarketing=false;
    
    if ($pdept=="SLS" OR $pdept=="SLS01") {
        $ppilihsales=true;
    }
    
    if ($pdept=="SLS02") {
        $ppilihsales_gsm=true;
    }
    
    if ($pdept=="SLS03") {
        $ppilihsales_sm=true;
        if (!empty($pidkrysm)) {
            $query = "select jabatanId as jabatanid FROM hrd.karyawan WHERE karyawanId='$pidkrysm'";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            $row= mysqli_fetch_array($tampil);
            $pjabatansm=$row['jabatanid'];
        }
        
    }
    
    if ($pdept=="MKT") {
        $ppilihmarketing=true;
    }
    
    
    
    
    $query_coa01="SELECT distinct COA4, NAMA4, DESKRIPSI4 FROM dbmaster.coa_level4 "
            . " WHERE 1=1 ";
    $query_coa01 .=" ORDER BY COA4, NAMA4";
        
        
    $query_coa="";
    if (empty($pdept)) {
        
        $query_coa02="SELECT distinct COA4, NAMA4, DESKRIPSI4 FROM dbproses.proses_coa "
                . " WHERE 1=1 AND tahun='$ptahun' ";
        if ($psemuadep == true) {
        }else{
            $query_coa02 .=" AND divisi_pengajuan IN (select IFNULL(divisi_pengajuan,'') FROM dbproses.maping_karyawan_dep WHERE karyawanid='$fkaryawan') ";
        }
        
        
        $query_coa02 .=" ORDER BY COA4, NAMA4";
        
        $query_coa=$query_coa02;
        
    }else{
        
        $query_coa02="SELECT distinct COA4, NAMA4, DESKRIPSI4 FROM dbproses.proses_coa "
                . " WHERE 1=1 AND tahun='$ptahun' ";
        $query_coa02 .=" AND iddep='$pdept' ";
        if ($ppilihsales == true) {
            if (!empty($ppengajuan)) {
                $query_coa02 .=" AND divisi_pengajuan='$ppengajuan' ";
            }else{
                if ($pregion=="B_ETH" OR $pregion=="T_ETH") {
                    $query_coa02 .=" AND divisi_pengajuan='ETH' ";
                }elseif ($pregion=="B_OTC" OR $pregion=="T_OTC") {
                    $query_coa02 .=" AND divisi_pengajuan='OTC' ";
                }else{
                    if ($fjbtid=="36") {
                        $query_coa02 .=" AND divisi_pengajuan='OTC' ";
                    }elseif ($fjbtid=="20" OR $fjbtid=="05") {
                        $query_coa02 .=" AND divisi_pengajuan='ETH' ";
                    }
                }
            }
        }elseif ($ppilihsales_sm == true) {
            if (!empty($pidkrysm)) {
                if ($pjabatansm=="36") {
                    $query_coa02 .=" AND divisi_pengajuan='OTC' ";
                }elseif ($pjabatansm=="20") {
                    $query_coa02 .=" AND divisi_pengajuan='ETH' ";
                }else{
                    $query_coa02 .=" AND divisi_pengajuan IN (select IFNULL(divisi_pengajuan,'') FROM dbproses.maping_karyawan_dep WHERE karyawanid='$pidkrysm') ";
                }
            }else{
                if ($fjbtid=="05" AND !empty($pilihregion)) {
                    $query_coa02 .=" AND divisi_pengajuan='ETH' ";
                }
            }
        }elseif ($ppilihmarketing==true) {
            if (!empty($pilproduk)) {
                if ($pilproduk=="OTC") $query_coa02 .=" AND divisi_pengajuan='$pilproduk' ";
                else $query_coa02 .=" AND divisi_pengajuan='ETH' ";
            }else{
                if ($pregion=="B_ETH" OR $pregion=="T_ETH") {
                    $query_coa02 .=" AND divisi_pengajuan='ETH' ";
                }elseif ($pregion=="B_OTC" OR $pregion=="T_OTC") {
                    $query_coa02 .=" AND divisi_pengajuan='OTC' ";
                }
            }
        }
        
        
        $query_coa02 .=" ORDER BY COA4, NAMA4";
        
        $query_coa=$query_coa02;
    }
    
    
    if (!empty($query_coa)) {
        
        $tampil = mysqli_query($cnmy, $query_coa);
        $ketemu = mysqli_num_rows($tampil);
        
        if ((INT)$ketemu<=0) {
            //$query_coa=$query_coa02;
            //$tampil = mysqli_query($cnmy, $query_coa);
        }
        
        while ($z= mysqli_fetch_array($tampil)) {
            $pcoa4=$z['COA4'];
            $pnmcoa4=$z['NAMA4'];
            echo "&nbsp; <input type='checkbox' value='$pcoa4' name='chkbox_coa[]' checked> $pcoa4 - $pnmcoa4<br/>";
        }
        
    }
    
    
    
    mysqli_close($cnmy);
}

?>