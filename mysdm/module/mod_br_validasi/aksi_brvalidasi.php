<?php
session_start();
include "../../config/koneksimysqli.php";
$module=$_GET['module'];
if ($module=="validate"){
    $empupdate=$_POST['ukaryawan'];
    if (!empty($_POST['chkbox_br'])){
        $nobri="(".substr($_POST['chkbox_br'], 0, -1).")";
        
        mysqli_query($cnmy, "update dbbudget.br0_ttd set TTDPROS_ID='$empupdate', TTDPROS_DATE=Current_Date() where "
                . " BRID in $nobri and ifnull(TTDPROS_ID,'')=''");
        
        
        /* untuk save image
        $nobri=substr($_POST['chkbox_br'], 0, -1);
        $tag=  str_replace("'", "", $nobri);
        $arr_kata = explode(",",$tag);
        $jumlah_tag = substr_count($tag, ",") + 1;
        for ($x=0; $x<=$jumlah_tag; $x++){
            if (!empty($arr_kata[$x])){
                $uBrId=trim($arr_kata[$x]);
                
            }
        }
         * 
         */

        echo "VALIDATE, SUKSES....";
        

    }else
        echo "BRID Kosong, tidak ada data yang tervalidate";
    
}elseif ($module=="unproses"){
    $nobri=('');
    if (!empty($_POST['chkbox_br'])){
        $nobri="(".substr($_POST['chkbox_br'], 0, -1).")";
    }
    
    mysqli_query($cnmy, "update dbbudget.br0_ttd set TTDPROS_ID=NULL, TTDPROS_DATE=NULL where "
            . " BRID in $nobri");
        
    /*
    $ssql="select TTDPROS, TTDPROS_GBR from dbbudget.br0_ttd where $nobri";
    $tampil=mysqli_query($cnmy, $ssql);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0){
        while ($r=  mysqli_fetch_array($tampil)){
            if (!empty($r['TTDPROS_GBR']))
                unlink("../../images/tanda_tangan_base64/$r[TTDPROS_GBR]");
        }
    }
    mysqli_query($cnmy, "DELETE FROM dbbudget.br0_ttd where $nobri");
     * 
     */
    
    echo "UNPROSES, BERHASIL....";
    
}elseif ($module=="reject"){
    
    
    echo "REJECT, BERHASIL....";
}elseif ($module=="pending"){
    
    
    echo "PENDING, BERHASIL....";
    
}

?>

