<?php

    session_start();
    $puserid="";
    if (isset($_SESSION['IDCARD'])) $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "data tidak ada yang diproses, silakan login ulang..."; exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    
$berhasil="Tidak ada data yang disimpan...";

$pmodule=$_GET['module'];
$pact=$_GET['act'];

if ($pmodule=="simpandanabpjs" AND $pact=="input") {
    
    $pdivisi="HO";
    $pttdgbr=$_POST['uttdimg'];
    $pperiode=$_POST['uperiode'];
    $pnodivisi=$_POST['unodiv'];
    $pmintadanarp=$_POST['ujmlrpminta'];
    $pmintadanarp=str_replace(",","", $pmintadanarp);
    $pkodeid="2";
    $psubkode="25";
    $pcoa4="750-03-003";//M-KESEHATAN HO
    if (!empty($pnodivisi)) {
        
        $query = "select idinput, tanggal, bulan, jumlah FROM dbmaster.t_spd_bpjs0 WHERE periode='$pperiode' AND IFNULL(stsnonaktif,'')<>'Y'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $row= mysqli_fetch_array($tampil);
            $pidinputada=$row['idinput'];
            $ptanggal=$row['tanggal'];
            $pbulan=$row['bulan'];
            $pjumlahrp=$row['jumlah'];
            if ((DOUBLE)$pjumlahrp==0) $pjumlahrp="";
            if (!empty($pidinputada)) {
                mysqli_close($cnmy); echo "$berhasil"; exit;
            }
            if (empty($pjumlahrp)) {
                mysqli_close($cnmy); echo "$berhasil"; exit;
            }

            $query = "select nodivisi FROM dbmaster.t_suratdana_br WHERE nodivisi='$pnodivisi' AND IFNULL(stsnonaktif,'')<>'Y' AND kodeid='$pkodeid' AND subkode='$psubkode'";
            $tampilk= mysqli_query($cnmy, $query);
            $ketemuk= mysqli_num_rows($tampilk);
            if ($ketemuk>0) {
                $berhasil = "No. Divisi : $pnodivisi Sudah ada, silakan edit nodivisi... <br/>data tidak ada yang disimpan.";
                mysqli_close($cnmy); echo "$berhasil"; exit;
            }
            
            
            $sql=  mysqli_query($cnmy, "select MAX(idinput) as NOURUT from dbmaster.t_suratdana_br");
            $ketemu=  mysqli_num_rows($sql);
            $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $urut=$o['NOURUT']+1;
                $kodenya=$urut;
            }else{
                $kodenya=$_POST['e_id'];
            }
            if (!empty($kodenya)) {
                
                $query = "INSERT INTO dbmaster.t_suratdana_br (karyawanid, idinput, divisi, kodeid, subkode, nomor, tgl, nodivisi, jumlah, keterangan, "
                        . " userid, tglinput, coa4, pilih, tglf, tglt, apv1, tgl_apv1, apv2, tgl_apv2, gbr_apv1) VALUES "
                        . " ('$puserid', '$kodenya', '$pdivisi', '$pkodeid', '$psubkode', '', '$ptanggal', '$pnodivisi', '$pmintadanarp', '', "
                        . " '$puserid', NOW(), '$pcoa4', 'Y', '$pbulan', '$pbulan', '$puserid', NOW(), '$puserid', NOW(), '$pttdgbr')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                
                $query = "UPDATE dbmaster.t_spd_bpjs0 SET idinput='$kodenya' WHERE periode='$pperiode' AND IFNULL(stsnonaktif,'')<>'Y' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); 
                if (!empty($erropesan)) {
                    $query = "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y' WHERE tglf='$pbulan' AND idinput='$kodenya' and kodeid='$pkodeid' AND subkode='$psubkode' LIMIT 1";
                    mysqli_query($cnmy, $query);
                    mysqli_close($cnmy); echo $erropesan; exit; 
                }

                $query = "UPDATE dbmaster.t_spd_bpjs SET idinput='$kodenya' WHERE periode='$pperiode'";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy);
                if (!empty($erropesan)) { 
                    $query = "UPDATE dbmaster.t_spd_bpjs0 SET stsnonaktif='Y' WHERE periode='$pperiode' AND idinput='$kodenya' LIMIT 1";
                    mysqli_query($cnmy, $query);
                    $query = "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y' WHERE tglf='$pbulan' AND idinput='$kodenya' and kodeid='$pkodeid' AND subkode='$psubkode' LIMIT 1";
                    mysqli_query($cnmy, $query);

                    mysqli_close($cnmy); echo $erropesan; exit; 
                }
                

                $berhasil= "permintaan dana BPJS, berhasil disimpan...";
            
            }
            
        }
        
    }
    
}

mysqli_close($cnmy);
echo "$berhasil";
?>

