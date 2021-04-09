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
    
    
    $query = "select distinct b.tanggal from hrd.t_cuti0 as a JOIN hrd.t_cuti1 as b "
            . " on a.idcuti=b.idcuti WHERE a.idcuti<>'$pidinput' AND b.tanggal in $pilihantgl AND a.karyawanid='$pkaryawanid'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $boleh="Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain";
    }

    mysqli_close($cnmy);

    echo $boleh; exit;
}

?>

