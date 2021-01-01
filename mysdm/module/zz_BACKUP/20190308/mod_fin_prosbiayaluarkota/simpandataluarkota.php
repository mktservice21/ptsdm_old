<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    $cnmy=$cnmy;
    $module=$_GET['module'];
    $notlert="Tidak ada data yang disimpan";
    if ($module=="simpandatanya") {
        $pidrutin=$_POST['uidrutin'];
        $pnourut=$_POST['unourut'];
        $pnoid=$_POST['uinoid'];
        $pqty=str_replace(",","", $_POST['uiqty']);
        $prp=str_replace(",","", $_POST['uirp']);
        $ptotalrp=str_replace(",","", $_POST['uitotal']);
        $palasan=$_POST['uialasan'];
        $psemua=str_replace(",","", $_POST['uitotsemua']);
        
        if (!empty($pidrutin) AND !empty($pnoid)) {
            
            $query="select idrutin from dbmaster.t_brrutin0_backup WHERE idrutin='$pidrutin'";
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
            if ($ketemu==0) {
                $query="INSERT INTO dbmaster.t_brrutin0_backup "
                        . " SELECT *, '$_SESSION[IDCARD]', NOW() from dbmaster.t_brrutin0 WHERE "
                        . " idrutin='$pidrutin'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
            
            $query="select idrutin from dbmaster.t_brrutin1_backup WHERE idrutin='$pidrutin'";
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
            if ($ketemu==0) {
                $query="INSERT INTO dbmaster.t_brrutin1_backup (nourut, idrutin, nobrid, deskripsi, qty, rp, rptotal, notes, coa, tgl1, tgl2)"
                        . " SELECT nourut, idrutin, nobrid, deskripsi, qty, rp, rptotal, notes, coa, tgl1, tgl2 from dbmaster.t_brrutin1 WHERE "
                        . " idrutin='$pidrutin'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }else{
                $query="select idrutin from dbmaster.t_brrutin1_backup WHERE idrutin='$pidrutin'";
                $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
                if ($ketemu==0) { echo "data tidak ada...."; exit; }
            }
            
            
            $fieldsave="";
            if ($pnoid=="25") $fieldsave=", qty='$pqty', rp='$prp' ";
            
            
            if (empty($pnourut)) {
                include "../../config/fungsi_sql.php";
                $pdivprodid = getfieldcnmy("select divisi as lcfields from dbmaster.t_brrutin0 WHERE idrutin='$pidrutin'");
                if (empty($pdivprodid)) $pdivprodid="HO";
                $coadet = getfieldcnmy("select COA4 as lcfields from dbmaster.posting_coa_rutin WHERE divisi='$pdivprodid' AND nobrid='$pnoid'");
                //echo "$coadet, $pidrutin, $pnoid, $ptotalrp, $palasan, $pqty, $ptotalrp, TOTAL : $psemua"; exit;
                mysqli_query($cnmy, "DELETE FROM dbmaster.t_brrutin1 WHERE idrutin='$pidrutin' AND nobrid='$pnoid'");
                
                $query = "INSERT INTO dbmaster.t_brrutin1 (coa, idrutin, nobrid, rptotal, alasanedit_fin, qty, rp) values"
                        . " ('$coadet', '$pidrutin', '$pnoid', '$ptotalrp', '$palasan', '$pqty', '$ptotalrp')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $pnourut = getfieldcnmy("select nourut as lcfields from dbmaster.t_brrutin1 where idrutin='$pidrutin' AND nobrid='$pnoid'");
            }
            //exit;
            
            $query = "UPDATE dbmaster.t_brrutin1 SET rptotal='$ptotalrp', alasanedit_fin='$palasan' $fieldsave WHERE idrutin='$pidrutin' AND nourut=$pnourut AND nobrid='$pnoid'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $query = "UPDATE dbmaster.t_brrutin0 SET jumlah='$psemua' WHERE idrutin='$pidrutin' ";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            
            //update date time user yang update
            $query = "UPDATE dbmaster.t_brrutin0_backup SET userid_fin_edit='$_SESSION[IDCARD]', userid_fin_date=NOW() WHERE idrutin='$pidrutin' ";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            $notlert="Berhasil";
        }
        
    }elseif ($module=="simpandataperiode") {
        $pidrutin=$_POST['uidrutin'];
        if (!empty($pidrutin)) {
            $ptgl1=$_POST['utgl1'];
            $pmon1=$_POST['umon1'];
            $pyear1=$_POST['uyear1'];
            
            $ptgl2=$_POST['utgl2'];
            $pmon2=$_POST['umon2'];
            $pyear2=$_POST['uyear2'];
            
            $ptglall1=$pyear1."-".$pmon1."-".$ptgl1;
            $ptglall2=$pyear2."-".$pmon2."-".$ptgl2;
            
            
            $query="select idrutin from dbmaster.t_brrutin0_backup WHERE idrutin='$pidrutin'";
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
            if ($ketemu==0) {
                $query="INSERT INTO dbmaster.t_brrutin0_backup "
                        . " SELECT *, '$_SESSION[IDCARD]', NOW() from dbmaster.t_brrutin0 WHERE "
                        . " idrutin='$pidrutin'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
            
            $query="select idrutin from dbmaster.t_brrutin1_backup WHERE idrutin='$pidrutin'";
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
            if ($ketemu==0) {
                $query="INSERT INTO dbmaster.t_brrutin1_backup (nourut, idrutin, nobrid, deskripsi, qty, rp, rptotal, notes, coa, tgl1, tgl2)"
                        . " SELECT nourut, idrutin, nobrid, deskripsi, qty, rp, rptotal, notes, coa, tgl1, tgl2 from dbmaster.t_brrutin1 WHERE "
                        . " idrutin='$pidrutin'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }else{
                $query="select idrutin from dbmaster.t_brrutin1_backup WHERE idrutin='$pidrutin'";
                $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
                if ($ketemu==0) { echo "data tidak ada...."; exit; }
            }
            
            $query = "UPDATE dbmaster.t_brrutin0 SET bulan='$ptglall1', periode1='$ptglall1', periode2='$ptglall2' WHERE idrutin='$pidrutin' ";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $notlert="Berhasil disimpan..";
            
            
        }
        
    }
    echo "$notlert";
?>