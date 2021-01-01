<?php

function cBoxIsiDivisi(){
    include "config/koneksimysqli.php";
    $sql=mysqli_query($cnmy, "SELECT distinct divprodid from ms.gpeth order by divprodid");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[divprodid]' name=chkbox_divisi[] checked> $Xt[divprodid]<br/>";
    }
}
function cBoxIsiProduk(){
    include "config/koneksimysqli.php";
    $sql=mysqli_query($cnmy, "SELECT distinct iProdId, nama from ms.iproduk order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[iProdId]' name=chkbox_produk[] checked> $Xt[nama]<br/>";
    }
}

function cBoxIsiCabang(){
    include "config/koneksimysqli.php";
    $sql=mysqli_query($cnmy, "SELECT distinct icabangid, nama from ms.icabang order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[icabangid]' name=chkbox_cabang[] checked> $Xt[nama]<br/>";
    }
}


//COMBOBOX

function ComboGroupProduk($filter, $includkode, $onchange, $filselected, $sizewidth){
    include "config/koneksimysqli.php";
    if (empty ($filselected)) $filselected="0";
    if (empty ($sizewidth)) $sizewidth="100%";
    echo "<select class='form-control' name='e_groupprod' id='e_groupprod' style='width: $sizewidth;' onchange='$onchange'>";
    $ssqcombo="SELECT distinct ifnull(kategori,'NN') as id, ifnull(kategori,'NN') as groupp FROM dbmaster.v_produk ";
    if (!empty ($filter)) $ssqcombo .=" Where kategori='$filter' ";
    //$ssqcombo .=" order by kategori";
    
    $tampilcombo=mysqli_query($cnmy, $ssqcombo);
    echo "<option value='0' selected>- Pilih -</option>";
    while($comb=mysqli_fetch_array($tampilcombo)){
        if (!empty ($includkode)){
            if ($filselected==$comb['id'])
                echo "<option value='$comb[id]' selected>$comb[id] - $comb[groupp]</option>";
            else
                echo "<option value='$comb[id]'>$comb[id] - $comb[groupp]</option>";
        }else
            if ($filselected==$comb['id'])
                echo "<option value='$comb[id]' selected>$comb[groupp]</option>";
            else
                echo "<option value='$comb[id]'>$comb[groupp]</option>";
    }
    echo "</select>";
}



?>
