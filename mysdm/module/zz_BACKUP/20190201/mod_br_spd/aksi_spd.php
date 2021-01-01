<?php

    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
    
// Hapus 
if ($module=='saldosuratdana' AND $act=='hapus')
{
    mysqli_query($cnmy, "update $dbname.t_suratdana_br set stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE idinput='$_GET[id]'");
    mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br1 WHERE idinput='$_GET[id]'");
    mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br2 WHERE idinput='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='saldosuratdana')
{
    
    $pdivisi=$_POST['cb_divisi'];
    $pkode=$_POST['cb_kode'];
    $psubkode=$_POST['cb_kodesub'];
    $pnomor=$_POST['e_nomor'];
    $ptgl = str_replace('/', '-', $_POST['e_tglberlaku']);
    $periode1= date("Y-m-d", strtotime($ptgl));
    
    $pdivno=$_POST['e_nomordiv'];
    $pjumlah=str_replace(",","", $_POST['e_jmlusulan']);
    
    
    if ($act=="input") {
        $sql=  mysqli_query($cnmy, "select MAX(idinput) as NOURUT from $dbname.t_suratdana_br");
        $ketemu=  mysqli_num_rows($sql);
        $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $kodenya=$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }
    }else{
        $kodenya=$_POST['e_id'];
    }
    
    $pcoa="101-02-002";
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, nomor, tgl, nodivisi, jumlah, userid, coa4)values"
                . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$pnomor', '$periode1', '$pdivno', '$pjumlah', '$_SESSION[IDCARD]', '$pcoa')";
    }else{
        $query = "UPDATE $dbname.t_suratdana_br SET coa4='$pcoa', divisi='$pdivisi', kodeid='$pkode', "
                . " subkode='$psubkode', nomor='$pnomor', tgl='$periode1', nodivisi='$pdivno', jumlah='$pjumlah', userid='$_SESSION[IDCARD]' WHERE "
                . " idinput='$kodenya'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $tgl01f = $_POST['bulan1'];
    $tgl02t = $_POST['bulan2'];
    $periodef = date("Y-m-d", strtotime($tgl01f));
    $periodet = date("Y-m-d", strtotime($tgl02t));
    $query = "INSERT INTO $dbname.t_suratdana_br2 (idinput,tglf,tglt)values('$kodenya', '$periodef', '$periodet')";
    mysqli_query($cnmy, $query);
    
    
    
    $query = "DELETE FROM $dbname.t_suratdana_br1 WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $purutan=1;
    $pkodeurutan=1;
    for ($nm=1; $nm<=11; $nm++){
    
        $datanya="";
        if ($nm==1) {
            if (isset($_POST['chkbox_idA'])) $datanya=$_POST['chkbox_idA'];
        }elseif ($nm==2) {
            if (isset($_POST['chkbox_idB'])) $datanya=$_POST['chkbox_idB'];
        }elseif ($nm==3) {
            if (isset($_POST['chkbox_idC'])) $datanya=$_POST['chkbox_idC'];
        }elseif ($nm==4) {
            if (isset($_POST['chkbox_idD'])) $datanya=$_POST['chkbox_idD'];
        }elseif ($nm==5) {
            if (isset($_POST['chkbox_idE'])) $datanya=$_POST['chkbox_idE'];
        }elseif ($nm==6) {
            if (isset($_POST['chkbox_idF'])) $datanya=$_POST['chkbox_idF'];
        }elseif ($nm==7) {
            if (isset($_POST['chkbox_idG'])) $datanya=$_POST['chkbox_idG'];
        }elseif ($nm==8) {
            if (isset($_POST['chkbox_idH'])) $datanya=$_POST['chkbox_idH'];
        }elseif ($nm==9) {
            if (isset($_POST['chkbox_idI'])) $datanya=$_POST['chkbox_idI'];
        }elseif ($nm==10) {
        }elseif ($nm==11) {
            
        }
        

        if (!empty($datanya)){
            
            $tag = implode(',',$datanya);
            $arr_kata = explode(",",$tag);
            $count_kata = count($arr_kata);
            $jumlah_tag = substr_count($tag, ",") + 1;
            $u=0;
            for ($x=0; $x<=$jumlah_tag; $x++){
                if (!empty($arr_kata[$u])){
                    $uTag=trim($arr_kata[$u]);
                    $kata= explode("-", $uTag);
                    $nobrinput="";
                    $kodeinput="";
                    if (isset($kata[0])) $nobrinput=$kata[0];
                    if (isset($kata[1])) $kodeinput=$kata[1];

                    if (!empty($nobrinput) AND $nobrinput <> "0") {
                    
                        //eksekusi input
                        $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan)VALUES"
                                . "('$kodenya', '$nobrinput', '$kodeinput', '$pkodeurutan')";
                        mysqli_query($cnmy, $query);
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                        if ($purutan==30) {
                            $purutan=0;
                            $pkodeurutan++;
                        }
                        $purutan++;
                    }
                    
                }
                $u++;
            }

        }
        
    }
    
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
    
?>