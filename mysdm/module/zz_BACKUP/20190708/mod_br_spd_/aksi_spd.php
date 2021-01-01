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
    mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br_d WHERE idinput='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='saldosuratdana')
{
    $pkode=$_POST['cb_kode'];
    $psubkode=$_POST['cb_kodesub'];
    
    $pmystsyginput="";
    if ($_SESSION['IDCARD']=="0000000566") {
        $pmystsyginput=1;
    }elseif ($_SESSION['IDCARD']=="0000001043") {
        $pmystsyginput=2;
    }elseif ($_SESSION['IDCARD']=="0000000148") {
        if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {
            $pmystsyginput=5;
        }
    }else{
        //$periodef, $periodet
        if ($pkode=="1" AND $psubkode=="03") {//ria
            $pmystsyginput=3;
        }elseif ($pkode=="2" AND $psubkode=="21") {//marsis
            $pmystsyginput=4;
        }
    }
    
    
    $ppertipe=$_POST['cb_pertipe'];
    $pdivisi=$_POST['cb_divisi'];
    $pjenis=$_POST['cb_jenis'];
    $padvance=$_POST['cb_jenispilih'];
    $pnomor=$_POST['e_nomor'];
    //$ptgl = str_replace('/', '-', $_POST['e_tglberlaku']);
    $ptgl = $_POST['e_tglberlaku'];
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
    
    $periodef = date("Y-m-d", strtotime($ptgl));
    $periodet = date("Y-m-d", strtotime($ptgl));
    
    $pmytgl = $_POST['e_periode1'];
    $pmytg2 = $_POST['e_periode2'];
    
    if ($pmystsyginput==1 OR $pmystsyginput==2 OR $pmystsyginput==5) {
        
        $periodef = date("Y-m-d", strtotime($pmytgl));
        $periodet = date("Y-m-d", strtotime($pmytg2));
        
    }else{
        
        $myperiode1= date("Y-m-01", strtotime($pmytgl));
        $myperiode2= date("Y-m-15", strtotime($pmytg2));


        $pkodeperiode="";
        $pstspilihan="";

        if ($pdivisi=="OTC") {
        }else{
            if ($pkode=="1" AND $psubkode=="03") {
                $pkodeperiode="1";
                $nd1= date("d", strtotime($pmytgl));
                if ((INT)$nd1>=16) {
                    $pkodeperiode="2";
                    $myperiode1= date("Y-m-16", strtotime($pmytgl));
                    $myperiode2= date("Y-m-t", strtotime($pmytg2));
                }

            }elseif ($pkode=="2" AND $psubkode=="21") {
                $pstspilihan=$_POST['sts_rpt'];
                $myperiode1= date("Y-m-01", strtotime($pmytgl));
                $myperiode2= date("Y-m-t", strtotime($pmytg2));

            }

            $periodef=$myperiode1;
            $periodet=$myperiode2;

        }
        
    }
    
    
    if ($pmystsyginput==1 OR $pmystsyginput==2) {//erni OR prita
        if ($padvance=="A" OR $padvance=="D"){
        }else{
            $pkode="2";
            $psubkode="20";
        }
    }
    //echo "$periodef, $periodet <br/>$pkode, $psubkode <br/>$kodenya<br/>$pjenis<br/>$ppertipe<br/>$padvance<br/>"; exit;
    
    
    $pcoa="101-02-002";
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, nomor, tgl, nodivisi, jumlah, "
                . " userid, coa4, lampiran, tglf, tglt, kodeperiode, sts, karyawanid, periodeby, jenis_rpt)values"
                . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$pnomor', '$periode1', '$pdivno', '$pjumlah', "
                . " '$_SESSION[IDCARD]', '$pcoa', '$pjenis', '$periodef', '$periodet', '$pkodeperiode', '$pstspilihan', '$_SESSION[IDCARD]', '$ppertipe', '$padvance')";
    }else{
        $query = "UPDATE $dbname.t_suratdana_br SET lampiran='$pjenis', pilih='Y', coa4='$pcoa', divisi='$pdivisi', kodeid='$pkode', "
                . " subkode='$psubkode', nomor='$pnomor', tgl='$periode1', nodivisi='$pdivno', jumlah='$pjumlah', userid='$_SESSION[IDCARD]', "
                . " tglf='$periodef', tglt='$periodet', kodeperiode='$pkodeperiode', sts='$pstspilihan', periodeby='$ppertipe', jenis_rpt='$padvance' WHERE "
                . " idinput='$kodenya'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if (isset($_POST['e_chkpilih'])) {
        if ($_POST['e_chkpilih']=="N") {
            $query = "UPDATE $dbname.t_suratdana_br SET pilih='N' WHERE idinput='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
   
    
    
    $query = "DELETE FROM $dbname.t_suratdana_br_d WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    
    if ($pmystsyginput!=1 AND $pmystsyginput!=2 AND $pmystsyginput!=3 AND $pmystsyginput!=4 AND $pmystsyginput!=5) {
        $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput,divisi,jumlah)values('$kodenya', '$pdivisi', '$pjumlah')";
        mysqli_query($cnmy, $query);
    }
    
    
    
    $query = "DELETE FROM $dbname.t_suratdana_br1 WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $purutan=1;
    $pkodeurutan=1;
    
    if ($pdivisi=="OTC") {
        
        $kodeinput="D";//KODE BR OTC
        
        $filterlampiran="";
        if (!empty($pjenis)) $filterlampiran = " and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$pjenis' ";
        
        $query="SELECT DISTINCT ifnull(brOtcId,'') nobrid from hrd.br_otc where "
                . " brOtcId not in (SELECT DISTINCT ifnull(brOtcId,'') from hrd.br_otc_reject) AND "
                . " DATE_FORMAT(tglbr,'%Y-%m-%d') = '$periode1' $filterlampiran"
                . " AND brOtcId NOT IN (SELECT DISTINCT ifnull(bridinput,'') from dbmaster.t_suratdana_br1 WHERE kodeinput='D')";
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

                if ($purutan==30) {
                    $purutan=0;
                    $pkodeurutan++;
                }
                $purutan++;
            }
            
        }
        
    }else{
        
        if ($pmystsyginput==1 OR $pmystsyginput==2 OR $pmystsyginput==5) {//erni or prita

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
            $tmp01 =" dbtemp.IDSETHH01_".$userid."_$now ";


            if (!empty($fidbr)) {

                $fidbr=substr($fidbr, 0, -1);
                $fidbr="(".$fidbr.")";

                if (!empty($kodenya)){
                    $kodeinput="A";//KODE BR ERNI
                    if ($pmystsyginput==2) $kodeinput="B";//KODE BR PRITA
                    if ($pmystsyginput==5) $kodeinput="C";//KODE BR ANE
                    
                    $query = "SELECT brId, divprodid divisi, jumlah FROM hrd.br0 WHERE brId IN $fidbr";
                    if ($pmystsyginput==2 AND $padvance=="D") {
                        $kodeinput="E";
                        $query = "SELECT klaimId brId, DIVISI divisi, jumlah FROM hrd.klaim WHERE klaimId IN $fidbr";
                    }
                    $query = "create TEMPORARY table $tmp01 ($query)"; 
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                    //simpan detail

                    $purutan=1;
                    $pkodeurutan=1;

                    
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

                            if ($purutan==30) {
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
            
            
            
        //end erni or prita
        }else{
            
            //$periodef, $periodet
            if ($pkode=="1" AND $psubkode=="03") {
                //$pkodeperiode (1/2)

            }elseif ($pkode=="2" AND $psubkode=="21") {
                //$pstspilihan (C/S/B)

            }
            
        }
        
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
