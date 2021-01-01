<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    
if ($_GET['module']=="tampilkansudahproses") {
    include "../../../config/koneksimysqli.php";
    $stsreport=$_POST['usts'];
    $ptgl_pillih = $_POST['utgl'];
    $ptgl_pil01= date("Y-m", strtotime($ptgl_pillih));
    if ($stsreport!="C") {
        ?>
        <div class='form-group' hidden>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih Sudah Proses <span class='required'></span></label>
            <div class='col-xs-9'>
                <select class='form-control' id="sts_sudahprosesid" name="sts_sudahprosesid">

                </select>
            </div>
        </div>
        <?PHP
    }else{
        ?>
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih Sudah Proses <span class='required'></span></label>
            <div class='col-xs-9'>
                <select class='form-control' id="sts_sudahprosesid" name="sts_sudahprosesid">
                    <option value="" selected>All</option>
                    <?PHP
                    $gno_=1;
                    $query = "select distinct igroup, jmltrans, saldo from dbmaster.t_brrutin_ca_close_head WHERE DATE_FORMAT(bulan,'%Y-%m')='$ptgl_pil01' order by igroup";
                    $tampil_= mysqli_query($cnmy, $query);
                    while($nrow= mysqli_fetch_array($tampil_)) {
                        $pigroup=$nrow['igroup'];
                        $pjmltrans=$nrow['jmltrans'];
                        $pjmltrans=number_format($pjmltrans,0,",",",");
                        
                        $pjmlsaldo=$nrow['saldo'];
                        $pjmlsaldo=number_format($pjmlsaldo,0,",",",");
                        
                        echo "<option value='$pigroup'>Proses $gno_ &nbsp; - Rp. $pjmlsaldo</option>";
                        $gno_++;
                    }
                    ?>
                </select>
            </div>
        </div>
        <?PHP
    }
    mysqli_close($cnmy);
}

?>
