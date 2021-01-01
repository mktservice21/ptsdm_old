<?php
session_start();
include "../../config/koneksimysqli.php";
$module=$_GET['module'];
if ($module=="simpan_ttdallam"){
    
    if (!empty($_POST['unobr'])){
        $nobr="(".substr($_POST['unobr'], 0, -1).")";
        if (strtoupper($_POST['ket'])=="APPROVE") {
            $img=$_POST['uttd'];
            $img = base64_encode(serialize($img));
            
            mysqli_query($cnmy, "delete from dbbudget.br0_ttd where brId in $nobr");
            $NO=1;
            $tampil=  mysqli_query($cnmy, "select brId from dbbudget.br0 where brId in $nobr");
            while ($r=  mysqli_fetch_array($tampil)) {
                $ssql="insert into dbbudget.br0_ttd (brId, TTDAM_ID, TTDAM)"
                        . "values('$r[brId]', '$_SESSION[IDCARD]', '$img')";
                mysqli_query($cnmy, $ssql);
                
                $tampilid=  mysqli_query($cnmy, "select max(IDKU) as idku from dbbudget.br0_ttd");
                $t=  mysqli_fetch_array($tampilid);    
                $NO=$t['idku'];
                $data="data:".$_POST['uttd'];
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $nama="img_".$r['brId']."_".$NO.".png";
                mysqli_query($cnmy, "update dbbudget.br0_ttd set TTDAM_GBR='$nama' where brId='$r[brId]'");
                file_put_contents('../../images/tanda_tangan_base64/'.$nama, $data);
                 
                 
                $NO++;
            }

            echo "APPROVE, SUKSES....";
        
        }else
            echo "tidak ada data yang tersimpan";
    }else
        echo "brId Kosong, tidak ada data yang tersimpan";
    
}elseif ($module=="unapprove"){
    $filterbr=('');
    if (!empty($_POST['chkbox_br'])){
        $filterbr=" brId in (".substr($_POST['chkbox_br'], 0, -1).")";
    }
    

    $ssql="select TTDAM, TTDAM_GBR from dbbudget.br0_ttd where $filterbr";
    $tampil=mysqli_query($cnmy, $ssql);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0){
        while ($r=  mysqli_fetch_array($tampil)){
            if (!empty($r['TTDAM_GBR']))
                unlink("../../images/tanda_tangan_base64/$r[TTDAM_GBR]");
        }
    }
    mysqli_query($cnmy, "DELETE FROM dbbudget.br0_ttd where $filterbr");
    
    echo "UNAPPROVE, BERHASIL....";
    
}elseif ($module=="reject"){
    
    
    echo "REJECT, BERHASIL....";
}elseif ($module=="pending"){
    
    
    echo "PENDING, BERHASIL....";
    
}

?>

