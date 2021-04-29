<?PHP
session_start();

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);


$puserid="";
$pidcard="";
$pidgroup="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

if ($module=='dkdweeklyvisit')
{
    if ($act=="hapus") {


        exit;
    }elseif ($act=="input" OR $act=="update") {

        $pcardidlog=$_POST['e_idcarduser'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }


        include "../../../config/koneksimysqli.php";

        $pkaryawanid=$_POST['e_idcarduser'];
        $pidjabatan=$_POST['e_idjbt'];
        
        $kodenya=$_POST['e_id'];
        $ptgl=$_POST['e_periode1'];
        $ptgledit=$_POST['e_idtgledit'];
        $pketid=$_POST['cb_ketid'];//keperluan
        $pcompl=$_POST['e_compl'];
        $paktivitas=$_POST['e_aktivitas'];

        $ptanggal= date("Y-m-d", strtotime($ptgl));
        $pedittgl= date("Y-m-d", strtotime($ptgledit));
        if (!empty($pcompl)) $pcompl = str_replace("'", " ", $pcompl);
        if (!empty($paktivitas)) $paktivitas = str_replace("'", " ", $paktivitas);

        if (empty($pidjabatan)) {
            if (isset($_SESSION['JABATANID'])) $pidjabatan=$_SESSION['JABATANID'];
        }
        
        $pkdspv=$_POST['e_kdspv'];
        $pkddm=$_POST['e_kddm'];
        $pkdsm=$_POST['e_kdsm'];
        $pkdgsm=$_POST['e_kdgsm'];
    
        
        $pisitglspv=false;
        $pisitgldm=false;
        $pisitglsm=false;
        $pisitglgsm=false;

        //$pkdspv="";$pkddm="";$pkdsm="A";$pkdgsm="A";

        if (empty($pkdspv)) {
            $pisitglspv=true;
            if (empty($pkddm)) {
                $pisitgldm=true;
                if (empty($pkdsm)) {
                    $pisitglsm=true;
                    if (empty($pkdgsm)) {
                        $pisitglgsm=true;
                    }
                }
            }
        }
        
        
        $pbolehinputakv=true;
        $pbolehinputcal=true;
        
        $filteredit="";
        if ($act=="input") {
            
            $query = "select tanggal from hrd.dkd_new0 where tanggal='$ptanggal' And karyawanid='$pkaryawanid' $filteredit";
            $tampil=mysqli_query($cnmy, $query);
            $ketemu=mysqli_num_rows($tampil);
            if ((INT)$ketemu>0) {
                $pbolehinputakv=false;
                //echo "Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain"; mysqli_close($cnmy); exit;
            }
            
            $query = "select tanggal from hrd.dkd_new1 where tanggal='$ptanggal' And karyawanid='$pkaryawanid' $filteredit";
            $tampil1=mysqli_query($cnmy, $query);
            $ketemu2=mysqli_num_rows($tampil1);
            if ((INT)$ketemu2>0) {
                $pbolehinputcal=false;
                //echo "Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain"; mysqli_close($cnmy); exit;
            }
            
            
            if ($pbolehinputakv == false AND $pbolehinputcal == false) {
                echo "Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain"; mysqli_close($cnmy); exit;
            }
        }
        
        
        $query = "select * from hrd.dkd_new0 WHERE karyawanid='$pkaryawanid' AND tanggal='$ptanggal'";
        $tampilm=mysqli_query($cnmy, $query);
        $ketemum=mysqli_num_rows($tampilm);
        
        //echo "$ketemum : $pkaryawanid, $kodenya, $ptanggal, $pketid, $pcompl, $paktivitas<br/>"; exit;
        
        if ($act=="input" OR (INT)$ketemum<=0) {
            
            if ( (!empty($pketid) OR !empty($paktivitas)) AND $pbolehinputakv==true) {
                $query = "INSERT INTO hrd.dkd_new0 (tanggal, karyawanid, ketid, compl, aktivitas, userid, jabatanid)
                    VALUES
                    ('$ptanggal', '$pkaryawanid', '$pketid', '$pcompl', '$paktivitas', '$pidcard', '$pidjabatan')";
                mysqli_query($cnmy, $query); 
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

                $kodenya = mysqli_insert_id($cnmy);
            }
            
            $kodenya=0;
            
        }elseif ($act=="update") {
            
            $query = "UPDATE hrd.dkd_new0 SET
                tanggal='$ptanggal', karyawanid='$pkaryawanid', 
                ketid='$pketid', compl='$pcompl', aktivitas='$paktivitas', userid='$pidcard', jabatanid='$pidjabatan' WHERE
                karyawanid='$pkaryawanid' AND tanggal='$ptanggal' LIMIT 1";
            mysqli_query($cnmy, $query); 
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

        }

        

        unset($pinsert_data_detail);//kosongkan array
        $psimpandata=false;
        if (isset($_POST['chkbox_br'])) {
            foreach ($_POST['chkbox_br'] as $piddata) {
                if (empty($piddata)) {
                    //continue;
                }
                
                $pjv=$_POST['m_jv'][$piddata];
                $piddokt=$_POST['m_iddokt'][$piddata];
                $pketdokt=$_POST['txt_ketdokt'][$piddata];
                
                if (!empty($pketdokt)) $pketdokt = str_replace("'", " ", $pketdokt);
                
                $pnamajenis="";
                if ($pjv=="Y") $pnamajenis="JV";
                
                //echo "$pjv : $piddokt, $pketdokt<br/>";
                
                $pinsert_data_detail[] = "('$pkaryawanid', '$ptanggal', '$pidjabatan', '$pnamajenis', '$piddokt', '$pketdokt', '$kodenya')";
                $psimpandata=true;
                    
            }
        }

        if ($psimpandata==true AND $pbolehinputcal == true) {

            mysqli_query($cnmy, "DELETE FROM hrd.dkd_new1 WHERE karyawanid='$pkaryawanid' AND tanggal='$ptanggal'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $query_detail="INSERT INTO hrd.dkd_new1 (karyawanid, tanggal, jabatanid, jenis, dokterid, notes, idinput) VALUES ".implode(', ', $pinsert_data_detail);
            mysqli_query($cnmy, $query_detail);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

        }


        /*
            $query = "UPDATE hrd.dkd_new0 SET atasan1='$pkdspv', atasan2='$pkddm', atasan3='$pkdsm', atasan4='$pkdgsm' WHERE idinput='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            if ($pisitglspv==true) {
                $query = "UPDATE hrd.dkd_new0 SET tgl_atasan1=NOW() WHERE idinput='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }

            if ($pisitgldm==true) {
                $query = "UPDATE hrd.dkd_new0 SET tgl_atasan2=NOW() WHERE idinput='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }

            if ($pisitglsm==true) {
                $query = "UPDATE hrd.dkd_new0 SET tgl_atasan3=NOW() WHERE idinput='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }

            if ($pisitglgsm==true) {
                $query = "UPDATE hrd.dkd_new0 SET tgl_atasan4=NOW() WHERE idinput='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }
        */


        mysqli_close($cnmy);
        if ($act=="update") {
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
        }else{
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
        }

        exit;

    }
}
?>