<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if ($pmodule=="viewdataregion") {
    
    $ppengajuan=$_POST['upengajuan'];
    if ($ppengajuan=="ETH"){
        echo "<option value='' selected>-- All --</option>";
        echo "<option value='B' >Barat</option>";
        echo "<option value='T' >Timur</option>";
    }elseif ($ppengajuan=="OTC" || $ppengajuan=="OT" || $ppengajuan=="CHC"){
        echo "<option value='' selected>-- All --</option>";
        echo "<option value='B' >Barat</option>";
        echo "<option value='T' >Timur</option>";
    }else{
        echo "<option value='' selected>-- All Ethical & CHC --</option>";
        echo "<option value='B_ETH' >Barat Ethical</option>";
        echo "<option value='T_ETH' >Timur Ethical</option>";
        echo "<option value='B_OTC' >Barat CHC</option>";
        echo "<option value='T_OTC' >Timur CHC</option>";
    }
    
}elseif ($pmodule=="viewdatacabang") {
    
    $ppengajuan=$_POST['upengajuan'];
    $pregion=$_POST['uregion'];
    
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
    
    
    
    
    if ($ipengajuan=="ETH"){
        
        $query_cab = "select iCabangId as icabangid, nama as nama_cabang, 'ETHICAL' as iket, region from mkt.icabang WHERE IFNULL(aktif,'')<>'N' $filter_region ";
        $query_cab .= " AND LEFT(nama,5) NOT IN ('PEA -', 'OTC -') ";
        $query_cab .= " ORDER BY nama";
        
    }elseif ($ipengajuan=="OTC" || $ipengajuan=="OT" || $ipengajuan=="CHC"){
        
        $query_cab .= "select icabangid_o as icabangid, nama as nama_cabang, 'OTC' as iket, region from dbmaster.v_icabang_o WHERE IFNULL(aktif,'')<>'N' $filter_region ";
        $query_cab .= " ORDER BY nama";
        
    }else{
        
        $query_cab = "select iCabangId as icabangid, nama as nama_cabang, 'ETHICAL' as iket, region from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
        $query_cab .= " AND LEFT(nama,5) NOT IN ('PEA -', 'OTC -') ";
        $query_cab .= " UNION ";
        $query_cab .= "select icabangid_o as icabangid, nama as nama_cabang, 'OTC' as iket, region from dbmaster.v_icabang_o WHERE IFNULL(aktif,'')<>'N' ";
        $query_cab = "SELECT * FROM ($query_cab) as ntabel WHERE 1=1 $filter_region ";
        $query_cab .= " ORDER BY nama_cabang";
        
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
            
            $pnid_kode=$ncabid."_".$niket;
            
            echo "&nbsp; <input type=checkbox value='$pnid_kode' name='chkbox_cab[]' checked> $pn_namacabang<br/>";

        }
        
    }else{
        echo "";
    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdatacoadep") {
    include "../../../config/koneksimysqli.php";
    
    $pdept=$_POST['udep'];
    $ptahun=$_POST['utahun'];
    
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
            JOIN dbmaster.t_budget_divisi as d on a.COA4=d.coa4 WHERE 1=1 ";

        $query_coa01 .=" AND YEAR(d.bulan)='$ptahun' AND d.departemen='$pdept' ";

        $query_coa01 .= " ORDER BY a.COA4";
        
        $query_coa=$query_coa01;
        
    }else{
        $query_coa=$query_coa02;
    }
    
    if (!empty($query_coa)) {
        
        $tampil = mysqli_query($cnmy, $query_coa);
        $ketemu = mysqli_num_rows($tampil);
        
        if ((INT)$ketemu<=0) {
            $query_coa=$query_coa02;
            $tampil = mysqli_query($cnmy, $query_coa);
        }
        
        while ($z= mysqli_fetch_array($tampil)) {
            $pcoa4=$z['COA4'];
            $pnmcoa4=$z['NAMA4'];
            echo "&nbsp; <input type=checkbox value='$pcoa4' name='chkbox_coa[]' checked> $pcoa4 - $pnmcoa4<br/>";
        }
        
    }else{
        echo "";
    }
    
    mysqli_close($cnmy);
}elseif ($pmodule=="xxxx") {
    
}

?>