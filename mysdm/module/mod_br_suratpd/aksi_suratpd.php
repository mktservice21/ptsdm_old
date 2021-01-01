<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    $pkert=$_POST['ket'];
    $pidnomor=$_POST['unobr'];

    $pketreject=$_POST['ketrejpen'];
    //$berhasil="$pkert, $pidnomor, $pketreject";
    $userid=$_SESSION['IDCARD'];
    if (empty($userid)) {
        echo "data tidak ada yang diproses, silakan login ulang..."; exit;
    }
    $berhasil = "Tidak ada data yang diproses...";
    if (!empty($pidnomor)) {
        if ($pkert=="simpan") {
            
            $ptgl=$_POST['utgl'];
            $ptglspd= date("Y-m-d", strtotime($ptgl));
            $pnospd=$_POST['unospd'];
            
            
            $now=date("mdYhis");
            $tmp01 =" dbtemp.DBSAVEPD01_".$userid."_$now ";
    
            $query = "SELECT idinput, idinputbank, NOW() tglinput, tanggal tgl, CURRENT_DATE() as tglspd, divisi, kodeid, subkode, "
                    . " nomor, jumlah, keterangan, userid karyawanid, "
                    . " userid, userid as userproses, NOW() as tgl_proses, coa4, CURRENT_DATE() tglf, CURRENT_DATE() tglt "
                    . " FROM dbmaster.t_suratdana_bank WHERE idinputbank IN $pidnomor";
            
            $query = "create temporary table $tmp01 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            //bunga diganti jadi minus
            $query = "UPDATE $tmp01 SET jumlah=0-IFNULL(jumlah,0) WHERE CONCAT(kodeid, subkode) IN ('231') AND IFNULL(jumlah,0)>0"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
			
			
            $query = "select MAX(idinput) as NOURUT from dbmaster.t_suratdana_br";
            $tampil_= mysqli_query($cnmy, $query);
            $ketemu_ = mysqli_num_rows($tampil_);
            
            $pnourut_spd="";
            if ($ketemu_>0) {
                $xs= mysqli_fetch_array($tampil_);
                $pnourut_spd=(double)$xs['NOURUT'];
            }
                
            $query = "select * from $tmp01 ORDER BY idinputbank";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu = mysqli_num_rows($tampil);
            
            if ($ketemu>0) {
                
                if (!empty($pnourut_spd)) {
                    
                    while($nr= mysqli_fetch_array($tampil)){
                        $pidinputbak=$nr['idinputbank'];
                        
                        $pnourut_spd=(double)$pnourut_spd+1;
                        
                        $query = "UPDATE $tmp01 SET idinput='$pnourut_spd' WHERE idinputbank='$pidinputbak'";
                        mysqli_query($cnmy, $query);
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "drop temporary table $tmp01"); echo $erropesan; exit; }
                    }
                    
                    
                    $query = "UPDATE $tmp01 SET userid='$userid', tglspd='$ptglspd', nomor='$pnospd', userproses='$userid', tgl_proses=NOW()";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "drop temporary table $tmp01"); echo $erropesan; exit; }
                    
                    

                    $query = "INSERT INTO dbmaster.t_suratdana_br"
                            . " (idinputbank, idinput, tglinput, tgl, tglspd, divisi, kodeid, subkode, "
                            . " nomor, jumlah, keterangan, karyawanid, "
                            . " userid, userproses, tgl_proses, coa4, tglf, tglt) "
                            . " SELECT idinputbank, idinput, NOW() tglinput, tgl, tglspd, divisi, kodeid, subkode, "
                            . " nomor, jumlah, keterangan, userid karyawanid, "
                            . " userid, userproses, tgl_proses, coa4, CURRENT_DATE() tglf, CURRENT_DATE() tglt "
                            . " FROM $tmp01";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "drop temporary table $tmp01"); echo "$erropesan <br/>ERROR INSERT TEMP BANK"; exit; }
                    
                    
                    $query = "UPDATE dbmaster.t_suratdana_bank a JOIN $tmp01 b ON a.idinputbank=b.idinputbank SET "
                            . " a.idinput=b.idinput, a.nomor=b.nomor WHERE a.idinputbank IN $pidnomor AND a.subkode NOT IN ('29')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "drop temporary table $tmp01"); echo "$erropesan <br/>ERROR UPDATE BANK TEMP"; exit; }
                    
					
                    $query = "UPDATE dbmaster.t_suratdana_bank a JOIN $tmp01 b ON a.idinputbank=b.idinputbank SET "
                            . " a.sudahklaim='Y' WHERE a.idinputbank IN $pidnomor";
                    mysqli_query($cnmy, $query);
					
					
                    
                }
            
            }        
                    
            mysqli_query($cnmy, "drop temporary table $tmp01");
            
            
            $query = "UPDATE dbmaster.t_suratdana_br SET tglspd='$ptglspd', nomor='$pnospd', userproses='$userid', tgl_proses=NOW() WHERE IFNULL(nomor,'')='' AND idinput IN $pidnomor";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
            $berhasil = "Data berhasil diproses...";
            
        }if ($pkert=="hapus") {
                
                $now=date("mdYhis");
                $tmp01 =" dbtemp.DBSAVEPD01_".$userid."_$now ";

                $query = "SELECT idinput, idinputbank, nomor FROM dbmaster.t_suratdana_br WHERE idinput IN $pidnomor AND IFNULL(idinputbank,'')<>''";
                $query = "create temporary table $tmp01 ($query)"; 
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                $query = "select * from $tmp01";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu = mysqli_num_rows($tampil);

                if ($ketemu>0) {
                    $nnidinput_="";
                    while($nr= mysqli_fetch_array($tampil)){
                        $nnidinput_ .="'".$nr['idinput']."',";
                    }
                    
                    if (!empty($nnidinput_)) {
                        $nnidinput_="(".substr($nnidinput_, 0, -1).")";
                    
                        $query = "UPDATE dbmaster.t_suratdana_bank a JOIN $tmp01 b ON a.idinputbank=b.idinputbank AND "
                                . " a.idinput=b.idinput AND a.nomor=b.nomor SET "
                                . " a.idinput=0, a.nomor='' WHERE a.idinput IN $pidnomor AND a.subkode NOT IN ('29')";
                        mysqli_query($cnmy, $query);
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "drop temporary table $tmp01"); echo "$erropesan <br/>ERROR UPDATE BANK TEMP"; exit; }

                        $query = "UPDATE dbmaster.t_suratdana_br a JOIN $tmp01 b ON a.idinputbank=b.idinputbank AND "
                                . " a.idinput=b.idinput AND a.nomor=b.nomor SET "
                                . " a.stsnonaktif='Y' WHERE IFNULL(a.idinputbank,'')<>'' AND a.idinput IN $nnidinput_";
                        mysqli_query($cnmy, $query);
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
						
						
                        $query = "UPDATE dbmaster.t_suratdana_bank a JOIN (select distinct idinputbank from $tmp01 where IFNULL(idinputbank,'')<>'') b ON a.idinputbank=b.idinputbank SET "
                                . " a.sudahklaim='N'";
                        mysqli_query($cnmy, $query);
						
                    
                    }
                    
                }

                mysqli_query($cnmy, "drop temporary table $tmp01");
                
                
            
            $query = "UPDATE dbmaster.t_suratdana_br SET tglspd='', nomor='', userproses=NUll, tgl_proses=NULL, idinputbank=NULL WHERE idinput IN $pidnomor";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            $berhasil = "Data berhasil dihapus...";
            
        }
    }
    

    echo $berhasil;
?>

