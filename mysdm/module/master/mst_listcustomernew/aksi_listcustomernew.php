<?PHP

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();

$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

include "../../../config/koneksimysqli_ms.php";

if ($module=='mstlistcustbaru' AND $act=='simpandatalitcust')
{

    if (!isset($_POST['chkbox_br'])) {
        echo "tidak ada data yang diproses...";
        exit;
    }

    $puserapv=$_POST['e_idcard'];
    
    $bulaninput="2021-05-01";//jangan lupa di viewdatatablecust.php ganti $pbulanawal=


    unset($pinsert_data);//kosongkan array
    $jmlrec=0;
    $isimpan=false;

    foreach ($_POST['chkbox_br'] as $nobrinput) {
        $pidcabang=$_POST['txtidcab'][$nobrinput];
        $pidarea=$_POST['txtidarea'][$nobrinput];
        $pidcust=$_POST['txtidcust'][$nobrinput];
        $pvalue=$_POST['txtvalue'][$nobrinput];

        $pvalue=str_replace(",","", $pvalue);

        //echo "$pidcabang, $pidarea, $pidcust, $pvalue<br/>";

        $pinsert_data[] = "('$pidcabang', '$pidarea', '$pidcust', '$pvalue', '$puserapv', NOW(), '$bulaninput')";
        $isimpan=true;

        $jmlrec++;
    }

    if ($isimpan==true) {
        $query = "INSERT INTO mkt.new_icust (icabangid, areaid, icustid, `value`, approve_dm, tgl_apv_dm, bulan) "
                . " VALUES ".implode(', ', $pinsert_data);
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { 
            echo "ERROR.... $erropesan";
            mysqli_close($cnms);
            exit;
        }

        mysqli_close($cnms);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }

}elseif ($module=='mstlistcustbaru' AND $act=='hapuslistcust')
{
    $pidno=$_GET['uid'];
    if (!empty($pidno)) {
        $query = "DELETE FROM mkt.new_icust WHERE CONCAT(icabangid, areaid, icustid)='$pidno' LIMIT 1";
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { 
            echo "ERROR.... $erropesan";
            mysqli_close($cnms);
            exit;
        }

        mysqli_close($cnms);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
}


mysqli_close($cnms);

?>