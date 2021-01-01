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
        include "../../config/fungsi_sql.php";
        
        $pidsektor=$_POST['cb_sektorid'];
        $pnamaoutlet=$_POST['e_nmoutlet'];
        $palamat=$_POST['e_alamat'];
        $pprovinsi=$_POST['e_provinsi'];
        $pkota=$_POST['e_kota'];
        $pkdpos=$_POST['e_kdpos'];
        $ptelp=$_POST['e_telp'];
        $pkontakperson=$_POST['e_kontakperson'];
        $pnotes=$_POST['e_notes'];
        
        $pperiode="2021";
        $pidsem=$_POST['cb_semester'];
        $pidcab=$_POST['cb_cabangid'];
        $pidarea=$_POST['cb_areaid'];
        
        $pidcust="";
        if (isset($_POST['cb_custid'])) $pidcust=$_POST['cb_custid'];
        
        
        $pnodpl=$_POST['e_nodpl'];
        
        $palldiscount=$_POST['e_discount'];
        $pallbonus=$_POST['e_sdmbonus'];
        $pdiscsdm=$_POST['e_sdmdiscount'];
        
        if (!empty($pnamaoutlet)) $pnamaoutlet = str_replace("'", ' ', $pnamaoutlet);
        if (!empty($palamat)) $palamat = str_replace("'", ' ', $palamat);
        if (!empty($pprovinsi)) $pprovinsi = str_replace("'", ' ', $pprovinsi);
        if (!empty($pkota)) $pkota = str_replace("'", ' ', $pkota);
        if (!empty($pkdpos)) $pkdpos = str_replace("'", ' ', $pkdpos);
        if (!empty($ptelp)) $ptelp = str_replace("'", ' ', $ptelp);
        if (!empty($pkontakperson)) $pkontakperson = str_replace("'", ' ', $pkontakperson);
        if (!empty($pnotes)) $pnotes = str_replace("'", ' ', $pnotes);
        if (!empty($pnodpl)) $pnodpl = str_replace("'", ' ', $pnodpl);
        
        if (empty($palldiscount)) $palldiscount=0;
        $palldiscount=str_replace(",","", $palldiscount);
        
        if (empty($pallbonus)) $pallbonus=0;
        //$pallbonus=str_replace(",","", $pallbonus);
        
        if (empty($pdiscsdm)) $pdiscsdm=0;
        $pdiscsdm=str_replace(",","", $pdiscsdm);
        
        $pgroupid=$_POST['e_grpid'];
        $ppilihcust=$_POST['cb_custpilih'];
        $padacustmulti=false;
        $fcustid="";
        $pmulticustid="";
        if ($pgroupid=="0") $pgroupid="";
        
        $fkodeotldpl="";
        $fkodeotldpldelete_d="";
        $pbolehsimpan=false;
        unset($pinsert_data_detail);//kosongkan array
                
        if ($ppilihcust=="M") {
            $pidcust="";
            
            if (isset($_POST['chkbox_custid'])) {
                $padacustmulti=true;
                $pmulticustid=$_POST['chkbox_custid'];
            }

            
            if ($padacustmulti==true) {
                
                if ($act=="input" OR empty($pgroupid)) {
                    
                    $query = "select MAX(igroup) as urutan FROM dbdiscount.t_outlet_dpl";
                    $tampil=mysqli_query($cnmy, $query);
                    $ketemu= mysqli_num_rows($tampil);
                    if ((INT)$ketemu>0) {
                        $nrow= mysqli_fetch_array($tampil);
                        if (isset($nrow['urutan'])) $pgroupid=$nrow['urutan'];
                        $pgroupid++;
                    }else{
                        $pgroupid=1;
                    }
                    
                    //hanya buat jaga-jaga
                    $query = "select igroup as urutan FROM dbdiscount.t_outlet_dpl WHERE igroup='$pgroupid' LIMIT 1";
                    $tampil2=mysqli_query($cnmy, $query);
                    $ketemu2= mysqli_num_rows($tampil2);
                    if ((INT)$ketemu2>0) {
                        echo "ID GROUP SUDAH ADA...";
                        mysqli_close($cnmy); exit;
                    }
                    
                    if ($act=="input") {
                    }else{
                        //jiga update dan igroup awal kosong...
                        $query = "UPDATE dbdiscount.t_outlet_dpl SET igroup='$pgroupid', userid='$pidcardinput' WHERE idoutlet_dpl='$pkodeid' LIMIT 1";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    }
                }
                
                
                if ($act=="input") {
                }else{
                    $pjmldatarec=0;
                    $query = "select igroup, idoutlet_dpl FROM dbdiscount.t_outlet_dpl WHERE IFNULL(igroup,'0')='$pgroupid' AND IFNULL(igroup,'')<>''";
                    $tampil3=mysqli_query($cnmy, $query);
                    while ($nr= mysqli_fetch_array($tampil3)) {
                        $pkodeid=$nr['idoutlet_dpl'];
                        
                        if (!empty($pkodeid)) {
                            $fkodeotldpldelete_d .="'".$pkodeid."',";
                        }
                        $pjmldatarec++;
                    }
                    
                    //$pjmldatarec= getfieldcnmy("select count(*) as lcfields FROM dbdiscount.t_outlet_dpl WHERE IFNULL(igroup,0)='$pgroupid' AND IFNULL(igroup,'')<>''");
                    if (empty($pjmldatarec)) $pjmldatarec=0;
                    
                    //echo $pjmldatarec; mysqli_close($cnmy); exit;
                    
                    if (!empty($pgroupid)) {
                        $query="DELETE FROM dbdiscount.t_outlet_dpl WHERE IFNULL(igroup,0)='$pgroupid' AND IFNULL(igroup,'')<>'' AND userid='$pidcardinput' LIMIT $pjmldatarec";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan;  mysqli_close($cnmy); exit; }
                    }
                    
                }
                    
                foreach ($pmulticustid as $pcustidpl) {
                    if (!empty($pcustidpl)) {
                        
                        $pidcab="";
                        $pidarea="";
                        $ppilihcustid="";
                        
                        $ppilihcustexp=explode("|",$pcustidpl);
                        
                        if (isset($ppilihcustexp[0])) $pidcab=$ppilihcustexp[0];
                        if (isset($ppilihcustexp[1])) $pidarea=$ppilihcustexp[1];
                        if (isset($ppilihcustexp[2])) $ppilihcustid=$ppilihcustexp[2];
                        
                        
                        
                        //echo "$pidcab, $pidarea, $ppilihcustid, all id : $pcustidpl<br/>";
                        
                        if (!empty($pidcab) AND !empty($pidarea) AND !empty($pcustidpl)) {
                        
                            //if ($act=="input") {
                                $pinsert_data_detail[] = "('$pgroupid', '$pidsektor', '$pnamaoutlet', '$pidcardinput', "
                                        . " '$pidcab', '$pidarea', '$ppilihcustid', '$pperiode', '$pidsem', '$pnodpl', '$palldiscount', '$pallbonus', "
                                        . " '$palamat', '$pprovinsi', '$pkota', '$pkdpos', '$ptelp', '$pkontakperson', '$pnotes')";

                                $pbolehsimpan=true;
                            //}
                            
                        }
                        
                    }
                }
                
                
            }
            
            
            if ($pbolehsimpan == true) {
                //$query = "ALTER TABLE dbdiscount.t_outlet_dpl AUTO_INCREMENT = 1";
                //mysqli_query($cnmy, $query);
                
                $query_detail="INSERT INTO dbdiscount.t_outlet_dpl (igroup, isektorid, nama_outlet, userid, icabangid, areaid, "
                        . " icustid, periode, semester, nodpl, discount, bonus, "
                        . " alamat, provinsi, kota, kodepos, telp, kontakperson, notes) "
                        . " VALUES ".implode(', ', $pinsert_data_detail);
                mysqli_query($cnmy, $query_detail); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan." INSERT "; mysqli_close($cnmy); exit; }
                
                
                unset($pinsert_data_detail_d);//kosongkan array
                $psimpandata_d=false;
                foreach ($_POST['chk_kodeid'] as $piddist) {
                    $pdiscount=$_POST['e_jmldisc'][$piddist];
                    $pket=$pnotes;//$_POST['txt_ket'][$piddist];

                    if (!empty($pdiscount)) $pdiscount = str_replace("'", '', $pdiscount);
                    if (!empty($pket)) $pket = str_replace("'", ' ', $pket);

                    $pdiscount=str_replace(",","", $pdiscount);
                    
                    
                    $query = "select igroup, idoutlet_dpl FROM dbdiscount.t_outlet_dpl WHERE IFNULL(igroup,'0')='$pgroupid' AND IFNULL(igroup,'')<>''";
                    $tampil3=mysqli_query($cnmy, $query);
                    while ($nr= mysqli_fetch_array($tampil3)) {
                        $pkodeid=$nr['idoutlet_dpl'];
                        
                        if (!empty($pkodeid)) {
                            $fkodeotldpl .="'".$pkodeid."',";
                            $fkodeotldpldelete_d .="'".$pkodeid."',";
                            
                            $pinsert_data_detail_d[] = "('$pkodeid', '$piddist', '$pdiscount', '$pket')";
                            $psimpandata_d=true;
                        }
                    }
                    
                    
                }
                
                
                if (!empty($fkodeotldpl)) $fkodeotldpl="(".substr($fkodeotldpl, 0, -1).")";
                if (!empty($fkodeotldpldelete_d)) $fkodeotldpldelete_d="(".substr($fkodeotldpldelete_d, 0, -1).")";

                if ($psimpandata_d == true AND !empty($fkodeotldpl)) {
                    if ($act=="input") {
                        
                    }else{
                        
                        $pjmldatarec= getfieldcnmy("select count(*) as lcfields FROM dbdiscount.t_outlet_dpl_d WHERE idoutlet_dpl IN $fkodeotldpldelete_d");
                        if (empty($pjmldatarec)) $pjmldatarec=0;
                    
                        $query_delete_detail="DELETE FROM dbdiscount.t_outlet_dpl_d WHERE idoutlet_dpl IN $fkodeotldpldelete_d LIMIT $pjmldatarec";
                        $pdeletedetail = mysqli_query($cnmy, $query_delete_detail);
                        $erropesan = mysqli_error($cnmy); 
                        if (!empty($erropesan)) { 
                            echo $erropesan;  mysqli_close($cnmy); exit;     
                        }
                    }

                    //$query = "ALTER TABLE dbdiscount.t_outlet_dpl_d AUTO_INCREMENT = 1";
                    //mysqli_query($cnmy, $query);

                    $query_detail="INSERT INTO dbdiscount.t_outlet_dpl_d (idoutlet_dpl, distid, disc, keterangan) "
                            . " VALUES ".implode(', ', $pinsert_data_detail_d);
                    $pinsertdetail = mysqli_query($cnmy, $query_detail);
                    $erropesan = mysqli_error($cnmy); 
                    if (!empty($erropesan)) { 
                        echo $erropesan; 
                        mysqli_close($cnmy); exit;  
                    }


                }
                
                
                $pket=$pnotes;
                //disc sdm
                $query = "select igroup, idoutlet_dpl FROM dbdiscount.t_outlet_dpl WHERE IFNULL(igroup,'')='$pgroupid'";
                $tampil4=mysqli_query($cnmy, $query);
                while ($nr2= mysqli_fetch_array($tampil4)) {
                    $pkodeid=$nr2['idoutlet_dpl'];
                    if (!empty($pkodeid)) {

                        $query_detail="INSERT INTO dbdiscount.t_outlet_dpl_d (idoutlet_dpl, distid, disc, keterangan) VALUES "
                                . "('$pkodeid', '0000000000', '$pdiscsdm', '$pket')";
                        $pinsertdetail = mysqli_query($cnmy, $query_detail);
                        $erropesan = mysqli_error($cnmy); 
                        if (!empty($erropesan)) { 
                            echo $erropesan; 
                            mysqli_close($cnmy); exit;
                        }

                    }
                }
                
                
            }
            
            
            //echo "grp : $pgroupid : $fcustid"; exit;
            
        }else{
        
        
            //echo "HAPUS SAJA"; exit;
        
            if (empty($pidcust)) {
                echo "Customer belum diisi...";
                mysqli_close($cnmy); exit;
            }
            
        
            if ($act=="input") {
                $pkodeid="";
                $query_mst="INSERT INTO dbdiscount.t_outlet_dpl (isektorid, nama_outlet, userid, icabangid, areaid, icustid, periode, semester, nodpl, discount, bonus)VALUES"
                        . "('$pidsektor', '$pnamaoutlet', '$pidcardinput', '$pidcab', '$pidarea', '$pidcust', '$pperiode', '$pidsem', '$pnodpl', '$palldiscount', '$pallbonus')";
                $pinsertmaster = mysqli_query($cnmy, $query_mst);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                $pkodeid = mysqli_insert_id($cnmy);
            }



            $psimpandata=false;
            unset($pinsert_data_detail);//kosongkan array
            if (!empty($pkodeid)) {

                
                $query = "UPDATE dbdiscount.t_outlet_dpl SET isektorid='$pidsektor', nama_outlet='$pnamaoutlet', "
                        . " alamat='$palamat', provinsi='$pprovinsi', kota='$pkota', "
                        . " kodepos='$pkdpos', telp='$ptelp', kontakperson='$pkontakperson', "
                        . " notes='$pnotes', discount='$palldiscount', bonus='$pallbonus', "
                        . " icabangid='$pidcab', areaid='$pidarea', icustid='$pidcust', periode='$pperiode', semester='$pidsem', nodpl='$pnodpl', "
                        . " userid='$pidcardinput' WHERE idoutlet_dpl='$pkodeid' LIMIT 1";
                $pinsertmaster = mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }



                $psimpandata=false;
                foreach ($_POST['chk_kodeid'] as $piddist) {
                    $pdiscount=$_POST['e_jmldisc'][$piddist];
                    $pket=$pnotes;//$_POST['txt_ket'][$piddist];

                    if (!empty($pdiscount)) $pdiscount = str_replace("'", '', $pdiscount);
                    if (!empty($pket)) $pket = str_replace("'", ' ', $pket);

                    $pdiscount=str_replace(",","", $pdiscount);


                    $pinsert_data_detail[] = "('$pkodeid', '$piddist', '$pdiscount', '$pket')";
                    $psimpandata=true;


                }

                if ($psimpandata==true) {
                    
                    if ($act=="input") {
                    }else{
                        
                        $pjmldatarec= getfieldcnmy("select count(*) as lcfields FROM dbdiscount.t_outlet_dpl_d WHERE idoutlet_dpl='$pkodeid'");
                        if (empty($pjmldatarec)) $pjmldatarec=0;
                        
                        $query_delete_detail="DELETE FROM dbdiscount.t_outlet_dpl_d WHERE idoutlet_dpl='$pkodeid' LIMIT $pjmldatarec";
                        $pdeletedetail = mysqli_query($cnmy, $query_delete_detail);
                        $erropesan = mysqli_error($cnmy); 
                        if (!empty($erropesan)) { 
                            echo $erropesan;  mysqli_close($cnmy); exit;     
                        }
                    
                    }

                    //$query = "ALTER TABLE dbdiscount.t_outlet_dpl_d AUTO_INCREMENT = 1";
                    //mysqli_query($cnmy, $query);

                    $query_detail="INSERT INTO dbdiscount.t_outlet_dpl_d (idoutlet_dpl, distid, disc, keterangan) VALUES ".implode(', ', $pinsert_data_detail);
                    $pinsertdetail = mysqli_query($cnmy, $query_detail);
                    $erropesan = mysqli_error($cnmy); 
                    if (!empty($erropesan)) { 
                        echo $erropesan; 
                        mysqli_close($cnmy); exit;     
                    }

                }


                $pket=$pnotes;
                //disc sdm
                $query_detail="INSERT INTO dbdiscount.t_outlet_dpl_d (idoutlet_dpl, distid, disc, keterangan) VALUES "
                        . "('$pkodeid', '0000000000', '$pdiscsdm', '$pket')";
                $pinsertdetail = mysqli_query($cnmy, $query_detail);
                $erropesan = mysqli_error($cnmy); 
                if (!empty($erropesan)) { 
                    echo $erropesan; 
                    mysqli_close($cnmy); exit;     
                }


            }
            
            
        }//end pilihan
        
        //mysqli_close($cnmy); exit;
        
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
        
        
        
    }
    
    
    
}



?>
