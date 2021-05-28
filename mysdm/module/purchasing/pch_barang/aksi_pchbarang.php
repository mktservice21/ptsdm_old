<?php
session_start();

    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='pchdatabarang' AND ($act=="hapus" OR $act=="aktifkan") )
{
    include "../../../config/koneksimysqli.php";
    $pidinput=$_GET['id'];
    $puserinput=$_SESSION['IDCARD'];
    
    if (!empty($pidinput)) {
        if ($act=="aktifkan") {
            $query = "UPDATE dbmaster.t_barang SET STSNONAKTIF='N', MODIFUN='$puserinput' WHERE IDBARANG='$pidinput' LIMIT 1";
        }else{
            $query = "UPDATE dbmaster.t_barang SET STSNONAKTIF='Y', MODIFUN='$puserinput' WHERE IDBARANG='$pidinput' LIMIT 1";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    mysqli_close($cnmy);
    header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='pchdatabarang' AND $act=="hapusgambar")
{
    $pidinput=$_GET['id'];
    $pidgambar=$_GET['idgam'];
    if (!empty($pidinput) AND !empty($pidgambar)) {
        include "../../../config/koneksimysqli.php";
        
        $query = "DELETE FROM dbimages.img_barang_gimic WHERE IDBARANG='$pidinput' AND NOURUT='$pidgambar'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        mysqli_close($cnmy);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=editdata'.'&num=$idmenu'.'&id='.$pidinput);
    }
}
elseif ($module=='pchdatabarang')
{
    
    include "../../../config/koneksimysqli.php";
    
    
    $pidinput=$_POST['e_id'];
    
    $phargarp=$_POST['e_harga'];
    if (empty($phargarp)) $phargarp=0;
    $phargarp=str_replace(",","", $phargarp);
    $psatuanid=$_POST['cb_satuan'];
    
    $pidtipebrg=$_POST['cb_tipebrg'];
    $pidgrpbrg="";
    $pidbrand=0;
    $pkategoriid=$_POST['cb_kategori'];
    $pnmbarang=$_POST['e_nmbarang'];
    if (!empty($pnmbarang)) $pnmbarang = str_replace("'", '', $pnmbarang);
    if (empty($psatuanid)) $psatuanid=0;
    $pspesifik=$_POST['e_spesif'];
    $pketer=$_POST['e_keterangan'];
    $psupplierid="";
    
    if (!empty($pspesifik)) $pspesifik = str_replace("'", '', $pspesifik);
    if (!empty($pketer)) $pketer = str_replace("'", '', $pketer);
    
    $puserinput=$_SESSION['IDCARD'];
    
    //echo "$pidinput, $phargarp, $psatuanid, $pidgrpbrg, $pkategoriid, $pnmbarang"; exit;
    
    
    if ($act=="input") {
        
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(IDBARANG,9)) as NOURUT from dbmaster.t_barang");
        $ketemu=  mysqli_num_rows($sql);
        $awal=9; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="I".str_repeat("0", $awal).$urut;
        }
        $pidinput=$kodenya;
        if (empty($pidinput)) {
            echo "KODE KOSONG....ULANGI";
            mysqli_close($cnmy);
            exit;
        }
        $query_eksekusi = "INSERT INTO dbmaster.t_barang (IDBARANG, NAMABARANG, HARGA, IDSATUAN, DIVISIID, IDKATEGORI, MODIFUN, SPESIFIKASI, KETERANGAN, KDSUPP, IDBRAND, IDTIPE)"
                . "VALUES('$pidinput', '$pnmbarang', '$phargarp', '$psatuanid', '$pidgrpbrg', '$pkategoriid', '$puserinput', '$pspesifik', '$pketer', '$psupplierid', '$pidbrand', '$pidtipebrg')";
        mysqli_query($cnmy, $query_eksekusi);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }elseif ($act=="update") {
        
        if (empty($pidinput)) {
            echo "KODE KOSONG....ULANGI";
            mysqli_close($cnmy);
            exit;
        }
        
        $query_eksekusi = "UPDATE dbmaster.t_barang SET NAMABARANG='$pnmbarang', "
                . " HARGA='$phargarp', IDSATUAN='$psatuanid',  DIVISIID='$pidgrpbrg', IDKATEGORI='$pkategoriid', "
                . " MODIFUN='$puserinput', SPESIFIKASI='$pspesifik', KETERANGAN='$pketer', KDSUPP='$psupplierid', IDBRAND='$pidbrand', "
                . " IDTIPE='$pidtipebrg' WHERE IDBARANG='$pidinput' LIMIT 1";
        mysqli_query($cnmy, $query_eksekusi);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }
    
    
    include "../../../config/fungsi_image.php";
    $gambarnya=$_POST['e_imgconv'];
    if (!empty($gambarnya)) {
        mysqli_query($cnmy, "insert into dbimages.img_barang_gimic (IDBARANG, GAMBAR) values ('$pidinput', '$gambarnya')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
		mysqli_close($cnmy);
    }
    
    
    mysqli_close($cnmy);
    header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

?>