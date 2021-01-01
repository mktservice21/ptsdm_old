<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    
    $per1=$_POST['uper1'];
    $per2=$_POST['uper2'];
    $tgl1=$_POST['uperiode'];
    $nourutnya=$_POST['uurut'];
    
    $ptgltrans="";
    $pnobukti="";
    
    $periode1= date("Y-m", strtotime($tgl1));
    $tgl_sblmnya_ = date('F Y', strtotime('-1 month', strtotime($tgl1)));
    
    $sudahclosing="";
    $query = "select bulan from dbmaster.t_brrutin_ca_close_otc where date_format(bulan,'%Y-%m')='$periode1'";
    $ketemucls= mysqli_num_rows(mysqli_query($cnmy, $query));
    if ($ketemucls>0){
        $sudahclosing="SUDAH";
        
        $query = "select tgltrans from dbmaster.t_brrutin0 where 
            idrutin in (select distinct IFNULL(idrutin,'') from dbmaster.tmp_lk_closing_otc) AND 
            IFNULL(tgltrans,'0000-00-00') <> '0000-00-00'";
        $rx= mysqli_fetch_array(mysqli_query($cnmy, $query));
        if (!empty($rx['tgltrans'])) $ptgltrans= date("d F Y", strtotime($rx['tgltrans']));
        
        $query = "select nobukti from dbmaster.t_brrutin0 where 
            idrutin in (select distinct IFNULL(idrutin,'') from dbmaster.tmp_lk_closing_otc) AND 
            IFNULL(nobukti,'') <> ''";
        $rx= mysqli_fetch_array(mysqli_query($cnmy, $query));
        if (!empty($rx['nobukti'])) $pnobukti= $rx['nobukti'];
        
    }
    
    $no=1;

    $gtotjumlah=0;
    $gtotca1=0;
    $gtotselisih=0;
    $gtotca2=0;
    $gtottrans=0;
    
    $gjmladj=0;
    
    $query = "select distinct karyawanid, totalrutin, ca1, ca2, jml_adj from dbmaster.tmp_lk_closing_otc "
            . " WHERE nourut in $nourutnya AND idsession='$_SESSION[IDSESI]' and userid='$_SESSION[IDCARD]' order by karyawanid";
    $result = mysqli_query($cnmy, $query);
    $records = mysqli_num_rows($result);

    if ($records) {
        $reco = 1;
        while ($row = mysqli_fetch_array($result)) {
            $pkaryawanid=$row['karyawanid'];
            $prprutin=number_format($row['totalrutin'],0,",",",");
            $pca1=number_format($row['ca1'],0,",",",");
            $pca2=number_format($row['ca2'],0,",",",");
            $jmladj=$row['jml_adj'];
            

            $pselisih=$row['ca1']-$row['totalrutin'];

            //$pjmltrans=$row['ca2']-$pselisih;
            //if ($pselisih>0 AND $row['ca2']==0) $pjmltrans=0;
            //elseif ($pselisih>0 AND $row['ca2']>0) $pjmltrans=$row['ca2'];
            //elseif ($pselisih==0 AND $row['ca2']>0) $pjmltrans=$row['ca2'];

            
            $pjmltrans=(double)$row['ca2']-(double)$pselisih+(double)$jmladj;
            //if ((double)$jmladj<0) $jmladj=0;
            if ($pselisih>0 AND $row['ca2']==0) $pjmltrans=0;
            elseif ($pselisih>0 AND $row['ca2']>0) $pjmltrans=(double)$row['ca2']+(double)$jmladj;
            elseif ($pselisih==0 AND $row['ca2']>0) $pjmltrans=(double)$row['ca2']+(double)$jmladj;
            
            

            $gtotca1=$gtotca1+$row['ca1'];
            $gtotca2=$gtotca2+$row['ca2'];
            $gtotselisih=$gtotselisih+$pselisih;
            
            $gjmladj=(double)$gjmladj+(double)$row['jml_adj'];
            
            ///$gtottrans=$gtottrans+$pjmltrans;
            
            if ((double)$jmladj>0)
                $gtottrans=$gtottrans+$pjmltrans+$jmladj;
            else
                $gtottrans=$gtottrans+$pjmltrans;

            $query = "select * from dbmaster.tmp_lk_closing_otc where "
                    . " nourut in $nourutnya AND idsession='$_SESSION[IDSESI]' and userid='$_SESSION[IDCARD]' AND karyawanid='$pkaryawanid' order by idrutin";
            $result2 = mysqli_query($cnmy, $query);


            $belum=false;    
            while ($row2 = mysqli_fetch_array($result2)) {

                //$gjmladj=(double)$gjmladj+(double)$row2['jml_adj'];
                
                $pnolk=$row2['idrutin'];
                $pidca1=$row2['idca1'];
                $pidca2=$row2['idca2'];
                $pjumlah=number_format($row2['jumlah'],0,",",",");
                $gtotjumlah=$gtotjumlah+$row2['jumlah'];

                $chkck="";
                $pnourutnya=$row2['nourut'];
                $ceklisnya = "<input type='checkbox' value='$pnourutnya' name='chkpilih[]' id='chkpilih[]' class='cekbr' $chkck>";

                if ($belum==true) {

                }else{
                    $pselisih=number_format($pselisih,0,",",",");
                    $pjmltrans=number_format($pjmltrans,0,",",",");
                }

                $belum=true;
                $no++;
                $reco++;
            }
        }

    }
