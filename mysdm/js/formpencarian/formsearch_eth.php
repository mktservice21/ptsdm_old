<?php
if ($_GET['module']=="carirealisasi") {
    include "../../config/koneksimysqli_it.php";
    
    $cari=$_POST["keyword"];
    $pencarian=mysqli_query($cnit, "SELECT idkontak, nama FROM dbmaster.t_kontak_realisasi_eth WHERE nama like '%$cari%' 
            ORDER BY nama");
    $jum_cari=mysqli_num_rows($pencarian);
    if($jum_cari==0){
        ?><div class='infoCari'>[<a onClick="HideDataFormSearch('<?php echo $_GET['myidform']."|".$_GET['idnya']."|".$_GET['myDivForm']; ?>');"><?php echo "Close / Esc"; ?></a>]&nbsp;&nbsp;&nbsp; Data tidak ditemukan</div><?PHP
    }else{
        ?>
        <div class='infoCari'>[<a onClick="HideDataFormSearch('<?php echo $_GET['myidform']."|".$_GET['idnya']."|".$_GET['myDivForm']; ?>');"><?php echo "Close / Esc"; ?></a>]&nbsp;&nbsp;&nbsp; Ditemukan : <b><?PHP echo $jum_cari; ?></b> data, dengan kata kunci <?PHP echo $cari; ?>.</div>
        <?PHP
        $no=1;
        echo "<ul id='search-form'>";

        while($p=mysqli_fetch_array($pencarian)){
            $ket=$p["nama"];
            ?> <li onClick="selectDataFormSearch('<?php echo $_GET['myidform']."|".$_GET['idnya']."|".$_GET['myDivForm']."|".$p["idkontak"]."|".$p["nama"]; ?>');">
            <?php echo $ket; ?></li> <?PHP
                
            $no++;
        }
    }
    echo "</ul>";
}elseif ($_GET['module']=="carikaryawankontrak") {
    include "../../config/koneksimysqli.php";
    
    $cari=$_POST["keyword"];
    $pencarian=mysqli_query($cnmy, "SELECT id idkontak, nama FROM dbmaster.t_karyawan_kontrak WHERE nama like '%$cari%' 
            ORDER BY nama");
    $jum_cari=mysqli_num_rows($pencarian);
    if($jum_cari==0){
        ?><div class='infoCari'>[<a onClick="HideDataFormSearch('<?php echo $_GET['myidform']."|".$_GET['idnya']."|".$_GET['myDivForm']; ?>');"><?php echo "Close / Esc"; ?></a>]&nbsp;&nbsp;&nbsp; Data tidak ditemukan</div><?PHP
    }else{
        ?>
        <div class='infoCari'>[<a onClick="HideDataFormSearch('<?php echo $_GET['myidform']."|".$_GET['idnya']."|".$_GET['myDivForm']; ?>');"><?php echo "Close / Esc"; ?></a>]&nbsp;&nbsp;&nbsp; Ditemukan : <b><?PHP echo $jum_cari; ?></b> data, dengan kata kunci <?PHP echo $cari; ?>.</div>
        <?PHP
        $no=1;
        echo "<ul id='search-form'>";

        while($p=mysqli_fetch_array($pencarian)){
            $ket=$p["nama"];
            ?> <li onClick="selectDataFormSearch('<?php echo $_GET['myidform']."|".$_GET['idnya']."|".$_GET['myDivForm']."|".$p["idkontak"]."|".$p["nama"]; ?>');">
            <?php echo $ket; ?></li> <?PHP
                
            $no++;
        }
    }
    echo "</ul>";
}else{
    ?> <li onClick="selectDataFormSearch('<?php echo $_GET['myidform']."|".$_GET['idnya']."|".$_GET['myDivForm']."|".$p["nama"]."|".$p["idkontak"].$_GET["myId"]; ?>');">
                <?PHP
}
?>
