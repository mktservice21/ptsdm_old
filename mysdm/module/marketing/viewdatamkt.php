<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewdatatanggal") {
    $tgl_pertama=$_POST['utgl'];
    $ptglpilih = date('Y-m-01', strtotime($tgl_pertama));
    $p_tgl = date('d', strtotime($ptglpilih));
    $p_akh = date('t', strtotime($ptglpilih));
    
    
    echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih' > $p_tgl &nbsp; &nbsp; ";
    $nom=2;
    for ($ix=1;$ix<(INT)$p_akh;$ix++) {
        $ptglpilih = date('Y-m-d', strtotime('+1 days', strtotime($ptglpilih)));
        $p_tgl = date('d', strtotime($ptglpilih));
        echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih'> $p_tgl &nbsp; &nbsp; ";
        if ($nom>5) {echo "<br/>"; $nom=0;}
        $nom++;
    }
}elseif ($pmodule=="cekdatasudahada") {
    include "../../config/koneksimysqli.php";

    $pidinput=$_POST['uid'];
    $ppilihtgl=$_POST['utglpilih'];
    $pkaryawanid=$_POST['ukry'];
    $pjenis=$_POST['ujenis'];
    $pbln1=$_POST['ubln1'];
    $pbln2=$_POST['ubln2'];
    
    $pbln1= date("Ym", strtotime($pbln1));
    $pbln2= date("Ym", strtotime($pbln2));
    
    $itgl=explode(',',$ppilihtgl);
    $pilihantgl="";
    foreach($itgl as $ptgl)
    {
        $pilihantgl .="'".$ptgl."',";
    }
    if (!empty($pilihantgl)) $pilihantgl="(".substr($pilihantgl, 0, -1).")";
    else $pilihantgl="('')";

    
    $boleh="boleh";
    
    //echo "$boleh"; exit;
    
    if ($pjenis=="02") {
        if ($pbln1>$pbln2) {
            mysqli_close($cnmy); echo "Bulan tidak sesuai..."; exit;
        }
        
        $query = "select distinct b.tanggal from hrd.t_cuti0 as a JOIN hrd.t_cuti1 as b "
                . " on a.idcuti=b.idcuti WHERE a.idcuti<>'$pidinput' AND "
                . " (b.tanggal in $pilihantgl OR (DATE_FORMAT(a.bulan1,'%Y%m') BETWEEN '$pbln1' AND '$pbln2') OR (DATE_FORMAT(a.bulan2,'%Y%m') BETWEEN '$pbln1' AND '$pbln2') ) "
                . " AND a.karyawanid='$pkaryawanid'";
    }else{
        $query = "select distinct b.tanggal from hrd.t_cuti0 as a JOIN hrd.t_cuti1 as b "
                . " on a.idcuti=b.idcuti WHERE a.idcuti<>'$pidinput' AND b.tanggal in $pilihantgl AND a.karyawanid='$pkaryawanid'";
    }
    
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $boleh="Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain";
    }

    mysqli_close($cnmy);

    echo $boleh; exit;
}

?>

