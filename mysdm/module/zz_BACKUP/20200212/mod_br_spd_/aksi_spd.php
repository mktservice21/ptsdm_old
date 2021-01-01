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
    //mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br1 WHERE idinput='$_GET[id]'");
    //mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br_d WHERE idinput='$_GET[id]'");
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='saldosuratdana')
{
    $kodenya=$_POST['e_id'];
    
    $pdivisi=$_POST['cb_divisi'];
    $pkode=$_POST['cb_kode'];
    $psubkode=$_POST['cb_kodesub'];
    
    $ptgl = $_POST['e_tglberlaku'];
    $periode1= date("Y-m-d", strtotime($ptgl));
    
    $padvance=$_POST['cb_jenispilih'];//jenis
    
    $pnomor=$_POST['e_nomor'];
    $pdivno=$_POST['e_nomordiv'];
    $pjenis=$_POST['cb_jenis'];//lampiran
    
    $ppertipe=$_POST['cb_pertipe'];//periode by
    $pmytgl = $_POST['e_periode1'];
    $pmytg2 = $_POST['e_periode2'];
    
    $periodef = date("Y-m-d", strtotime($pmytgl));
    $periodet = date("Y-m-d", strtotime($pmytg2));
    
    
    $pjumlah=str_replace(",","", $_POST['e_jmlusulan']);
    
    $keterangan=$_POST['e_keterangan'];
    if (!empty($keterangan)) $keterangan = str_replace("'", " ", $keterangan);
    
    
    $pnomorspd_adj="";
    $pdivno_adj="";
    if ($padvance=="J") {//adjusment
        $pdivno="";
        $pdivno_adj=$_POST['cb_ajsnobr'];
        $pjenis="";//lampiran
        $ppertipe="I";
        $periodef=$periode1;
        $periodet=$periode1;
        
        $edit = mysqli_query($cnmy, "SELECT nomor FROM dbmaster.t_suratdana_br WHERE nodivisi='$pdivno_adj' AND IFNULL(stsnonaktif,'')<>'Y'");
        $r    = mysqli_fetch_array($edit);
        
        if (empty($pnospd)) {//jika kosong maka cari nomor spd sesuai  no br / divisi
            $pnomorspd_adj=$r['nomor'];
        }
        
    }
    
    $pmystsyginput="";
    if ($_SESSION['IDCARD']=="0000000566") {
        $pmystsyginput=1;
    }elseif ($_SESSION['IDCARD']=="0000001043") {
        $pmystsyginput=2;
    }
    
    if (empty($pmystsyginput)) exit;
    
    
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
    }
    
    
    if (empty($pkode)) {
        $pkode="1";
        $psubkode="01";
        if ($padvance=="K" OR $padvance=="B") {
            $pkode="2";
            $psubkode="20";
        }elseif ($padvance=="S") {
            $pkode="6";
            $psubkode="80";
        }elseif ($padvance=="J") {
            $pkode="3";
            $psubkode="50";
        }
    }
    
    //echo "$kodenya, $pdivisi, $pkode, $psubkode, tgl : $periode1, jenis : $padvance, $pnomor, no adj : $pnomorspd_adj, nodiv : $pdivno, nodiv adj : $pdivno_adj.., $pjenis, $ppertipe : $periodef - $periodet, jml : $pjumlah, ket : $keterangan"; exit;
    
    if (empty($kodenya)) exit;
    
    $pcoa="101-02-002";
    if ($act=="input") {
        
        $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, tgl, nodivisi, jumlah, "
                . " userid, coa4, lampiran, tglf, tglt, karyawanid, periodeby, jenis_rpt, keterangan, nodivisi2, nomor2)values"
                . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$periode1', '$pdivno', '$pjumlah', "
                . " '$_SESSION[IDCARD]', '$pcoa', '$pjenis', '$periodef', '$periodet', '$_SESSION[IDCARD]', '$ppertipe', '$padvance', '$keterangan', '$pdivno_adj', '$pnomorspd_adj')";
        
    }else{
        $query = "UPDATE $dbname.t_suratdana_br SET lampiran='$pjenis', coa4='$pcoa', divisi='$pdivisi', kodeid='$pkode', "
                . " subkode='$psubkode', tgl='$periode1', nodivisi='$pdivno', jumlah='$pjumlah', userid='$_SESSION[IDCARD]', "
                . " tglf='$periodef', tglt='$periodet', periodeby='$ppertipe', jenis_rpt='$padvance', "
                . " keterangan='$keterangan', nodivisi2='$pdivno_adj', nomor2='$pnomorspd_adj' WHERE "
                . " idinput='$kodenya'";
    }
    //echo $query; exit;
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    //belum ada kwitansi (CA) atau via surabaya diskon/br
    
    if ($padvance=="B" OR $padvance=="V" OR $padvance=="C") {
        $query = "UPDATE $dbname.t_suratdana_br SET pilih='N' WHERE idinput='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    
    $query = "DELETE FROM $dbname.t_suratdana_br1 WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "DELETE FROM $dbname.t_suratdana_br_d WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
        
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.IDSTHHSR01_".$userid."_$now ";
    
    $purutan=1;
    $pkodeurutan=1;
    
    $ntot_adj_rp=0;
    
    if ($padvance!="J") {
    
        
        $fidbr="";
        foreach ($_POST['chk_jml1'] as $nobrinput) {
            if (!empty($nobrinput) AND $nobrinput <> "0") {
                $fidbr=$fidbr."'".$nobrinput."',";
            }
        }
        
        
        if (!empty($fidbr)) {
            $fidbr=substr($fidbr, 0, -1);
            $fidbr="(".$fidbr.")";        

            if (!empty($kodenya)){

                $kodeinput="A";//KODE BR ERNI
                if ($pmystsyginput==2) $kodeinput="B";//KODE BR PRITA

                $query = "SELECT brId, divprodid divisi, jumlah, jumlah1 FROM hrd.br0 WHERE brId IN $fidbr";
                if ($pmystsyginput==2 AND ($padvance=="D" OR  $padvance=="C")) {//C=via surabaya klaim disc
                    $kodeinput="E";
                    $query = "SELECT klaimId brId, DIVISI divisi, jumlah, jumlah as jumlah1 FROM hrd.klaim WHERE klaimId IN $fidbr";
                }
                $query = "create TEMPORARY table $tmp01 ($query)"; 
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_query($cnmy, "drop TEMPORARY table $tmp01"); exit; }

                if ($_SESSION['IDCARD']=="0000000566" OR $_SESSION['IDCARD']=="0000001043") {
                    if ($padvance=="A"){
                    }else{
                        mysqli_query($cnmy, "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)>0");
                    }
                }


                //save input ke table
                foreach ($_POST['chk_jml1'] as $no_brid) {
                    if (!empty($no_brid)) {
                        $no_urutbr = $_POST['cb_urut'][$no_brid];
                        if (empty($no_urutbr)) $no_urutbr="0";

                        $ptrans_ke="";
                        if (isset($_POST['chk_transke'][$no_brid])) $ptrans_ke=$_POST['chk_transke'][$no_brid];
                        
                        $pket_adj_pilih="";
                        $pjml_adj_pilih=0;
                        $pilihchk_adj="";
                        if (isset($_POST['chk_adj'][$no_brid])) {
                            $pilihchk_adj=$_POST['chk_adj'][$no_brid];
                            if (!empty($pilihchk_adj)) {
                                
                                if (isset($_POST['txt_adj_ket'][$no_brid])) $pket_adj_pilih=$_POST['txt_adj_ket'][$no_brid];
                                if (!empty($pket_adj_pilih)) $pket_adj_pilih = str_replace("'", " ", $pket_adj_pilih);
                                
                                
                                if (isset($_POST['txt_adj'][$no_brid])) $pjml_adj_pilih=$_POST['txt_adj'][$no_brid];
                                if (!empty($pjml_adj_pilih)) $pjml_adj_pilih=str_replace(",","", $pjml_adj_pilih);
                                
                            }
                        }
                        
                        
                        //eksekusi input
                        $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan, amount, trans_ke)"
                                . " SELECT '$kodenya' as idinput, brId, '$kodeinput' as kodeinput, "
                                . " '$no_urutbr' as urutan, jumlah, '$ptrans_ke' as trans_ke from $tmp01 WHERE brId='$no_brid'";
                        mysqli_query($cnmy, $query);
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_query($cnmy, "drop TEMPORARY table $tmp01"); exit; }
                        
                        if (empty($pjml_adj_pilih)) $pjml_adj_pilih=0;
                        $ntot_adj_rp=(double)$ntot_adj_rp+(double)$pjml_adj_pilih;
                        
                        $query = "UPDATE $dbname.t_suratdana_br1 SET jml_adj='$pjml_adj_pilih' WHERE bridinput='$no_brid' AND idinput='$kodenya'";
                        mysqli_query($cnmy, $query);
                        
                        $query = "UPDATE $dbname.t_suratdana_br1 SET aktivitas1='$pket_adj_pilih' WHERE bridinput='$no_brid' AND idinput='$kodenya'";
                        mysqli_query($cnmy, $query);
                        
                        
                    }
                }


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
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; mysqli_query($cnmy, "drop TEMPORARY table $tmp01"); exit; }


                    }
                }

                mysqli_query($cnmy, "drop TEMPORARY table $tmp01");

            }


        }
            
            
        
    }else{
        
        $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput,divisi,jumlah)values('$kodenya', '$pdivisi', '$pjumlah')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput,urutan,amount)values('$kodenya', 1, '$pjumlah')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    
    
    
    $nomordivisiadj="";
    if(isset($_POST['cb_ajsnobr2'])) $nomordivisiadj=$_POST['cb_ajsnobr2'];
    if (!empty($nomordivisiadj)) {

        $query = "UPDATE $dbname.t_suratdana_br SET nodivisi2='$nomordivisiadj' WHERE idinput='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    }
        
    
    
    if (empty($ntot_adj_rp)) $ntot_adj_rp=0;
    $query = "UPDATE $dbname.t_suratdana_br SET jumlah2='$ntot_adj_rp' WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
}
    
    
?>