?>
<script src="js/inputmask.js"></script>
<div class='x_panel'>
    <div class='x_content'>
        <div class='col-md-12 col-sm-12 col-xs-12'>


            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                <div class='col-xs-3'>
                     <div id='loading2'></div>
                    <button type='button' id="btnhitung" name="btnhitung" class='btn btn-danger btn-xs' onclick='HitungTotalJumlah()'>Hitung Jumlah</button> <span class='required'></span>
                    
                    <input type='hidden' id='e_per1' name='e_per1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $per1; ?>' Readonly>
                    <input type='hidden' id='e_per2' name='e_per2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $per2; ?>' Readonly>
                    <input type='hidden' id='e_periode' name='e_periode' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tgl1; ?>' Readonly>
                    <input type='hidden' id='e_nourut' name='e_nourut' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nourutnya; ?>' Readonly>
                    <input type='hidden' id='e_sudah' name='e_sudah' class='form-control col-md-7 col-xs-12' value='<?PHP echo $sudahclosing; ?>' Readonly>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saldo Real <span class='required'></span></label>
                <div class='col-xs-3'>
                    <input type='text' id='e_saldo' name='e_saldo' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotjumlah; ?>' Readonly>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CA <?PHP echo $per1; ?><span class='required'></span></label>
                <div class='col-xs-3'>
                    <input type='text' id='e_ca1' name='e_ca1' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotca1; ?>' Readonly>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Selisih <span class='required'></span></label>
                <div class='col-xs-3'>
                    <input type='text' id='e_selisih' name='e_selisih' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotselisih; ?>' Readonly>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CA <?PHP echo $per2; ?><span class='required'></span></label>
                <div class='col-xs-3'>
                    <input type='text' id='e_ca2' name='e_ca2' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotca2; ?>' Readonly>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Utang Piutang <?PHP echo $tgl_sblmnya_; ?><span class='required'></span></label>
                <div class='col-xs-3'>
                    <input type='text' id='e_jmladj' name='e_jmladj' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gjmladj; ?>' Readonly>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Trsf. <span class='required'></span></label>
                <div class='col-xs-3'>
                    <input type='text' id='e_jmltrsf' name='e_jmltrsf' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtottrans; ?>' Readonly>
                </div>
            </div>

            <div hidden>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Trsf. <span class='required'></span></label>
                <div class='col-xs-3'>
                    <div class='input-group date' id='tgl1'>
                        <input type='text' id='e_periode01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $ptgltrans; ?>' data-inputmask="'mask': '99/99/9999'">
                        <span class="input-group-addon">
                           <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>

            <div hidden>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Bukti <span class='required'></span></label>
                <div class='col-xs-3'>
                    <input type='text' id='e_nobukti' name='e_nobukti' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnobukti; ?>'>
                    <input type='text' id='e_nodivisi' name='e_nodivisi' class='form-control col-md-7 col-xs-12' value=''>
                    <input type='text' id='e_iddanabank' name='e_iddanabank' class='form-control col-md-7 col-xs-12' value=''>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                <div class='col-xs-9'>
                    <div class="checkbox">
                        <button type='button' class='btn btn-success' id="btnsave" name="btnsave" onclick='disp_confirm("Simpan ?", "<?PHP echo ""; ?>")'>Save</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('#e_periode01').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            /*
            minDate: '0',
            maxDate: '+2Y',
            */
            onSelect: function(dateStr) {
            } 
        });
    });
</script>