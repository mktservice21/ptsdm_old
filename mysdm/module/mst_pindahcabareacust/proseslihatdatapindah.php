<?PHP
    session_start();
    
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    
    $pidcabang=$_POST['uicab'];
    $pidarea=$_POST['uiarea'];
    
    $piddaricabang=$_POST['uicabdari'];
    $piddariarea=$_POST['uiareadari'];
    
    // echo "$pidcabang - $pidarea dan $piddaricabang - $piddariarea";
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmppindahcustcabar00_".$puserid."_$now ";
    
    
    $query = "select icabangid, icustid, areaid, nama, alamat1, alamat2, kodepos, contact, "
            . " telp, fax, ikotaid, kota, isektorid, aktif, dispen, user1,oldflag, scode, grp, grp_spp, "
            . " o_icabangid, o_areaid, o_icustid, pertgl, batch_id, icabangid_hist, iareaid_hist, icustid_hist, "
            . " istatus, idisc, sys_now from MKT.icust where icabangid='$piddaricabang' and areaid='$piddariarea'";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select max(icustid) as icustid from MKT.icust where icabangid='$pidcabang'";
    $tampil= mysqli_query($cnit, $query);
    $ketemu=mysqli_num_rows($tampil);
    
?>


<?PHP
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
    mysqli_close($cnit);
?>