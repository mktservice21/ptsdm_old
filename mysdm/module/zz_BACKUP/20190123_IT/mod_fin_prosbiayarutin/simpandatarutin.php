<?PHP
    session_start();
    
    $module=$_GET['module'];
    $notlert="Tidak ada data yang disimpan";
    if ($module=="simpandatanya") {
        include "../../config/koneksimysqli_it.php";
        $cnmy=$cnit;
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
            if ($pnoid=="04") $fieldsave=", qty='$pqty', rp='$prp' ";
            
            if (empty($pnourut)) {
                include "../../config/fungsi_sql.php";
                $pdivprodid = getfieldcnit("select divisi as lcfields from dbmaster.t_brrutin0 WHERE idrutin='$pidrutin'");
                if (empty($pdivprodid)) $pdivprodid="HO";
                $coadet = getfieldcnit("select COA4 as lcfields from dbmaster.posting_coa_rutin WHERE divisi='$pdivprodid' AND nobrid='$pnoid'");
                
                mysqli_query($cnmy, "DELETE FROM dbmaster.t_brrutin1 WHERE idrutin='$pidrutin' AND nobrid='$pnoid'");
                
                $query = "INSERT INTO dbmaster.t_brrutin1 (coa, idrutin, nobrid, rptotal, alasanedit_fin, qty, rp) values"
                        . " ('$coadet', '$pidrutin', '$pnoid', '$ptotalrp', '$palasan', '$pqty', '$ptotalrp')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $pnourut = getfieldcnit("select nourut as lcfields from dbmaster.t_brrutin1 where idrutin='$pidrutin' AND nobrid='$pnoid'");
            }
            
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
        
    }
    echo "$notlert";
?>