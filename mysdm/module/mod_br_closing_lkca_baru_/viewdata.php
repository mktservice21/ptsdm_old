<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    
if ($_GET['module']=="tampilkansudahproses") {
    include "../../config/koneksimysqli.php";
    $stsreport=$_POST['usts'];
    $ptgl_pillih = $_POST['utgl'];
    $ptgl_pil01= date("Y-m", strtotime($ptgl_pillih));
    
    $ppilih_prose_def=$_SESSION['CLSETHPILIHPROS'];
    
    if ($stsreport!="C") {
        ?>
        <div class='col-sm-2' hidden>
            <div class="form-group">
                <select class='form-control' id="sts_sudahprosesid" name="sts_sudahprosesid">
                    <option value="" selected></option>
                </select>
            </div>
        </div>
        <?PHP
    }else{
        ?>
        <div class='col-sm-2'>
            Pilih Sudah Proses
            <div class="form-group">
                <select class='form-control' id="sts_sudahprosesid" name="sts_sudahprosesid">
                    <?PHP
                    $gno_=1;
                    $query = "select distinct igroup, jmltrans from dbmaster.t_brrutin_ca_close_head WHERE DATE_FORMAT(bulan,'%Y-%m')='$ptgl_pil01' order by igroup";
                    $tampil_= mysqli_query($cnmy, $query);
                    while($nrow= mysqli_fetch_array($tampil_)) {
                        $pigroup=$nrow['igroup'];
                        $pjmltrans=$nrow['jmltrans'];
                        
                        $pjmltrans=number_format($pjmltrans,0,",",",");
                        if ($pigroup==$ppilih_prose_def)
                            echo "<option value='$pigroup' selected>Proses $gno_ &nbsp; - Rp. $pjmltrans</option>";
                        else
                            echo "<option value='$pigroup'>Proses $gno_ &nbsp; - Rp. $pjmltrans</option>";
                        
                        $gno_++;
                    }
                    ?>
                </select>
            </div>
        </div>
        <?PHP
    }
    mysqli_close($cnmy);
}elseif ($_GET['module']=="xxxxx") {
    
}
