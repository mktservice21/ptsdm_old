<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];

    
if ($module=='closingbrlkca2' AND $act=='hapus')
{
    $pigroup_pillih = $_POST['e_idgroup'];
    $ptgl_pillih = $_POST['e_per1'];
    $ptgl_pillih= date("Ym", strtotime($ptgl_pillih));
    
    $stsreport = $_POST['e_sts'];
    $scaperiode1 = $_POST['e_periodeca1'];
    $scaperiode2 = $_POST['e_periodeca2'];
    
    
    $query = "DELETE FROM dbmaster.t_brrutin_ca_close WHERE DATE_FORMAT(bulan,'%Y%m')='$ptgl_pillih' and IFNULL(igroup,'')='$pigroup_pillih'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "DELETE FROM dbmaster.t_brrutin_ca_close_head WHERE DATE_FORMAT(bulan,'%Y%m')='$ptgl_pillih' and IFNULL(igroup,'')='$pigroup_pillih'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //echo "$pigroup_pillih : $ptgl_pillih, $stsreport, $scaperiode1, $scaperiode2"; exit;

    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='closingbrlkca2')
{   
                    /*

                    $now=date("mdYhis");
                    $puserid=$_SESSION['USERID'];
                    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$puserid."_$now ";
                    
                    ini_set("memory_limit","512M");
                    ini_set('max_execution_time', 0);

                    //cek selisih, jumlah transfer
                    $query = "SELECT * FROM dbmaster.t_brrutin_ca_close_otc";
                    $query = "create TEMPORARY table $tmp01 ($query)"; 
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto myhapus; }

                    $query = "select * from $tmp01 order by nourut";
                    $tampil_= mysqli_query($cnit, $query);
                    while ($row1= mysqli_fetch_array($tampil_)) {
                        $pnourut=$row1['nourut'];
                        $pkaryawanid=$row1['karyawanid'];

                        $pjmlca1 = $row1['ca1'];
                        $pjmllk = $row1['saldo'];
                        $pjmlca2 = $row1['ca2'];

                        $pjumlahadj=0;

                        if (empty($pjmlca1)) $pjmlca1=0;
                        if (empty($pjmllk)) $pjmllk=0;
                        if (empty($pjmlca2)) $pjmlca2=0;

                        $pselisih=(double)$pjmlca1-(double)$pjmllk;

                        $pjmltrans= ( (double)$pjmlca2-(double)$pselisih ) + (double)$pjumlahadj;
                        //if ((double)$pjmltrans<0) $pjmltrans=0;
                        if ($pselisih>0 AND (double)$pjmlca2==0) $pjmltrans=0;
                        elseif ((double)$pselisih>0 AND (double)$pjmlca2>0) $pjmltrans=(double)$pjmlca2 + (double)$pjumlahadj;
                        elseif ((double)$pselisih==0 AND (double)$pjmlca2>0) $pjmltrans=(double)$pjmlca2 + (double)$pjumlahadj;

                        if (empty($pselisih)) $pselisih=0;
                        if (empty($pjmltrans)) $pjmltrans=0;

                        $query="UPDATE dbmaster.t_brrutin_ca_close a SET a.selisih='$pselisih', jmltrans='$pjmltrans' WHERE nourut='$pnourut'";
                        mysqli_query($cnit, $query);
                        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


                    }

                    //END cek selisih, jumlah transfer

                    myhapus:
                        mysqli_query($cnit, "drop TEMPORARY table $tmp01");


                    exit;

                    */
    
    $pigroup_pillih = $_POST['e_idgroup'];
    //harus ada diseleksi
        $pilih_koneksi="../../config/koneksimysqli.php";
        $ptgl_pillih = $_POST['e_per1'];
        $stsreport = $_POST['e_sts'];
        $pprosid_sts = "";// untuk yang sudah closing di form closingan
        $scaperiode1 = $_POST['e_periodeca1'];
        $scaperiode2 = $_POST['e_periodeca2'];
        $iproses_simpandata=true;
        
        $u_filterkaryawan="";
        foreach ($_POST['chkbox_br'] as $no_brid) {
            $u_filterkaryawan .="'".$no_brid."',";
        }
        if (!empty($u_filterkaryawan)) $u_filterkaryawan=" (".substr($u_filterkaryawan, 0, -1).")";
    //END harus ada diseleksi
    //seleksi data
    include ("seleksi_data_lk_ca.php");
    
    $psaldo = $_POST['e_saldo'];
    $pca1 = $_POST['e_ca1'];
    $pca2 = $_POST['e_ca2'];
    $pkuranglebihca1 = $_POST['e_kuranglebihca1'];
    $pselisih = $_POST['e_selisih'];
    $pjmladj = $_POST['e_jmladj'];
    $pjmltransf = $_POST['e_jmltrsf'];
    
    $psaldo=str_replace(",","", $psaldo);
    $pca1=str_replace(",","", $pca1);
    $pca2=str_replace(",","", $pca2);
    $pkuranglebihca1=str_replace(",","", $pkuranglebihca1);
    $pselisih=str_replace(",","", $pselisih);
    $pjmladj=str_replace(",","", $pjmladj);
    $pjmltransf=str_replace(",","", $pjmltransf);
    

    //echo "$psaldo, $pca1, $pca2, $pkuranglebihca1, $pselisih, $pjmladj, $pjmltransf";
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp_01 =" dbtemp.DTBRSAVES01_".$puserid."_$now ";
    
    $query="karyawanid CHAR(10), ca1 DECIMAL(20,2), kuranglebihca1 DECIMAL(20,2), selisih DECIMAL(20,2), jmltrans DECIMAL(20,2)";
    $query = "create TEMPORARY table $tmp_01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    foreach ($_POST['chkbox_br'] as $no_brid) {
        $pjmlca1=$_POST['txt_1_ca'][$no_brid];
        $pjmlca1=str_replace(",","", $pjmlca1);
        
        $pjmlkurlebca1=$_POST['txtkuranglebih_1ca'][$no_brid];
        $pjmlkurlebca1=str_replace(",","", $pjmlkurlebca1);
        
        $pjmlselisih=$_POST['txtselisih'][$no_brid];
        $pjmlselisih=str_replace(",","", $pjmlselisih);
            
        $pjmltrans=$_POST['txt_ntrans'][$no_brid];
        $pjmltrans=str_replace(",","", $pjmltrans);
        
        $query = "INSERT INTO $tmp_01 (karyawanid, ca1, kuranglebihca1, selisih, jmltrans)VALUES"
                . "('$no_brid', '$pjmlca1', '$pjmlkurlebca1', '$pjmlselisih', '$pjmltrans')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    $query = "UPDATE $tmp01 a JOIN $tmp_01 b on a.karyawanid=b.karyawanid "
            . " SET a.ca1=b.ca1, a.kuranglebihca1=b.kuranglebihca1, a.selisih=b.selisih, a.jmltrans=b.jmltrans"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $no_idgroup=1;
    $query = "SELECT MAX(igroup) igroup FROM dbmaster.t_brrutin_ca_close";
    $tampil= mysqli_query($cnit, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $ox= mysqli_fetch_array($tampil);
        if (!empty($ox['igroup'])) {
            $no_idgroup=(double)$ox['igroup']+1;
        }
    }

    
    $query = "INSERT INTO dbmaster.t_brrutin_ca_close "
            . "(tglinput, bulan, karyawanid, divisi, idrutin, idca1, idca2, credit, saldo, ca1, "
            . "kuranglebihca1, selisih, ca2, jml_adj, jmltrans, periode_ca1, periode_ca2, sts,"
            . "icabangid, areaid, atasan1, atasan2, atasan3, atasan4, jabatanid, igroup)"
            . "SELECT CURRENT_DATE() as tglinput, bulan, karyawanid, divisi, idrutin, idca1, idca2, credit, saldo, ca1, "
            . "kuranglebihca1, selisih, ca2, jml_adj, jmltrans, periode_ca1, periode_ca2, sts,"
            . "icabangid, areaid, atasan1, atasan2, atasan3, atasan4, jabatanid, '$no_idgroup' as igroup FROM $tmp01";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO dbmaster.t_brrutin_ca_close_head "
            . "(igroup, periode_ca1, periode_ca2, sts, bulan, saldo, ca1, "
            . "kuranglebihca1, selisih, ca2, jml_adj, jmltrans)VALUES"
            . "('$no_idgroup', '$scaperiode1', '$scaperiode2', 'C', '$ptgl_pillih', '$psaldo', '$pca1', "
            . "'$pkuranglebihca1', '$pselisih', '$pca2', '$pjmladj', '$pjmltransf')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    hapusdata:
        mysqli_query($cnit, "drop TEMPORARY table $tmp01");
        mysqli_query($cnit, "drop TEMPORARY table $tmp02");
        mysqli_query($cnit, "drop TEMPORARY table $tmp03");
        mysqli_query($cnit, "drop TEMPORARY table $tmp04");
        mysqli_query($cnit, "drop TEMPORARY table $tmp_01");
        
        mysqli_close($cnit);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
        
}
?>