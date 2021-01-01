<?PHP
    session_start();
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
    
    $pmodule=$_GET['module'];
    $notlert="Tidak ada data yang disimpan";
    if ($pmodule=="simpandatanya") {
        
        
        $pidkascab=$_POST['uidkascab'];
        $pnoidkode=$_POST['uinoid'];
        $pnoidcoa=$_POST['ucoa'];
        $prp=$_POST['urp'];
        $pjumlah=$_POST['ujml'];
        $psaldo=$_POST['usaldo'];
        $pnotes=$_POST['unotes'];
        $pidcard=$_POST['uuserid'];
        if (empty($pidcard)) $pidcard=$puserid;
        
        
        if (empty($prp)) $prp=0;
        if (empty($pjumlah)) $pjumlah=0;
        if (empty($psaldo)) $psaldo=0;
        
        if (empty($pidcard)) {
            echo "Anda harus login ulang....!!!"; exit;
        }
        if ((DOUBLE)$pjumlah<0) {
            echo "Jumlah Kosong...!!!"; exit;
        }
        
        
        include "../../config/koneksimysqli.php";
        if (!empty($pidkascab) AND !empty($pnoidkode) AND !empty($pnoidcoa)) {
            
            $prp=str_replace(",","", $prp);
            $pjumlah=str_replace(",","", $pjumlah);
            $psaldo=str_replace(",","", $psaldo);
            if (!empty($pnotes)) $pnotes = str_replace("'", " ", $pnotes);
            
            $query = "DELETE FROM dbmaster.t_kaskecilcabang_d WHERE idkascab='$pidkascab' AND kode='$pnoidkode' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan.""; exit; }
            
            if ((DOUBLE)$prp==0 AND empty($pnotes)) {
            }else{
                $query = "INSERT INTO dbmaster.t_kaskecilcabang_d (idkascab, kode, jumlahrp, notes, tglpilih, coa4, useridd)VALUES"
                        . "('$pidkascab', '$pnoidkode', '$prp', '$pnotes', CURRENT_DATE(), '$pnoidcoa', '$pidcard')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan.""; exit; }
            }
            
            $query = "select sum(jumlahrp) as jumlahrp FROM dbmaster.t_kaskecilcabang_d WHERE idkascab='$pidkascab'";
            $tampil= mysqli_query($cnmy, $query);
            $row= mysqli_fetch_array($tampil);
            $pjumltot=$row['jumlahrp'];
            if (empty($pjumltot)) $pjumltot=0;
            //$pjumltot=$pjumlah;
            
            $query = "UPDATE dbmaster.t_kaskecilcabang SET jumlah='$pjumltot', userid='$pidcard' WHERE idkascab='$pidkascab' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan.""; exit; }
            
            $notlert="Berhasil";
            //$notlert="$pidkascab, $pidcard, $prp, $pjumlah, $psaldo";
        }
        
        mysqli_close($cnmy);
    }elseif ($pmodule=="simpanrealnorekdatanya") {
        
        $pidkascab=$_POST['uidkascab'];
        $pnmreal=$_POST['ureal'];
        $pnorek=$_POST['unorek'];
        $pidcard=$_POST['uuserid'];
        $pbulan=$_POST['ubln'];
        if (!empty($pbulan)) $pbulan= date("Y-m-d", strtotime($pbulan));
        
        if (empty($pidcard)) $pidcard=$puserid;
        
        if (empty($pidcard)) {
            echo "Anda harus login ulang....!!!"; exit;
        }
        
        include "../../config/koneksimysqli.php";
        if (!empty($pidkascab)) {
            
            if (!empty($pnmreal)) $pnmreal = str_replace("'", " ", $pnmreal);
            if (!empty($pnorek)) $pnorek = str_replace("'", " ", $pnorek);
            
            $query = "UPDATE dbmaster.t_kaskecilcabang SET nmrealisasi='$pnmreal', norekening='$pnorek', userid='$pidcard' WHERE idkascab='$pidkascab' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan.""; exit; }
            
            if (!empty($pbulan)) {
                $query = "UPDATE dbmaster.t_kaskecilcabang SET bulan='$pbulan' WHERE idkascab='$pidkascab' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan.""; exit; }
            }
            
            $notlert="Berhasil";
            //$notlert="$pnmreal, $pnorek, $pidcard";
        }
        
        mysqli_close($cnmy);
        
    }
    
    echo $notlert;
?>