<?PHP
    session_start();
    
    $module=$_GET['module'];
    $notlert="Tidak ada data yang disimpan";
    if ($module=="simpandatanya") {
        include "../../config/koneksimysqli.php";
        $cnmy=$cnmy;
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
            
            //
            $psavekm="";
            $psavetglkw="";
            $psavetglkw2="";
            $psaveuntuk="";
            
            if ($pnoid=="01") {
                $pkm=str_replace(",","", $_POST['ukm']);
                if (empty($pkm)) $pkm=0;
                $psavekm=", km='$pkm' ";
            }
            
            if ($pnoid=="10" OR $pnoid=="11" OR $pnoid=="16" OR $pnoid=="17" OR $pnoid=="18" OR $pnoid=="19" OR $pnoid=="12") {
                $ptglkw=$_POST['utglkw'];
                if (!empty($ptglkw)){
                    $ptglkw =  date("Y-m-d", strtotime($ptglkw));
                    $psavetglkw=", tgl1='$ptglkw' ";
                }
                
                $ptglkw2=$_POST['utglkw2'];
                if (!empty($ptglkw2)){
                    $ptglkw2 =  date("Y-m-d", strtotime($ptglkw2));
                    $psavetglkw2=", tgl2='$ptglkw2' ";
                }
                
            }
            
            if ($pnoid=="11") {
                $puntuk=$_POST['uuntuk'];
                $psaveuntuk=", obat_untuk='$puntuk' ";
            }
            
            
            //
            
            
            if (empty($pnourut)) {
                include "../../config/fungsi_sql.php";
                $pdivprodid = getfieldcnmy("select divisi as lcfields from dbmaster.t_brrutin0 WHERE idrutin='$pidrutin'");
                if (empty($pdivprodid)) $pdivprodid="HO";
                $coadet = getfieldcnmy("select COA4 as lcfields from dbmaster.posting_coa_rutin WHERE divisi='$pdivprodid' AND nobrid='$pnoid'");
                
                mysqli_query($cnmy, "DELETE FROM dbmaster.t_brrutin1 WHERE idrutin='$pidrutin' AND nobrid='$pnoid'");
                
                $query = "INSERT INTO dbmaster.t_brrutin1 (coa, idrutin, nobrid, rptotal, alasanedit_fin, qty, rp) values"
                        . " ('$coadet', '$pidrutin', '$pnoid', '$ptotalrp', '$palasan', '$pqty', '$ptotalrp')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $pnourut = getfieldcnmy("select nourut as lcfields from dbmaster.t_brrutin1 where idrutin='$pidrutin' AND nobrid='$pnoid'");
            }
            
            $query = "UPDATE dbmaster.t_brrutin1 SET rptotal='$ptotalrp', alasanedit_fin='$palasan' $fieldsave $psavekm $psavetglkw $psaveuntuk $psavetglkw2 WHERE idrutin='$pidrutin' AND nourut=$pnourut AND nobrid='$pnoid'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            
            $tampilkan=  mysqli_query($cnmy, "select sum(rptotal) rptotal from dbmaster.t_brrutin1 WHERE idrutin='$pidrutin'");
            $nketemu= mysqli_num_rows($tampilkan);
            $pintot=0;
            if ($nketemu>0) {
                $ntot= mysqli_fetch_array($tampilkan);
                $pintot=$ntot['rptotal'];
            }
            if ((double)$pintot>0) {
                $psemua=$pintot;
            }
        
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