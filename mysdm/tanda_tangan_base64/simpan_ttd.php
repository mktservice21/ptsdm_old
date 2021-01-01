<?php
    session_start();
    include "../config/koneksimysqli.php";

if ($_GET['module']=="tampil_ttd"){
    $ssql="select TTDSPV from dbbudget.t_br_ttd where NOBR='$_POST[nobr]' and RTrim(TTDSPV)<>'' order by IDKU desc";
    $tampil=mysql_query($ssql);
    $ketemu= mysql_num_rows($tampil);
    if ($ketemu>0){
        $t= mysql_fetch_array($tampil);
        $rf= "{\"lines"."\":[";
        $jmkar=strlen($t['TTDSPV']);
        //$data1=substr("$t[TTD]",12,$jmkar); 
        $data1=substr("$t[TTDSPV]",10,$jmkar); 
        $data2=substr("$data1",0, -2); 
        echo "$data2";
        
    }else{
        //echo "GAGAL";
    }
}elseif ($_GET['module']=="simpan_ttdall"){
    if (!empty($_POST['unobr'])){
        $nobr="(".substr($_POST['unobr'], 0, -1).")";
        if (strtoupper($_POST['uact'])=="APPROVE") {
            $img=$_POST['uttd'];
            $img = base64_encode(serialize($img));
            
            mysqli_query($cnmy, "delete from dbbudget.t_br_ttd where NOBR in $nobr");
            $NO=1;
            $tampil=  mysqli_query($cnmy, "select NOBR from dbbudget.t_br where NOBR in $nobr");
            while ($r=  mysqli_fetch_array($tampil)) {
                $ssql="insert into dbbudget.t_br_ttd (NOBR, TTDSPV_ID, TTDSPV)"
                        . "values('$r[NOBR]', '$_SESSION[IDCARD]', '$img')";
                mysqli_query($cnmy, $ssql);
                
                $tampilid=  mysqli_query($cnmy, "select max(IDKU) as idku from dbbudget.t_br_ttd");
                $t=  mysqli_fetch_array($tampilid);    
                $NO=$t['idku'];
                $data="data:".$_POST['uttd'];
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $nama="img_".$r['NOBR']."_".$NO.".png";
                mysqli_query($cnmy, "update dbbudget.t_br_ttd set TTDSPV_GBR='$nama' where NOBR='$r[NOBR]'");
                file_put_contents('../images/tanda_tangan_base64/'.$nama, $data);
                 
                 
                $NO++;
            }

            echo "sukses";
        
        }else
            echo "tidak ada data yang tersimpan";
    }else
        echo "NOBR Kosong, tidak ada data yang tersimpan";
    
}else{
    //mysql_query("delete from nic_CVMP_TTD_WEB");
    $img=$_POST['uttd'];
    $img = base64_encode(serialize($img));
    
    $uClat="$_POST[ulat]"; if ($_POST['ulat']==""){ $uClat="0";}
    $uClong="$_POST[ulong]"; if ($_POST['ulong']==""){ $uClong="0";}
    $uClat = (float)$uClat; $uClong = (float)$uClong;
    
    $REFNO_ku=trim($_POST['unobr']);
    
    if (empty($REFNO_ku)){
        echo "NOBR Kosong, tidak ada data yang tersimpan";
    }elseif ($uClat==0 AND $uClong==0){
        echo "Lokasi belum tersimpan, Coba Lagi...";
    }else{
        mysqli_query($cnmy, "delete from dbbudget.t_br_ttd where NOBR='$_POST[unobr]'");
        $ssql="insert into dbbudget.t_br_ttd (NOBR, TTDSPV_ID, TTDSPV)"
                . "values('$_POST[unobr]', '$_SESSION[IDCARD]', '$img')";
        mysqli_query($cnmy, $ssql);
        $tampil=  mysqli_query($cnmy, "select max(IDKU) as idku from dbbudget.t_br_ttd");
        $t=  mysqli_fetch_array($tampil);    
        $NO=$t['idku'];
        $data="data:".$_POST['uttd'];
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $nama="img_".$REFNO_ku."_".$NO.".png";
        mysqli_query($cnmy, "update dbbudget.t_br_ttd set TTDSPV_GBR='$nama' where NOBR='$_POST[unobr]'");
        file_put_contents('../images/tanda_tangan_base64/'.$nama, $data);

        echo "sukses";
    }
}
?>

