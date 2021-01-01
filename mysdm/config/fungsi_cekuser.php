<?php
    function CekHakAkses($idMenu, $user){
        $ket="N";
        $ssql="select ID_AKSES from t_groupmenu where ID='$idMenu' AND ID_GROUP='$user'";
        $ketemu=mysql_num_rows(mysql_query($ssql));
        if ($ketemu>0) $ket="Y";
        return $ket;
    }

    function CekManipulasi($idMenu, $cKode, $user){
        if ($cKode==2){
            $ssql="EDIT AS FIELDNYA";
        }elseif ($cKode==3){
            $ssql="HAPUS AS FIELDNYA";
        }else{
            $ssql="TAMBAH AS FIELDNYA";
        }
        $ssql="select $ssql from t_groupmenu where ID='$idMenu' AND ID_GROUP='$user'";

        $usr=mysql_fetch_array(mysql_query($ssql));
        $ketnya=$usr['FIELDNYA'];
        
        if ($cKode==1){
            if ($_SESSION['S_TAMBAH']=="N") $ketnya="N";
        }elseif ($cKode==2){
            if ($_SESSION['S_EDIT']=="N") $ketnya="N";
        }else{
            if ($_SESSION['S_HAPUS']=="N") $ketnya="N";
        }

        return $ketnya;
    }
?>
