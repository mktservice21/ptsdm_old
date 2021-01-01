<?php
    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnmy;
    $dbname = "dbmaster";
    
    
// Hapus 
if ($module=='spd' AND $act=='hapus')
{
    $_GET['id']=$_POST['e_id'];
    mysqli_query($cnmy, "update $dbname.t_suratdana_br set stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE idinput='$_GET[id]'");
    mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br1 WHERE idinput='$_GET[id]'");
    mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br_d WHERE idinput='$_GET[id]'");
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='spd')
{
    if (empty($_SESSION['JMLRECSPD'])) $_SESSION['JMLRECSPD']=30;
    
    $pdivisi=$_POST['cb_divisi'];
    $pjenis=$_POST['cb_jenis'];
    
    $pkode="1";
    $psubkode="01";
    if ($pjenis=="N"){
        $pkode="2";
        $psubkode="20";
    }
    
    $ptgl = $_POST['e_tglberlaku'];
    $periode1= date("Y-m-d", strtotime($ptgl));
    $pdivno=$_POST['e_nomordiv'];
    $pjumlah=str_replace(",","", $_POST['e_jmlusulan']);
    
    
    $pmytgl = $_POST['e_periode01'];
    $pmytg2 = $_POST['e_periode02'];
    
    $periodef = date("Y-m-d", strtotime($pmytgl));
    $periodet = date("Y-m-d", strtotime($pmytg2));
    


    
    $sql=  mysqli_query($cnmy, "select MAX(idinput) as NOURUT from $dbname.t_suratdana_br");
    $ketemu=  mysqli_num_rows($sql);
    $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
    if ($ketemu>0){
        $o=  mysqli_fetch_array($sql);
        $urut=$o['NOURUT']+1;
        $kodenya=$urut;
    }
    
    $pcoa="101-02-002";
    if (!empty($kodenya)){
        $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, tgl, nodivisi, jumlah, "
                . " userid, coa4, lampiran, tglf, tglt)values"
                . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$periode1', '$pdivno', '$pjumlah', "
                . " '$_SESSION[IDCARD]', '$pcoa', '$pjenis', '$periodef', '$periodet')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    $fidbr="";
    for ($n=1; $n<=2; $n++){
        
        $dname="chk_jml".$n;
        $datanya=$_POST[$dname];
      
        if (!empty($datanya)){
            $tag = implode(',',$datanya);
            $arr_kata = explode(",",$tag);
            $count_kata = count($arr_kata);
            $jumlah_tag = substr_count($tag, ",") + 1;
            $u=0;
            for ($x=0; $x<=$jumlah_tag; $x++){
                if (!empty($arr_kata[$u])){
                    $nobrinput=trim($arr_kata[$u]);
                    if (!empty($nobrinput) AND $nobrinput <> "0") {
                        $fidbr=$fidbr."'".$nobrinput."',";
                    }
                }
                $u++;
            }
        }
        
        $datanya="";
         
    }
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.IDSETH01_".$userid."_$now ";
    
    if (!empty($fidbr)) {
        
        $fidbr=substr($fidbr, 0, -1);
        $fidbr="(".$fidbr.")";
        
        if (!empty($kodenya)){
            
            $query = "select brId, divprodid divisi, jumlah from hrd.br0 where brId in $fidbr";
            $query = "create TEMPORARY table $tmp01 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            //simpan detail
            
            $purutan=1;
            $pkodeurutan=1;
            $kodeinput="A";//KODE BR ERNI

            $query="SELECT DISTINCT ifnull(brId,'') nobrid, divisi from $tmp01 order by divisi, brId";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($tr= mysqli_fetch_array($tampil)) {
                    $nobrinput=$tr['nobrid'];
                    //eksekusi input
                    $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan)VALUES"
                            . "('$kodenya', '$nobrinput', '$kodeinput', '$pkodeurutan')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                    if ($purutan==(INT)$_SESSION['JMLRECSPD']) {
                        $purutan=0;
                        $pkodeurutan++;
                    }
                    $purutan++;
                }

            }

            //END simpan detail
            
            
            $query = "SELECT divisi, sum(jumlah) jumlah FROM $tmp01 GROUP BY 1 ORDER BY divisi";
            $result2 = mysqli_query($cnmy, $query);
            $records2 = mysqli_num_rows($result2);
            if ($records2>0){
                while ($sh= mysqli_fetch_array($result2)) {
                    $ndivisi=$sh['divisi'];
                    $pjumlah=$sh['jumlah'];
                    
                    $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput, divisi, jumlah)values"
                            . "('$kodenya', '$ndivisi', '$pjumlah')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
                    
                    
                }
            }
            
            
        }
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        
        
    }
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    //echo "$fidbr<br/>$kodenya.... $pdivisi, $pjenis, $pkode, $psubkode, $periode1, $pdivno, $pjumlah... $periodef - $periodet";
    
}
?>
