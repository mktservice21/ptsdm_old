<?php

function cBoxIsiDivisiProd($onclick){
    include "config/koneksimysqli_it.php";
    $divkhusus = $_SESSION['KHUSUSSEL'];
    $onc="";
    if (!empty($onclick)) $onc=" onclick=".$onclick."";
    
    $sql=mysqli_query($cnit, "SELECT DivProdId, nama FROM dbmaster.divprod where br='Y' order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        $cek="checked";
        if (!empty($divkhusus)) {
            $adadata = strrpos($divkhusus, $Xt['DivProdId']);
            if ($adadata === false) $cek = "";
        }
        echo "<input type=checkbox value='$Xt[DivProdId]' name='chkbox_divisiprod[]' $onc $cek> $Xt[DivProdId]<br/>";
    }
}
function cBoxIsiKode(){
    include "config/koneksimysqli_it.php";
    $divkhusus = $_SESSION['KHUSUSSEL'];
    $fil="";
    if (!empty($divkhusus)) $fil=" WHERE divprodid in $divkhusus";
    /*
    $wewenang="";
    $sql=mysqli_query($cnit, "select distinct kodeid from dbmaster.v_coa_wewenang where karyawanId='$_SESSION[IDCARD]'");
    while ($Xt=mysqli_fetch_array($sql)){
        $wewenang .=$Xt['kodeid'].",";
    }
     * 
     */
    
    $sql=mysqli_query($cnit, "select kodeid,nama,divprodid from dbmaster.br_kode $fil order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        /* $cek="checked";
        if (!empty($wewenang)) {
            $adadata = strrpos($wewenang, $Xt['kodeid']);
            if ($adadata === false) $cek = "";
        }*/
        echo "<input type=checkbox value='$Xt[kodeid]' name=chkbox_kode[] checked> $Xt[kodeid] - $Xt[nama]<br/>";
    }
}

function cBoxIsiCabang(){
    include "config/koneksimysqli_it.php";
    $sql=mysqli_query($cnit, "SELECT distinct iCabangId, nama from dbmaster.icabang WHERE nama NOT like '%OTC -%' AND nama NOT like '%PEA -%' order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[iCabangId]' name=chkbox_cabang[] checked> $Xt[nama]<br/>";
    }
}


//COMBOBOX

function ComboGroupProduk($filter, $includkode, $onchange, $filselected, $sizewidth){
    include "config/koneksimysqli_it.php";
    if (empty ($filselected)) $filselected="0";
    if (empty ($sizewidth)) $sizewidth="100%";
    echo "<select class='form-control' name='e_groupprod' id='e_groupprod' style='width: $sizewidth;' onchange='$onchange'>";
    $ssqcombo="SELECT distinct ifnull(kategori,'NN') as id, ifnull(kategori,'NN') as groupp FROM dbmaster.v_produk ";
    if (!empty ($filter)) $ssqcombo .=" Where kategori='$filter' ";
    //$ssqcombo .=" order by kategori";
    
    $tampilcombo=mysqli_query($cnit, $ssqcombo);
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
