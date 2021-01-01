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
if ($module=='spdkas' AND $act=='hapus')
{
    mysqli_query($cnmy, "update $dbname.t_suratdana_br set stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE idinput='$_GET[id]'");
    //mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br1 WHERE idinput='$_GET[id]'");
    //mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br_d WHERE idinput='$_GET[id]'");
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='spdkas')
{
    
    if (empty($_SESSION['JMLRECSPD'])) $_SESSION['JMLRECSPD']=30;
    
    $userid=$_SESSION['IDCARD'];
    
    $pkode=$_POST['cb_kode'];
    $psubkode=$_POST['cb_kodesub'];
    $pdivisi="HO";
    $pnomor="";
    
    $ptgl = $_POST['e_tglberlaku'];
    $periode1= date("Y-m-d", strtotime($ptgl));
    
    $pdivno=$_POST['e_nomordiv'];
    $pjumlah=str_replace(",","", $_POST['e_jmlusulan']);
    
    $pjumlah_kb=str_replace(",","", $_POST['e_jmlusulan_kb']);
    
    
    $mytgl1 = $_POST['e_periode1'];
    $mytgl2 = $_POST['e_periode2'];
    
    $mper1= date("Y-m-d", strtotime($mytgl1));
    $mper2= date("Y-m-d", strtotime($mytgl2));
    
    $periodef=$mper1;
    $periodet=$mper2;

    
    $pjenis="";//lampiran
    $padvance="";//advance/klaim/belum ada kuitansi
    
    $ppertipe="";
    
    
    $pkodeperiode="";
    
    $kodenya="";
    
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
    
    
    //echo "$periodef, $periodet <br/>$pkode, $psubkode <br/>$kodenya<br/>$pjenis<br/>$ppertipe<br/>$padvance<br/>$pjmle<br/>$pjmlpea<br/>$pjmlp<br/>"; exit;
    
    
    $pcoa="101-02-002";
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, nomor, tgl, nodivisi, jumlah, "
                . " userid, coa4, lampiran, tglf, tglt, kodeperiode, karyawanid, periodeby, jenis_rpt, jumlah2)values"
                . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$pnomor', '$periode1', '$pdivno', '$pjumlah', "
                . " '$_SESSION[IDCARD]', '$pcoa', '$pjenis', '$periodef', '$periodet', '$pkodeperiode', '$_SESSION[IDCARD]', '$ppertipe', '$padvance', '$pjumlah_kb')";
    }else{
        $query = "UPDATE $dbname.t_suratdana_br SET lampiran='$pjenis', pilih='Y', coa4='$pcoa', divisi='$pdivisi', kodeid='$pkode', "
                . " subkode='$psubkode', nomor='$pnomor', tgl='$periode1', nodivisi='$pdivno', jumlah='$pjumlah', userid='$_SESSION[IDCARD]', "
                . " tglf='$periodef', tglt='$periodet', kodeperiode='$pkodeperiode', periodeby='$ppertipe', jenis_rpt='$padvance', jumlah2='$pjumlah_kb' WHERE "
                . " idinput='$kodenya'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "DELETE FROM $dbname.t_suratdana_br_d WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    
    $query = "DELETE FROM $dbname.t_suratdana_br1 WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    
    
    $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput,divisi,jumlah)values('$kodenya', '$pdivisi', '$pjumlah')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    
    $fidbr="";
    for ($n=1; $n<=1; $n++){
        
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
    
    if (!empty($fidbr)) {
        
        $fidbr=substr($fidbr, 0, -1);
        $fidbr="(".$fidbr.")";
        
        if (!empty($kodenya)){
            
            //simpan detail
            
            $purutan=1;
            $pkodeurutan=1;
            $kodeinput="K";//KODE KAS

            $query="SELECT DISTINCT ifnull(kasId,'') nobrid, jumlah from hrd.kas WHERE kasId IN $fidbr order by kasId";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($tr= mysqli_fetch_array($tampil)) {
                    $nobrinput=$tr['nobrid'];
                    $pamount=$tr['jumlah'];
                    //eksekusi input
                    $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan, amount)VALUES"
                            . "('$kodenya', '$nobrinput', '$kodeinput', '$pkodeurutan', '$pamount')";
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
            
                //kasbon
                $kodeinput="T";
                
                $fidbr="";
                foreach ($_POST['chk_jml_kb'] as $nobrinput) {
                    if (!empty($nobrinput) AND $nobrinput <> "0") {
                        $fidbr=$fidbr."'".$nobrinput."',";
                    }
                }


                if (!empty($fidbr)) {
                    $fidbr=substr($fidbr, 0, -1);
                    $fidbr="(".$fidbr.")";        

                    $query="SELECT DISTINCT ifnull(idkasbon,'') nobrid, jumlah from dbmaster.t_kasbon WHERE idkasbon IN $fidbr order by idkasbon";
                    $tampil= mysqli_query($cnmy, $query);
                    $ketemu= mysqli_num_rows($tampil);
                    if ($ketemu>0) {
                        while ($tr= mysqli_fetch_array($tampil)) {
                            $nobrinput=$tr['nobrid'];
                            $pamount=$tr['jumlah'];
                            //eksekusi input
                            $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, amount)VALUES"
                                    . "('$kodenya', '$nobrinput', '$kodeinput', '$pamount')";
                            mysqli_query($cnmy, $query);
                        }

                    }
                    
                    
                }
                
                //end kasbon
            
        }
                
                
        
    }else{
        
        $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput,urutan,amount)values('$kodenya', 1, '$pjumlah')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
}
?>
