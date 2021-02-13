<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $ppilihproses="";
    if (isset($_POST['e_status'])) {// ket pilih proses dari approve belum approve sudag approve
        $ppilihproses=$_POST['e_status'];
    }
    
    if ($ppilihproses=="1") $act="approve";
    elseif ($ppilihproses=="2") $act="unapprove";
    
if ($module=='spgproses' AND ($act=='approve') OR $act=='unapprovex')
{
    $puserid=$_POST['e_idinputuser'];
    $pidcard=$_POST['e_idcarduser'];
    
    if (isset($_SESSION['USERID'])) {
        if (empty($puserid)) { 
            $puserid=$_SESSION['USERID'];
            $pidcard=$_SESSION['IDCARD'];
        }
    }
    
    if (empty($puserid)) { 
        mysqli_close($cnmy);
        echo  "Anda harus login Ulng...!!!";
        exit;
    }
    
     
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpprosspgbadm01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpprosspgbadm02_".$puserid."_$now ";
    
    
    $query = "select idbrspg, periode, id_spg, icabangid, alokid, "
            . " areaid, id_zona, jabatid, kodeid, qty, rp, rptotal, rptotal as rptotal2, "
            . " rptotal as insentif, periode periode_insentif, CAST('' as CHAR(1)) as sts, rptotal total, periode tglpengajuan, rptotal as insentif_tambahan, rptotal as lebihkurang, rptotal as pembulatan, gaji_asli from "
            . " dbmaster.t_spg_gaji_br1 WHERE idbrspg='XYZASASSDD' LIMIT 1";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); exit; }//echo $erropesan; 
    
    
    $query = "DELETE FROM $tmp01"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); exit; }//echo $erropesan; 
    
    
    
    $ptglpengajuan = date("Y-m-d");
    $date1=$_POST['e_periodepilih'];
    $bulan= date("Ym", strtotime($date1));
    $bulan_input= date("Y-m-d", strtotime($date1));
    
    $pidcabang=$_POST['e_cabangpilih'];
    $ptipests="";//pending dan bukan $_POST['cb_status'];
    
    //periode insentif
    $date2=$_POST['e_periodepilih2'];
    $bulaninsentif= date("Ym", strtotime($date2));
    $pperiodeinct= date("Y-m-d", strtotime($date2));
    
    unset($pinsert_data);//kosongkan array
    $jmlrec=0;
    $isimpan=false;
    
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $pidbrspg=$_POST['txtbridspg'][$nobrinput];
            $pidcabang=$_POST['txtidcabang'][$nobrinput];
            $pidalok=$_POST['txtalokid'][$nobrinput];
            $pidarea=$_POST['txtareaid'][$nobrinput];
            $pidzona=$_POST['txtzonaid'][$nobrinput];
            $pidjbt=$_POST['txtjabatid'][$nobrinput];
            
            $pjmlharisistem=$_POST['txtstdkerja'][$nobrinput];
            $pjmlhk=$_POST['txthrkerja'][$nobrinput];
            
            $ptotinsentif=$_POST['txtincentif'][$nobrinput];
            $ptotbosterinc=$_POST['txtincbot'][$nobrinput];
            $ptotlebihkurang=$_POST['txtlebihkurang'][$nobrinput];
            $ptotgp=$_POST['txtgp'][$nobrinput];
            $ptotsewa=$_POST['txtsewa'][$nobrinput];
            $ptotpulsa=$_POST['txtpulsa'][$nobrinput];
            $ptotbbm=$_POST['txtbbm'][$nobrinput];
            $ptotparkir=$_POST['txtparkir'][$nobrinput];
            $ptotlain=$_POST['txtlain'][$nobrinput];
            $prpmakan=$_POST['txtmakan'][$nobrinput];
            $ptotmakan=$_POST['txttotmakan'][$nobrinput];
            
            $ptotalperspg=$_POST['txttotall'][$nobrinput];
            
            $ptotbpjskry=$_POST['txtbpjskry'][$nobrinput];
            $ptotbpjssdm=$_POST['txtbpjssdm'][$nobrinput];
            $ptotplusbpjs=$_POST['txttotallbpjs'][$nobrinput];
            
            $ptotasligaji=$_POST['txtgasli'][$nobrinput];
            
            
            $ptotinsentif=str_replace(",","", $ptotinsentif);
            $ptotbosterinc=str_replace(",","", $ptotbosterinc);
            $ptotlebihkurang=str_replace(",","", $ptotlebihkurang);
            $ptotgp=str_replace(",","", $ptotgp);
            $ptotsewa=str_replace(",","", $ptotsewa);
            $ptotpulsa=str_replace(",","", $ptotpulsa);
            $ptotbbm=str_replace(",","", $ptotbbm);
            $ptotparkir=str_replace(",","", $ptotparkir);
            $ptotlain=str_replace(",","", $ptotlain);
            $prpmakan=str_replace(",","", $prpmakan);
            $ptotmakan=str_replace(",","", $ptotmakan);
            
            $ptotalperspg=str_replace(",","", $ptotalperspg);
            
            $ptotbpjskry=str_replace(",","", $ptotbpjskry);
            $ptotbpjssdm=str_replace(",","", $ptotbpjssdm);
            $ptotplusbpjs=str_replace(",","", $ptotplusbpjs);
            
            $ptotasligaji=str_replace(",","", $ptotasligaji);
            
            //echo "$pidbrspg ($nobrinput), $pidcabang, $pidalok, $pidarea, $pidzona, $pidjbt<br/>";
            //echo "(-) hari kerja sistem : $pjmlharisistem, hari kerja : $pjmlhk, inc :  $ptotinsentif, inc bots : $ptotbosterinc";
            //echo " Lebihkurang : $ptotlebihkurang, GP : $ptotgp, <br/>";
            //echo " sewa : $ptotsewa, Pulsa : $ptotpulsa, BBM : $ptotbbm, parkir : $ptotparkir, makan : $ptotmakan <br/>";
            //echo " Total : $ptotalperspg <br/>";
            //echo " BPJS KRY : $ptotbpjskry, BPJS SDM : $ptotbpjssdm, TOT Plus BPJS $ptotplusbpjs <br/>";
            
            
            $pnokodeid="01";// = Insentif
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '$ptotinsentif', '$ptotinsentif', '0', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            $pnokodeid="02";// = Gaji
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '$ptotgp', '$ptotgp', '0', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '$ptotasligaji')";
            
            
            $pnokodeid="03";// = Uang Makan
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '$pjmlhk', '$prpmakan', '$ptotmakan', '0', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            $pnokodeid="04";// = Sewa Kendaraan
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '$ptotsewa', '$ptotsewa', '0', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            $pnokodeid="05";// = Pulsa
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '$ptotpulsa', '$ptotpulsa', '0', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            $pnokodeid="06";// = Parkir
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '$ptotparkir', '$ptotparkir', '0', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            $pnokodeid="07";// = Insentif Tambahan == boster
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '$ptotbosterinc', '$ptotbosterinc', '0', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            $pnokodeid="08";// = BBM
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '$ptotbbm', '$ptotbbm', '0', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            $pnokodeid="09";// = Kurang / Lebih (Selisih)
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '$ptotlebihkurang', '$ptotlebihkurang', '0', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            $pnokodeid="10";// = BPJS Ketenagakerjaan PT SMD
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '0', '0', '$ptotbpjssdm', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            $pnokodeid="11";// = BPJS Ketenagakerjaan Karyawan
            $pinsert_data[] = "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                    . " '$pnokodeid', '1', '0', '0', '$ptotbpjskry', "
                    . " '$ptotinsentif', '$pperiodeinct', '$ptipests', '$ptotalperspg', "
                    . " '$ptglpengajuan', '$ptotbosterinc', '$ptotlebihkurang', '0', '0')";
            
            
            
            
            $isimpan=true;
        }
    }
    
    if ($isimpan==true) {
        
        
        $query = "INSERT INTO $tmp01 (idbrspg, periode, id_spg, icabangid, alokid, areaid, id_zona, jabatid, "
                . "kodeid, qty, rp, rptotal, rptotal2, "
                . "insentif, periode_insentif, sts, total, tglpengajuan, insentif_tambahan, lebihkurang, pembulatan, gaji_asli) "
                . " VALUES ".implode(', ', $pinsert_data);
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
            echo "ERROR.... $erropesan";
            exit;
            goto hapusdata;
        }
        
        
        $query = "create TEMPORARY table $tmp02 (SELECT * FROM $tmp01)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
            echo "ERROR....";
            exit;
            goto hapusdata;
        }
        
        $query = "Alter table $tmp02 ADD COLUMN coa4 VARCHAR(50)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); exit; }//echo $erropesan; 
        
        $query = "UPDATE $tmp02 as a JOIN dbmaster.t_spg_kode as b on a.kodeid=b.kodeid SET a.coa4=b.coa4";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); exit; }//echo $erropesan; 
                
        
        
            $query = "DELETE FROM dbmaster.t_spg_gaji_br1 WHERE DATE_FORMAT(periode,'%Y%m')='$bulan' AND "
                    . " CONCAT(id_spg,icabangid) IN (SELECT IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') FROM $tmp02)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                echo "ERROR....";
                exit;
                goto hapusdata;
            }

            $query = "INSERT INTO dbmaster.t_spg_gaji_br1 (idbrspg, periode, id_spg, icabangid, alokid, areaid, id_zona, jabatid, kodeid, qty, rp, rptotal, rptotal2, coa4, pembulatan, gaji_asli)"
                    . "SELECT idbrspg, periode, id_spg, icabangid, alokid, areaid, id_zona, jabatid, kodeid, qty, rp, rptotal, rptotal2, coa4, pembulatan, gaji_asli FROM $tmp02";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                echo "ERROR....";
                exit;     
                goto hapusdata;
            }


            $query = "UPDATE dbmaster.t_spg_gaji_br0 a JOIN "
                    . " (select distinct idbrspg, periode, id_spg, periode_insentif, sts, total, tglpengajuan, insentif_tambahan, insentif, lebihkurang FROM $tmp01) b "
                    . " ON a.idbrspg=b.idbrspg AND a.id_spg=b.id_spg AND DATE_FORMAT(a.periode,'%Y%m')=DATE_FORMAT(b.periode,'%Y%m') SET "
                    . " a.insentif=b.insentif, a.insentif_tambahan=b.insentif_tambahan, a.periode_insentif=b.periode_insentif, "
                    . " a.sts=b.sts, a.tglpengajuan=b.tglpengajuan, a.total=b.total, a.lebihkurang=b.lebihkurang, "
                    . " a.apv1='$puserid', a.apvtgl1=NOW() WHERE "
                    . " CONCAT(a.id_spg,a.icabangid) IN (SELECT IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') FROM $tmp02) AND "
                    . " DATE_FORMAT(a.periode,'%Y%m')='$bulan'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 

                $query = "DELETE FROM dbmaster.t_spg_gaji_br1 WHERE DATE_FORMAT(periode,'%Y%m')='$bulan' AND "
                        . " CONCAT(id_spg,icabangid) IN (SELECT IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') FROM $tmp02)";
                mysqli_query($cnmy, $query);

                echo "ERROR....";
                exit;     
                goto hapusdata;
            }
        
        
        
    }
    
    //echo "$puserid, $pidcard - tgl aju : $ptglpengajuan, bln input : $bulan_input, cab : $pidcabang, sts : $ptipests";
hapusdata:
    $query = "DROP TEMPORARY TABLE $tmp01";  mysqli_query($cnmy, $query);
    $query = "DROP TEMPORARY TABLE $tmp02";  mysqli_query($cnmy, $query);
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>