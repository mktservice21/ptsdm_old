<?php
if ($_GET['module']=="carirealisasi") {
    include "../../config/koneksimysqli.php";
    
    $cari=$_POST["keyword"];
    $pencarian=mysqli_query($cnmy, "SELECT id, idkontak, nama, bank, bankcab, bankrek FROM dbmaster.v_kontak_bank WHERE nama like '%$cari%' 
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
            if (!empty($p["bank"])) $ket=$p["nama"]." - ".$p["bank"];
            if (!empty($p["bankcab"])) $ket=$p["nama"]." - ".$p["bank"]." - ".$p["bankcab"];
            if (!empty($p["bankrek"])) $ket=$p["nama"]." - ".$p["bank"]." - ".$p["bankcab"]." (".$p["bankrek"].")";
            
            
            
            ?> <li onClick="selectDataFormSearch('<?php echo $_GET['myidform']."|".$_GET['idnya']."|".$_GET['myDivForm']."|".$p["id"]."|".$p["idkontak"]."|".$p["nama"]."|".$p["bank"]."|".$p["bankcab"]."|".$p["bankrek"]; ?>');">
            <?php echo $ket; ?></li> <?PHP
                
            $no++;
        }
    }
    echo "</ul>";
}else{
    ?> <li onClick="selectDataFormSearch('<?php echo $_GET['myidform']."|".$_GET['idnya']."|".$_GET['myDivForm']."|".$p["nama"]."|".$p["idkontak"]."|".$p["bank"]."|".$p["bankcab"]."|".$p["bankrek"]."|".$p["id"]."|".$_GET["myBank"]."|".$_GET["myCab"]."|".$_GET["myRek"]."|".$_GET["myId"]; ?>');">
                <?PHP
}
?>
