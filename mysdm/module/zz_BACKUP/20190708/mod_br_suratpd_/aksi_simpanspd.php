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
if ($module=='suratpd' AND $act=='hapus')
{
    mysqli_query($cnmy, "update $dbname.t_suratdana_br set stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE idinput='$_GET[id]'");
    mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br1 WHERE idinput='$_GET[id]'");
    mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br_d WHERE idinput='$_GET[id]'");
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='suratpd')
{
    if (empty($_SESSION['JMLRECSPD'])) $_SESSION['JMLRECSPD']=30;
    
            
    $userid=$_SESSION['IDCARD'];
    
    $pkode=$_POST['cb_kode'];
    $psubkode=$_POST['cb_kodesub'];
    $pdivisi=$_POST['cb_divisi'];
    $pnomor=$_POST['e_nomor'];
    $pketerangan=$_POST['e_keterangan'];
    
    $ptgl = $_POST['e_tglberlaku'];
    $periode1= date("Y-m-d", strtotime($ptgl));
    
    $pdivno=$_POST['e_nomordiv'];
    $pjumlah=str_replace(",","", $_POST['e_jmlusulan']);
    
    
    
    $periodef=$periode1;
    $periodet=$periode1;
    
    $pmytgl = $_POST['e_periode1'];
    $pmytg2 = $_POST['e_periode2'];
    
    if (!empty($pmytgl)) $periodef = date("Y-m-d", strtotime($pmytgl));
    if (!empty($pmytg2)) $periodet = date("Y-m-d", strtotime($pmytg2));

    
    $pjenis=$_POST['cb_jenis'];//lampiran
    $padvance=$_POST['cb_jenispilih'];//advance/klaim/belum ada kuitansi
    
    $ppertipe=$_POST['cb_pertipe'];
    
    
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
    
    //echo "$periodef, $periodet <br/>$pkode, $psubkode <br/>$kodenya<br/>$pjenis<br/>$ppertipe<br/>$padvance<br/>"; exit;
    
    $pcoa="101-02-002";
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, nomor, tgl, nodivisi, jumlah, "
                . " userid, coa4, lampiran, tglf, tglt, kodeperiode, karyawanid, periodeby, jenis_rpt, keterangan)values"
                . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$pnomor', '$periode1', '$pdivno', '$pjumlah', "
                . " '$_SESSION[IDCARD]', '$pcoa', '$pjenis', '$periodef', '$periodet', '$pkodeperiode', '$_SESSION[IDCARD]', '$ppertipe', '$padvance', '$pketerangan')";
    }else{
        $query = "UPDATE $dbname.t_suratdana_br SET lampiran='$pjenis', pilih='Y', coa4='$pcoa', divisi='$pdivisi', kodeid='$pkode', "
                . " subkode='$psubkode', nomor='$pnomor', tgl='$periode1', nodivisi='$pdivno', jumlah='$pjumlah', userid='$_SESSION[IDCARD]', "
                . " tglf='$periodef', tglt='$periodet', kodeperiode='$pkodeperiode', periodeby='$ppertipe', jenis_rpt='$padvance', keterangan='$pketerangan' WHERE "
                . " idinput='$kodenya'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if (!empty($pnomor)) {
        $query = "UPDATE $dbname.t_suratdana_br SET tglspd='$periode1', userproses='$userid', tgl_proses=NOW() WHERE idinput='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    $query = "DELETE FROM $dbname.t_suratdana_br_d WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    
    $query = "DELETE FROM $dbname.t_suratdana_br1 WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    
    
    if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {
        

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
        $tmp01 =" dbtemp.IDSTHHSR01_".$userid."_$now ";


        if (!empty($fidbr)) {

            $fidbr=substr($fidbr, 0, -1);
            $fidbr="(".$fidbr.")";

            if (!empty($kodenya)){
                $kodeinput="C";//KODE BR ANNE
                
                $cfiledamount = " jumlah ";
                if ($padvance=="A"){
                }else{
                    $cfiledamount = " jumlah1 as jumlah ";
                }
                        
                $query = "SELECT brId, divprodid divisi, $cfiledamount FROM hrd.br0 WHERE brId IN $fidbr";
                $query = "create TEMPORARY table $tmp01 ($query)"; 
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                //simpan detail

                $purutan=1;
                $pkodeurutan=1;


                $query="SELECT DISTINCT ifnull(brId,'') nobrid, divisi, jumlah from $tmp01 order by divisi, brId";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($tr= mysqli_fetch_array($tampil)) {
                        $nobrinput=$tr['nobrid'];
                            
                        $pamount=0;
                        if (isset($tr['jumlah'])) $pamount=$tr['jumlah'];
                        
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
        
        
        
    }else{
        $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput,divisi,jumlah)values('$kodenya', '$pdivisi', '$pjumlah')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput,urutan,amount)values('$kodenya', 1, '$pjumlah')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    $query = "UPDATE $dbname.t_suratdana_br SET bulan2=NULL, nomor2='', nodivisi2='' WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($pkode=="3") {
        $ptgl = $_POST['e_tglberlaku'];
        $periode1= date("Y-m-01", strtotime($ptgl));
    
        $pnomor2=$_POST['cb_ajsnospd'];
        $pnodivisi2=$_POST['cb_ajsnobr'];
        //$pdivisi2=$_POST['divisi2'];
        
        $hari_ini = date("Y-m-d");
        
        $setupdatetgl="";
        if ($act=="input") $setupdatetgl=" tgl='$hari_ini',  ";
        
        $query = "UPDATE $dbname.t_suratdana_br SET $setupdatetgl bulan2='$periode1', nomor2='$pnomor2', nodivisi2='$pnodivisi2' WHERE "
                . " idinput='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    }
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
}

?>

