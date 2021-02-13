<?php
    session_start();
if ($_GET['module']=="gantitombol") {
    $ptipe=$_POST['utipe'];
    ?>
        <div id="c_tombol">
            <div class='col-sm-4'>
                <small>&nbsp;</small>
               <div class="form-group">
                   <?PHP
                   if ($ptipe=="A") {
                       ?>
                       <input type='button' class='btn btn-success btn-xs' id="s-submit" value="Belum Proses" onclick="RefreshDataTabel('1')">&nbsp;
                       <input type='button' class='btn btn-info btn-xs' id="s-submit" value="Sudah Proses" onclick="RefreshDataTabel('2')">&nbsp;
                       <input type='button' class='btn btn-danger btn-xs' id="s-submit" value="Pending" onclick="RefreshDataTabel('3')">&nbsp;
                       <input type='hidden' class='btn btn-default btn-xs' id="s-submit" value="Sudah Proses HOS" onclick="RefreshDataTabel('4')">&nbsp;
                       <?PHP
                   }else{
                       ?>
                       <input type='button' class='btn btn-success btn-xs' id="s-submit" value="Belum Proses" onclick="DataPD('1')">&nbsp;
                       <input type='button' class='btn btn-info btn-xs' id="s-submit" value="Sudah Proses" onclick="DataPD('2')">&nbsp;
                       <input type='button' class='btn btn-dark btn-xs' id="s-submit" value="Transfer BR OTC" onclick="TransferData('1')">&nbsp;
                       <?PHP
                   }
                   ?>
               </div>
           </div>
       </div>
    <?PHP

}elseif ($_GET['module']=="hitungtotalcekbox") {
    include "../../config/koneksimysqli.php";
    
    $pnoid=$_POST['unoidbr'];
    
    
    $totalinput=0;
    
    $query="SELECT SUM(total) as jumlah from dbmaster.t_spg_gaji_br0 WHERE idbrspg IN $pnoid";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $tr= mysqli_fetch_array($tampil);
        if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
    }
    
    $pbpjssdmtot=0;
    
    $query="SELECT SUM(rptotal2) as rptotal2 from dbmaster.t_spg_gaji_br0 as a "
            . " join dbmaster.t_spg_gaji_br1 as b on a.idbrspg=b.idbrspg WHERE a.idbrspg IN $pnoid AND b.kodeid ='10'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $tr= mysqli_fetch_array($tampil);
        if (!empty($tr['rptotal2'])) $pbpjssdmtot=$tr['rptotal2'];
    }
    $totalinput=(DOUBLE)$totalinput+(DOUBLE)$pbpjssdmtot;
    
    echo $totalinput;
    
}elseif ($_GET['module']=="simpan") {
    $berhasil="Tidak ada data yang diproses...";
    include "../../config/koneksimysqli.php";
    $apvid=$_SESSION['IDCARD'];
    
    if (empty($apvid)) {
        echo "Anda harus login ulang....";
        exit;
    }
    
    $pnoid=$_POST['unoidbr'];
    $ptipests=$_POST['utipests'];
    
    $gbrapv=$_POST['uttd'];
    
    //, sts='$ptipests'
    $query = "UPDATE dbmaster.t_spg_gaji_br0 set apv2='$apvid', apvtgl2=NOW(), apvgbr2='$gbrapv' WHERE "
            . " idbrspg IN $pnoid";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    $berhasil = "data berhasil diproses";
    
    echo $berhasil;
}elseif ($_GET['module']=="simpanpending") {
    $berhasil="Tidak ada data yang diproses...";
    include "../../config/koneksimysqli.php";
    $apvid=$_SESSION['IDCARD'];
    
    if (empty($apvid)) {
        echo "Anda harus login ulang....";
        exit;
    }
    
    $pnoid=$_POST['unoidbr'];
    $ptipests=$_POST['utipests'];
    
    
    $query = "UPDATE dbmaster.t_spg_gaji_br0 set apv2='$apvid', apvtgl2=NOW(), sts='$ptipests' WHERE "
            . " idbrspg IN $pnoid";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    $berhasil = "data berhasil diproses";
    
    echo $berhasil;
}elseif ($_GET['module']=="hapus") {
    $berhasil="Tidak ada data yang diunproses...";
    include "../../config/koneksimysqli.php";
    
    $pnoid=$_POST['unoidbr'];
    
    //, sts=''
    $query = "UPDATE dbmaster.t_spg_gaji_br0 set apv2=NULL, apvtgl2=NULL, apvgbr2=NULL WHERE "
            . " idbrspg IN $pnoid";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    $berhasil = "data berhasil diunproses";
    
    echo $berhasil;
}elseif ($_GET['module']=="tampilkannodivisi") {
    include "../../config/koneksimysqli.php";
    
    $pidcab=$_POST['ucabang'];
    $ptgl=$_POST['utgl'];
    $pbulan = date('Ym', strtotime($ptgl));
    
    $query = "select distinct nodivisi, idinput from dbmaster.t_spg_gaji_br0 WHERE "
            . " stsnonaktif<>'Y' AND DATE_FORMAT(periode,'%Y%m')='$pbulan' AND IFNULL(nodivisi,'')<>'' ORDER BY 2,1";
    $tampil= mysqli_query($cnmy, $query);

    echo "<option value='' SELECTED>--All--</option>";
    while ($nr= mysqli_fetch_array($tampil)) {
        $nhnpdivisi=$nr['nodivisi'];
        $nhidinput=$nr['idinput'];
        echo "<option value='$nhidinput'>$nhnpdivisi</option>";
    }
    
    mysqli_close($cnmy);
    
}elseif ($_GET['module']=="hapusdatabrtrans") {
    include "../../config/koneksimysqli.php";
    //include "../../config/koneksimysqli_it.php";
    $berhasil="tidak ada data yang dihapus...";
    
    $pbrid=$_POST['ubridotc'];
    $pidinput=$_POST['uidinput'];
    $pikete=$_POST['uket'];
    
    if (empty($pbrid)) {
        echo $berhasil;
        exit;
    }
    
    if (empty($pidinput)) {
        echo $berhasil;
        exit;
    }
    
    $f_brhapus = " AND subkode='05' AND kodeid='12' ";
    $f_updatenulspg=" brotcid='$pbrid' AND IFNULL(brotcid2,'')='' AND IFNULL(brotcid3,'')='' ";
    if ($pikete=="2") {
        $f_brhapus = " AND subkode='03' AND kodeid='08' ";
        $f_updatenulspg=" brotcid2='$pbrid' ";
    }elseif ($pikete=="3") {
        $f_brhapus = " AND subkode='10' AND kodeid='93' ";
        $f_updatenulspg=" brotcid3='$pbrid' ";
    }
    
    $query = "DELETE FROM hrd.br_otc WHERE brOtcId='$pbrid'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "DELETE FROM dbmaster.t_suratdana_br1 WHERE idinput='$pidinput' AND bridinput='$pbrid' AND kodeinput='D'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($pikete=="2") {
        $query = "UPDATE dbmaster.t_spg_gaji_br0 SET brotcid2='' WHERE idinput='$pidinput' AND $f_updatenulspg";
    }elseif ($pikete=="3") {
        $query = "UPDATE dbmaster.t_spg_gaji_br0 SET brotcid3='' WHERE idinput='$pidinput' AND $f_updatenulspg";
    }else{
        $query = "UPDATE dbmaster.t_spg_gaji_br0 SET brotcid='' WHERE idinput='$pidinput' AND $f_updatenulspg";
    }
    $query = "UPDATE dbmaster.t_spg_gaji_br0 SET brotcid='', brotcid2='', brotcid3='' WHERE idinput='$pidinput' AND $f_updatenulspg";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $berhasil="";
    
    mysqli_close($cnmy);
    //mysqli_close($cnit);
    
    echo $berhasil;
    
}elseif ($_GET['module']=="xxx") {
    
}
?>
