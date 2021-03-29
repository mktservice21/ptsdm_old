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

if ($module=='dkdrealweeklyplan')
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
        $kodenya=$_POST['e_id'];
        $ptgl=$_POST['e_periode1'];
        $pketid=$_POST['cb_ketid'];//keperluan
        $pcompl=$_POST['e_compl'];
        $paktivitas=$_POST['e_aktivitas'];

        $ptanggal= date("Y-m-d", strtotime($ptgl));
        if (!empty($pcompl)) $pcompl = str_replace("'", " ", $pcompl);
        if (!empty($paktivitas)) $paktivitas = str_replace("'", " ", $paktivitas);


        
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


        //echo "$pkaryawanid, $kodenya, $ptanggal, $pketid, $pcompl, $paktivitas<br/>";

        if ($act=="input") {


        }elseif ($act=="update") {


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
                $psaran=$_POST['txt_saran'][$piddata];
                
                if (!empty($pketdokt)) $pketdokt = str_replace("'", " ", $pketdokt);
                if (!empty($psaran)) $psaran = str_replace("'", " ", $psaran);
                
                $pnamajenis="EC";
                
                
                //echo "$pjv : $piddokt, $pketdokt<br/>";
                
                if ($pjv=="EC") {
                    $pinsert_data_detail[] = "('$kodenya', '$pnamajenis', '$piddokt', '$pketdokt', '$psaran')";
                    $psimpandata=true;
                }
                    
            }
        }

        if ($psimpandata==true) {

            mysqli_query($cnmy, "DELETE FROM hrd.dkd_new1 WHERE idinput='$kodenya' AND IFNULL(jenis,'') IN ('EC')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $query_detail="INSERT INTO hrd.dkd_new1 (idinput, jenis, dokterid, notes, saran) VALUES ".implode(', ', $pinsert_data_detail);
            mysqli_query($cnmy, $query_detail);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

        }




        mysqli_close($cnmy);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');

        exit;

    }
}
?>