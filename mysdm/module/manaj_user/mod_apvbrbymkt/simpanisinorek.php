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
    
    
    $berhasil="tidak ada no rekening yang disimpan...";
    
    //$berhasil = "$pmodule, $pact, $pidmenu";
    if ($pmodule=="approvebrquestbymkt" AND $pact=="simpanbrrealbymkt") {
        
        include "../../../config/koneksimysqli.php";
        
        $piddokt=$_POST['uiduser'];
        $pidbank=$_POST['uidbank'];
        $pidkcp=$_POST['ukcp'];
        $pnorekening=$_POST['unorek'];
        $pnorekatasnama=$_POST['uatasnama'];
        $psesuairek=$_POST['usesuai'];
        $pnamarelasi=$_POST['unmrelasi'];
        
        if ($psesuairek=="Y") $pnamarelasi="";
        
        if (!empty($piddokt)) {
        
            if (!empty($pidbank)) {
                $pidbank = str_replace("'", "", $pidbank);
                $pidbank = str_replace('"', "", $pidbank);
            }
        
            if (!empty($pidkcp)) {
                $pidkcp = str_replace("'", "", $pidkcp);
                $pidkcp = str_replace('"', "", $pidkcp);
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

            if (!empty($pnamarelasi)) {
                $pnamarelasi = str_replace("'", "", $pnamarelasi);
                $pnamarelasi = str_replace('"', "", $pnamarelasi);
            }
            
            
            $query_rek = "select * from hrd.dokter_norekening WHERE norekening='$pnorekening'";
            $tampilrek= mysqli_query($cnmy, $query_rek);
            $ketemu_rek=mysqli_num_rows($tampilrek);
            if ((INT)$ketemu_rek<=0) {
                $query = "INSERT INTO hrd.dokter_norekening(dokterid, idbank, kcp, norekening, atasnama, norek_sesuai, relasi_norek, inputby)VALUES"
                        . " ('$piddokt', '$pidbank', '$pidkcp', '$pnorekening', '$pnorekatasnama', '$psesuairek', '$pnamarelasi', '$pidcard')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "error insert norekening"; mysqli_close($cnmy); exit; }
            }else{
                mysqli_close($cnmy);
                echo "GAGAL... No Rekening tersebut sudah ada..."; exit;
            }
            
            
            mysqli_close($cnmy);
            
            //$berhasil="$piddokt, $pidspesial, $ptgllahir, $palamat, $pkota, $pnohape, $pnowea";
            $berhasil="berhasil";
            
            
        }else{
            $berhasil="ID KOSONG...";
        }
        
    }
    
    echo $berhasil;
?>