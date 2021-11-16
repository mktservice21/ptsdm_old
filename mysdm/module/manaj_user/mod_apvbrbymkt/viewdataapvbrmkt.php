<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if ($pmodule=="viewnorekeningdata") {
    include "../../../config/koneksimysqli.php";

    $pdokterid=$_POST['uiduser'];
    $pidrek=$_POST['urekid'];
    
    $query = "select a.id_rekening, a.dokterid, a.idbank, b.NAMA as nama_bank, a.kcp, "
            . " a.norekening, a.atasnama, a.relasi_norek "
            . " from hrd.dokter_norekening as a "
            . " LEFT JOIN dbmaster.bank as b on a.idbank=b.KDBANK WHERE a.id_rekening='$pidrek'";
    $tampil=mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampil);
    
    $pbank=$nr['idbank'];
    $pnmbank=$nr['nama_bank'];
    $pkcpbank=$nr['kcp'];
    $pnorekatasnama=$nr['atasnama'];
    $pnorekuser=$nr['norekening'];
    $pnmrelasi=$nr['relasi_norek'];
    
    $psesuai_="Y";
    if (!empty($pnmrelasi)) {
        $psesuai_="N";
    }
    
?>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bank <span class='required'></span></label>
        <div class='col-md-4 col-sm-4 col-xs-12'>
            <input type='hidden' id='e_idbank' name='e_idbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbank; ?>'  Readonly>
            <input type='text' id='e_nmbank' name='e_nmbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmbank; ?>'  Readonly>
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>KCP <span class='required'></span></label>
        <div class='col-md-4 col-sm-4 col-xs-12'>
            <input type='text' id='e_kcpbank' name='e_kcpbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkcpbank; ?>'  Readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Rekening <span class='required'></span></label>
        <div class='col-md-4 col-sm-4 col-xs-12'>
            <input type='text' id='e_norek' name='e_norek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekuser; ?>'  Readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Rekening Atas Nama <span class='required'></span></label>
        <div class='col-md-4 col-sm-4 col-xs-12'>
            <input type='text' id='e_atsnmrek' name='e_atsnmrek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekatasnama; ?>'  Readonly>
        </div>
    </div>

    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
        <div class='col-md-4 col-sm-4 col-xs-12'>
            <?PHP
            if ($psesuai_=="Y") {
                echo "<label><input type='checkbox' class='js-switch' id='chk_sesuai' name='chk_sesuai' value='Y' checked> Rekening Sesuai User</label>";
            }else{
                echo "<label><input type='checkbox' class='js-switch' id='chk_sesuai' name='chk_sesuai' value=''> Rekening Sesuai User</label>";
            }
            ?>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Relasi (istri/anak/dll.) <span class='required'></span></label>
        <div class='col-md-5 col-sm-5 col-xs-12'>
            <input type='text' id='e_relasinorek' name='e_relasinorek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmrelasi; ?>' placeholder="diisi jika atas nama no rekening tidak sesuai user" Readonly>
        </div>
    </div>

<?PHP
    
    mysqli_close($cnmy);
}elseif ($pmodule=="xxxx") {
    
}

?>