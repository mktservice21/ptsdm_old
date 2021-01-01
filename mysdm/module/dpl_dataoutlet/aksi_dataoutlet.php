<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
$pidcard="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

if ($module=='dpldataoutlet')
{
    if ($act=="hapus") {
        
        if (empty($pidcard)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        include "../../config/koneksimysqli.php";
        
        
        
        mysqli_close($cnmy);
        
        //header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahhapus');
        exit;
        
    }elseif ($act=="input" OR $act=="update") {
        $pkodeid=$_POST['e_id'];
        $pidcardinput=$_POST['e_userinput'];
        if (empty($pidcardinput)) $pidcardinput=$pidcard;
        
        if (empty($pidcardinput)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        include "../../config/koneksimysqli.php";
        
        $pidsektor=$_POST['cb_sektorid'];
        $pnamaoutlet=$_POST['e_nmoutlet'];
        $palamat=$_POST['e_alamat'];
        $pprovinsi=$_POST['e_provinsi'];
        $pkota=$_POST['e_kota'];
        $pkdpos=$_POST['e_kdpos'];
        $ptelp=$_POST['e_telp'];
        $pnohp=$_POST['e_nohp'];
        $pkeyperson=$_POST['e_keyperson'];
        $pnotes=$_POST['e_notes'];
        
        if (!empty($pnamaoutlet)) $pnamaoutlet = str_replace("'", ' ', $pnamaoutlet);
        if (!empty($palamat)) $palamat = str_replace("'", ' ', $palamat);
        if (!empty($pprovinsi)) $pprovinsi = str_replace("'", ' ', $pprovinsi);
        if (!empty($pkota)) $pkota = str_replace("'", ' ', $pkota);
        if (!empty($pkdpos)) $pkdpos = str_replace("'", ' ', $pkdpos);
        if (!empty($ptelp)) $ptelp = str_replace("'", ' ', $ptelp);
        if (!empty($pnohp)) $pnohp = str_replace("'", ' ', $pnohp);
        if (!empty($pkeyperson)) $pkeyperson = str_replace("'", ' ', $pkeyperson);
        if (!empty($pnotes)) $pnotes = str_replace("'", ' ', $pnotes);
        
        
        if ($act=="input") {
            $pkodeid="";
            $query_mst="INSERT INTO dbdpl.t_outlet (isektorid, nama_outlet, userid)VALUES"
                    . "('$pidsektor', '$pnamaoutlet', '$pidcardinput')";
            $pinsertmaster = mysqli_query($cnmy, $query_mst);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            $pkodeid = mysqli_insert_id($cnmy);
        }
        
        
        
        $psimpandata=false;
        unset($pinsert_data_detail);//kosongkan array
        if (!empty($pkodeid)) {
            
            
            $query = "UPDATE dbdpl.t_outlet SET isektorid='$pidsektor', nama_outlet='$pnamaoutlet', "
                    . " alamat='$palamat', provinsi='$pprovinsi', kota='$pkota', "
                    . " kodepos='$pkdpos', telp='$ptelp', hp='$pnohp', keyperson='$pkeyperson', "
                    . " notes='$pnotes', "
                    . " userid='$pidcardinput' WHERE idoutlet='$pkodeid' LIMIT 1";
            $pinsertmaster = mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
            
            
            
            $psimpandata=false;
            foreach ($_POST['chkbox_br'] as $piddist) {
                $pdiscount=$_POST['txt_disc'][$piddist];
                $pket=$_POST['txt_ket'][$piddist];

                if (!empty($pdiscount)) $pdiscount = str_replace("'", '', $pdiscount);
                if (!empty($pket)) $pket = str_replace("'", ' ', $pket);
                
                $pdiscount=str_replace(",","", $pdiscount);
                
                $pinsert_data_detail[] = "('$pkodeid', '$piddist', '$pdiscount', '$pket')";
                $psimpandata=true;


            }
            
            if ($psimpandata==true) {
                $query_delete_detail="DELETE FROM dbdpl.t_outlet_d WHERE idoutlet='$pkodeid'";
                $pdeletedetail = mysqli_query($cnmy, $query_delete_detail);
                $erropesan = mysqli_error($cnmy); 
                if (!empty($erropesan)) { 
                    echo $erropesan;  mysqli_close($cnmy); exit;     
                }
                
                $query_detail="INSERT INTO dbdpl.t_outlet_d (idoutlet, distid, discount, keterangan) VALUES ".implode(', ', $pinsert_data_detail);
                $pinsertdetail = mysqli_query($cnmy, $query_detail);
                $erropesan = mysqli_error($cnmy); 
                if (!empty($erropesan)) { 
                    echo $erropesan; 
                    mysqli_close($cnmy); exit;     
                }
                
            }
            
            
            
            
        }
        
        
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
        
    }
    
    
    
}



?>
