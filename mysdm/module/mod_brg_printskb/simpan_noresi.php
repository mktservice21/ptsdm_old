<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    $dbname = "dbmaster";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    
    $berhasil="Tidak ada data yang disimpan";
    
    if ($module=="gimicprintskb" AND $act=="inputnoresi") {
        $pidgroup=$_POST['uidgroup'];
        $pidprntgroup=$_POST['uidprintgroup'];
        $pnoresi=$_POST['unoresi'];
        $pketkirim=$_POST['uketkirim'];
        $ptgl01 = str_replace('/', '-', $_POST['utgl']);
        $ptglresi = date('Y-m-d', strtotime($ptgl01));
        
        if (!empty($pketkirim)) $pketkirim = str_replace("'", ' ', $pketkirim);
        
        //$berhasil="$pidgroup, $ptglkirim";
        
        if (empty($pnoresi)) {
            $query = "UPDATE dbmaster.t_barang_keluar_kirim SET TGLKIRIM=NULL, NORESI=NULL, KETKIRIM=NULL WHERE IGROUP='$pidgroup' AND GRPPRINT='$pidprntgroup' AND (IFNULL(TGLTERIMA,'')='' OR IFNULL(TGLTERIMA,'0000-00-00')='0000-00-00')";
        }else{
            $query = "UPDATE dbmaster.t_barang_keluar_kirim SET TGLKIRIM='$ptglresi', NORESI='$pnoresi', KETKIRIM='$pketkirim' WHERE IGROUP='$pidgroup' AND GRPPRINT='$pidprntgroup'";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
        $berhasil="BERHASIL...";
    }
    
    mysqli_close($cnmy);
    echo $berhasil; exit;
?>