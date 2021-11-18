<?php
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_start();

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pidcard=$_SESSION['IDCARD'];
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    
    $berhasil="tidak ada data yang disimpan...";
    
    //$berhasil = "$pmodule, $pact, $pidmenu";
    if ($pmodule=="approvebrquestbymkt" AND $pact=="simpanbrrealbymkt") {
        
        include "../../../config/koneksimysqli.php";
        
        $piddokt=$_POST['uiduser'];
        $pidspesial=$_POST['uspesial'];
        $ptgllahir=$_POST['utgllahir'];
        $palamat=$_POST['ualamat'];
        $pkota=$_POST['ukota'];
        $pnohape=$_POST['unohp'];
        $pnowea=$_POST['unowa'];
        $pidbank=$_POST['uidbank'];
        $pnorekening=$_POST['unorek'];
        $pnorekatasnama=$_POST['uatasnama'];
        $pjekel=$_POST['ujkel'];
        
        if (!empty($piddokt)) {
        
            if (!empty($palamat)) {
                $palamat = str_replace("'", " ", $palamat);
                $palamat = str_replace('"', " ", $palamat);
            }

            if (!empty($pkota)) {
                $pkota = str_replace("'", " ", $pkota);
                $pkota = str_replace('"', " ", $pkota);
            }

            if (!empty($pnohape)) {
                $pnohape = str_replace("'", "", $pnohape);
                $pnohape = str_replace('"', "", $pnohape);
                $pnohape = str_replace('_', "", $pnohape);
            }

            if (!empty($pnowea)) {
                $pnowea = str_replace("'", "", $pnowea);
                $pnowea = str_replace('"', "", $pnowea);
                $pnowea = str_replace('_', "", $pnowea);
                $pnowea="+".TRIM($pnowea);
            }

            if (!empty($pidbank)) {
                $pidbank = str_replace("'", "", $pidbank);
                $pidbank = str_replace('"', "", $pidbank);
            }

            if (!empty($pnorekening)) {
                $pnorekening = str_replace("'", "", $pnorekening);
                $pnorekening = str_replace('"', "", $pnorekening);
                $pnorekening = str_replace('_', "", $pnorekening);
                $pnorekening = str_replace(' ', "", $pnorekening);
                $pnorekening=TRIM($pnorekening);
            }

            if (!empty($pnorekatasnama)) {
                $pnorekatasnama = str_replace("'", "", $pnorekatasnama);
                $pnorekatasnama = str_replace('"', "", $pnorekatasnama);
            }

            $query = "UPDATE hrd.dokter SET spid='$pidspesial', tgllahir='0000-00-00', alamat1='$palamat', kota='$pkota', hp='$pnohape', "
                    . " nowa='$pnowea', jekel='$pjekel', updateby='$pidcard', updatedate=NOW() where dokterid='$piddokt' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "error update user"; mysqli_close($cnmy); exit; }
            
            if (!empty($ptgllahir)) {
                $ptgllahir = str_replace('/', '-', $ptgllahir);
                $ptgllahir= date("Y-m-d", strtotime($ptgllahir));
                
                $query = "UPDATE hrd.dokter SET tgllahir='$ptgllahir', updateby='$pidcard', updatedate=NOW() where dokterid='$piddokt' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "error update tgl. lahir user"; mysqli_close($cnmy); exit; }
            
            }
            
            /*
            $query_rek = "select * from hrd.dokter_norekening WHERE norekening='$pnorekening'";
            $tampilrek= mysqli_query($cnmy, $query_rek);
            $ketemu_rek=mysqli_num_rows($tampilrek);
            if ((INT)$ketemu_rek<=0) {
                $query = "INSERT INTO hrd.dokter_norekening(dokterid, idbank, norekening, atasnama, inputby)VALUES"
                        . " ('$piddokt', '$pidbank', '$pnorekening', '$pnorekatasnama', '$pidcard')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "error insert norekening"; mysqli_close($cnmy); exit; }
            }
            */
            
            mysqli_close($cnmy);
            
            //$berhasil="$piddokt, $pidspesial, $ptgllahir, $palamat, $pkota, $pnohape, $pnowea";
            $berhasil="berhasil";
            
            
        }else{
            $berhasil="ID KOSONG...";
        }
        
    }
    
    echo $berhasil;
?>