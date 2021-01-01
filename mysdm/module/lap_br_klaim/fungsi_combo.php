<?php

function cBoxIsiDivisiProd($onclick){
    $onc="";
    if (!empty($onclick)) $onc=" onclick=".$onclick."('chkbox_divisiprod[]')";
    include "config/koneksimysqli.php";
    $sql=mysqli_query($cnmy, "SELECT DivProdId, nama FROM 1it.divprod where br='Y' order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[DivProdId]' name='chkbox_divisiprod[]' $onc checked> $Xt[DivProdId]<br/>";
    }
}
function cBoxIsiKode(){
    include "config/koneksimysqli.php";
    $sql=mysqli_query($cnmy, "select kodeid,nama,divprodid from dbbudget.br_kode order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[kodeid]' name=chkbox_kode[] checked> $Xt[kodeid] - $Xt[nama]<br/>";
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


function cBoxLampiran() {
    echo "<select class='form-control' name='e_lampiran' id='e_lampiran' style='width: 100%;'>";
    echo "<option value='' selected>All</option>";
    echo "<option value='Y'>Y</option>";
    echo "<option value='N'>N</option>";
    echo "</select>";
}

function cBoxCA() {
    echo "<select class='form-control' name='e_ca' id='e_ca' style='width: 100%;'>";
    echo "<option value='' selected>All</option>";
    echo "<option value='Y'>Y</option>";
    echo "<option value='N'>N</option>";
    echo "</select>";
}

function cBoxVIA() {
    echo "<select class='form-control' name='e_via' id='e_via' style='width: 100%;'>";
    echo "<option value='' selected>All</option>";
    echo "<option value='Y'>Y</option>";
    echo "<option value='N'>N</option>";
    echo "</select>";
}

?>
