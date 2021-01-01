<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<?PHP
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl_msk = date('Ym', strtotime($hari_ini));

$tgl_pengajuandana= date('F Y', strtotime($hari_ini));

                
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$divisi="HO";
if ($_SESSION['DIVISI']=="OTC") $divisi="OTC";
$p_bnkjumlah="";
$pcoa="105-02";
$pjenis="5";
$psubkode="";
$pstatus="1";
$pnomor="";
$pnodivisi="";
$pketerangan="";
$pbrnoid="";
$pbrnoslip="";
$pd_spd="N";
$pd_spd_debker="D";
if ($pses_grpuser!="25") $pd_spd="Y";
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_bank WHERE idinputbank='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idinputbank'];
    $tgl1 = date('d/m/Y', strtotime($r['tanggal']));
    $tgl_msk = date('Ym', strtotime($r['tanggal']));
    $pketerangan=$r['keterangan'];
    $p_bnkjumlah=$r['jumlah'];
    $pjenis=$r['kodeid'];
    $psubkode=$r['subkode'];
    $divisi=$r['divisi'];
    $pnomor=$r['nomor'];
    $pnodivisi=$r['nodivisi'];
    $pcoa=$r['coa4'];
    $pstatus=$r['sts'];
    $pd_spd_debker=$r['stsinput'];
    
    $pbrnoid=$r['brid'];
    $pbrnoslip=$r['noslip'];

    
    if (!empty($pnomor) OR !empty($pnodivisi)) {
        $pd_spd="Y";
        $carispd = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br WHERE nodivisi='$pnodivisi'");
        $sp    = mysqli_fetch_array($carispd);
        $ntglspd=$sp['tgl'];
        if (!empty($sp['tglspd']) AND $sp['tglspd']<>"0000-00-00") {
            $ntglspd=$sp['tglspd'];
        }
        $tgl_pengajuandana= date('F Y', strtotime($ntglspd));
    }
    
}

$ntglclose="";
/*
$caricls = mysqli_query($cnmy, "SELECT bulan FROM dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$tgl_msk'");
$cl    = mysqli_fetch_array($caricls);
if (!empty($cl['bulan'])) $ntglclose = date('d/m/Y', strtotime($cl['bulan']));
*/

?>

<?PHP if ($_GET['act']=="editdata"){ ?>
    <script> window.onload = function() { document.getElementById("e_jml").focus(); } </script>
<?PHP }else{ ?>
    <script> window.onload = function() { document.getElementById("e_id").focus(); } </script>
<?PHP } ?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Transaksi </label>
                                    <div class='col-md-3'>
                                        <input type="hidden" class="form-control" id='e_tgl_cls' name='e_tgl_cls' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ntglclose; ?>'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tglmasuk' name='e_tglmasuk' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>


                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dari SPD <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_darispd' name='cb_darispd' onchange="ShowAwal()">
                                            <?PHP
                                            $pdari_sel1="selected";
                                            $pdari_sel2="";
                                            if ($pd_spd=="Y") $pdari_sel2="selected";

                                            echo "<option value='T' $pdari_sel1>N</option>";
                                            echo "<option value='Y' $pdari_sel2>Y</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div id="div_spd">
                                    
                                    <style>
                                        .ui-datepicker-calendar {
                                            display: none;
                                        }
                                    </style>
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan Pengajuan Dana</label>
                                        <div class='col-md-3'>
                                            <input type="checkbox" id="chk_pilihbln" name="chk_pilihbln" value="" onclick="HapusBulanSPD()" >
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_tglspd' name='e_tglspd' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $tgl_pengajuandana; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih No. SPD <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_nospd' name='cb_nospd' onchange="ShowDataNoDivisiBR()">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                if ($_GET['act']=="editdata" AND $pd_spd=="Y"){
                                                    $fnomor="";
                                                    if (!empty($pnomor)) $fnomor=" OR nomor='$pnomor' ";
                                                    $ptglspd = date('Y-m', strtotime($ntglspd));
                                                    $filterrbulan=" AND ( DATE_FORMAT(tgl,'%Y-%m')='$ptglspd' OR DATE_FORMAT(tglspd,'%Y-%m')='$ptglspd' ) ";
                                                    $query = "select nomor, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE ( IFNULL(stsnonaktif,'')<>'Y' "
                                                            . " AND IFNULL(nomor,'')<>'' AND "
                                                            . "( DATE_FORMAT(tgl,'%Y-%m')='$ptglspd' OR DATE_FORMAT(tglspd,'%Y-%m')='$ptglspd' ) "
                                                            . ") $fnomor"
                                                            . "GROUP BY 1 ORDER BY 1";//and IFNULL(pilih,'') = 'Y'
                                                    $tampil= mysqli_query($cnmy, $query);
                                                    while ($tr= mysqli_fetch_array($tampil)) {
                                                        $pjmlspd=$tr['jumlah'];
                                                        if (!empty($pjmlspd)) $pjmlspd=number_format($pjmlspd,0);
                                                        $pnomorspd=$tr['nomor'];
                                                        $pajsketjml = "$pnomorspd   &nbsp;&nbsp;&nbsp;&nbsp;    (Rp. $pjmlspd)";
                                                        if ($pnomorspd==$pnomor AND !empty($pnomor))
                                                            echo "<option value='$pnomorspd' selected>$pajsketjml</option>";
                                                        else
                                                            echo "<option value='$pnomorspd'>$pajsketjml</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih No. BR/Divisi <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_nodivisi' name='cb_nodivisi' onchange="ShowDataNoBRSlip()">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                if ($_GET['act']=="editdata" AND $pd_spd=="Y"){
                                                    $n_filterkaryawan="";
                                                    if ($pses_grpuser=="1" OR $pses_grpuser=="24") {
                                                    }else{
                                                        if ($pses_divisi=="OTC") {
                                                            $n_filterkaryawan=" AND divisi='OTC' ";
                                                        }else{
                                                            $n_filterkaryawan=" AND divisi<>'OTC' AND karyawanid='$pses_idcard' ";
                                                        }
                                                    }
                                                    
                                                    $pbln= date("Y-m", strtotime($ntglspd));
                                                    $filnospd="";
                                                    if (!empty($pnomor)) $filnospd=" AND IFNULL(nomor,'')='$pnomor' ";
                                                    $query = "select divisi, nodivisi, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE ("
                                                            . " IFNULL(stsnonaktif,'')<>'Y' "
                                                            . " and IFNULL(pilih,'') = 'Y' AND IFNULL(nodivisi,'')<>'' $filnospd  "
                                                            . " AND ( DATE_FORMAT(tgl,'%Y-%m')='$pbln' OR DATE_FORMAT(tglspd,'%Y-%m')='$pbln' ) "
                                                            . " $n_filterkaryawan) OR nodivisi='$pnodivisi' "
                                                            . " GROUP BY 1,2 ORDER BY 1,2";
                                                    $tampil = mysqli_query($cnmy, $query);

                                                    while ($zs= mysqli_fetch_array($tampil)) {
                                                        $pjumlah=$zs['jumlah'];
                                                        if (!empty($pjumlah)) $pjumlah=number_format($pjumlah,0);
                                                        $pnobrdiv=$zs['nodivisi'];
                                                        $pdivisi=$zs['divisi'];
                                                        if (empty($pdivisi)) $pdivisi= "ETHICAL";
                                                        $pajsketjml = "$pnobrdiv   &nbsp;&nbsp;&nbsp;    (Rp. $pjumlah)";
                                                        if ($pnobrdiv==$pnodivisi)
                                                            echo "<option value='$pnobrdiv' selected>$pajsketjml</option>";
                                                        else
                                                            echo "<option value='$pnobrdiv'>$pajsketjml</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID BR / Noslip <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <div class='input-group '>
                                            <span class='input-group-btn'>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataBRSlip('e_idnobr', 'e_noslip')">Pilih!</button>
                                            </span>
                                            <input type='hidden' class='form-control' id='ex_idnobrxx' name='ex_idnobrxx' value='<?PHP echo $pbrnoid; ?>' Readonly>
                                            <input type='text' class='form-control' id='e_idnobr' name='e_idnobr' value='<?PHP echo $pbrnoid; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <div class='input-group '>
                                            <input type='text' class='form-control' id='e_noslip' name='e_noslip' value='<?PHP echo $pbrnoslip; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Noslip / ID BR <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_nobrslip' name='cb_nobrslip' onchange="">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                if ($_GET['act']=="editdata" AND $pd_spd=="Y"){
                                                    /*
                                                    $query = "select idinput from dbmaster.t_suratdana_br WHERE nodivisi='$pnodivisi'";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    $z= mysqli_fetch_array($tampil);
                                                    $nidinput=$z['idinput'];

                                                    $query = "select brId, noslip, jumlah From hrd.br0 where brId in 
                                                            (select DISTINCT IFNULL(a.bridinput,'') bridinput from dbmaster.t_suratdana_br1 a WHERE a.idinput='$nidinput') order by noslip, brId";
                                                    $tampil = mysqli_query($cnmy, $query);

                                                    while ($zs= mysqli_fetch_array($tampil)) {
                                                        $pbrid=$zs['brId'];
                                                        $pnoslip=$zs['noslip'];

                                                        $pjumlah=$zs['jumlah'];
                                                        if (!empty($pjumlah)) $pjumlah=number_format($pjumlah,0);

                                                        $pdataket = "$pbrid &nbsp; - &nbsp; $pnoslip   &nbsp;&nbsp;&nbsp;&nbsp;    (Rp. $pjumlah)";
                                                        
                                                        if ($pbrid==$pbrnoid)
                                                            echo "<option value='$pbrid' selected>$pdataket</option>";
                                                        else
                                                            echo "<option value='$pbrid'>$pdataket</option>";
                                                    }
                                                    */
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                                
                                <!-- sub jenis sebenarnya-->
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' onchange="ShowDebitKreditJenis()">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                                $query = "select distinct kodeid, subkode, subnama from dbmaster.t_kode_spd where ibank='Y' order by subkode";

                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    if ($z['subkode']==$psubkode)
                                                        echo "<option value='$z[subkode]' selected>$z[subkode] - $z[subnama]</option>";
                                                    else
                                                        echo "<option value='$z[subkode]'>$z[subkode] - $z[subnama]</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="div_nonspd">
                                
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_jenis' name='cb_jenis' onchange="">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                $pjns_sel1="selected";
                                                $pjns_sel2="";
                                                $pjns_sel5="";
                                                if ($pjenis=="2") $pjns_sel2="selected";
                                                if ($pjenis=="5") $pjns_sel5="selected";

                                                echo "<option value='1' $pjns_sel1>Advance</option>";
                                                echo "<option value='2' $pjns_sel2>Klaim</option>";
                                                echo "<option value='5' $pjns_sel5>Bank</option>";
                                                ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>COA <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_coa' name='cb_coa' onchange="">
                                                <!--<option value='' selected>-- Pilihan --</option>-->
                                                <?PHP
                                                $query = "select a.coa, b.NAMA4 FROM dbmaster.coa_dana_bank a JOIN "
                                                        . " dbmaster.coa_level4 b on a.coa=b.COA4 order by a.coa";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    if ($z['coa']==$pcoa)
                                                        echo "<option value='$z[coa]' selected>$z[coa] - $z[NAMA4]</option>";
                                                    else
                                                        echo "<option value='$z[coa]'>$z[coa] - $z[NAMA4]</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>



                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Pengajuan <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER') ";
                                                if ($_SESSION['DIVISI']=="OTC") {
                                                    $query .=" AND DivProdId = 'OTC' ";
                                                }
                                                $query .=" order by DivProdId";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    if ($z['DivProdId']==$divisi)
                                                        echo "<option value='$z[DivProdId]' selected>$z[DivProdId]</option>";
                                                    else
                                                        echo "<option value='$z[DivProdId]'>$z[DivProdId]</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_sts' name='cb_sts' onchange="">
                                            <?PHP
                                            if ($pstatus=="1") {
                                                echo "<option value='1' selected>Setoran (Tunai)</option>";
                                                echo "<option value='2'>Retur Bank</option>";
                                            }elseif ($pstatus=="2") {
                                                echo "<option value='1'>Setoran (Tunai)</option>";
                                                echo "<option value='2' selected>Retur Bank</option>";
                                            }else{
                                                echo "<option value='1'>Setoran (Tunai)</option>";
                                                echo "<option value='2'>Retur Bank</option>";
                                                echo "<option value='3' selected></option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Debit/Kredit <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_debitkredit' name='cb_debitkredit' onchange="ShowCoaPilihJenis()">
                                            <?PHP
                                            $pdebker_sel1="selected";
                                            $pdebker_sel2="";
                                            if ($pd_spd_debker=="K") $pdebker_sel2="selected";

                                            echo "<option value='D' $pdebker_sel1>Debit</option>";
                                            echo "<option value='K' $pdebker_sel2>Kredit</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Jumlah
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jml' name='e_jml' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $p_bnkjumlah; ?>'>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_ket' name='e_ket' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>'>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>
                    

                </div>
            </div>
            

        </form>
        
    </div>
    <!--end row-->
</div>


<script type="text/javascript">
        
    $(document).ready(function() {
        ShowEnableDisableSPD();
        //ShowAwal();
        
        <?PHP if ($_GET['act']=="tambahbaru") { ?>
                var eket = document.getElementById('cb_darispd').value;
                if (eket=="Y") {
                    ShowAwal();
                }
        <?PHP } ?>
            
    } );
        
    $(function() {
        $('#e_tglspd').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'MM yy',
            onSelect: function(dateStr) {

            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));

                ShowDataNoSPD();
                ShowDataNoDivisiBR();
            },

            beforeShow: function() {
                if ((selDate = $(this).val()).length > 0) 
                {
                    iYear = selDate.substring(selDate.length - 4, selDate.length);
                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                }
            }
        });
    });
    

    function ShowAwal() {
        ShowEnableDisableSPD();
        ShowDataNoSPD();
        ShowDataNoDivisiBR();
    }

    function ShowEnableDisableSPD() {
        var eket = document.getElementById('cb_darispd').value;
        var elespd = document.getElementById("div_spd");
        var elenon = document.getElementById("div_nonspd");
        if (eket=="Y") {
            elespd.classList.remove("disabledDiv");
            elenon.classList.add("disabledDiv");
        }else{
            elespd.classList.add("disabledDiv");
            elenon.classList.remove("disabledDiv");
        }
    }

    function HapusBulanSPD() {
        var chkinp=document.getElementById('chk_pilihbln');
        if (chkinp.checked == true){
            document.getElementById("e_tglspd").disabled = false;
        }else{
            document.getElementById("e_tglspd").disabled = true;
        }

        ShowDataNoSPD();
        ShowDataNoDivisiBR();
    }

    function ShowDataNoSPD() {
        var chkinp=document.getElementById('chk_pilihbln');
        if (chkinp.checked == true){
            var itglspd = document.getElementById('e_tglspd').value;
        }else{
            var itglspd = "";
        }

        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=viewnomorspd",
            data:"utglspd="+itglspd,
            success:function(data){
                $("#cb_nospd").html(data);
            }
        });
    }


    function ShowDataNoDivisiBR() {
        var chkinp=document.getElementById('chk_pilihbln');
        if (chkinp.checked == true){
            var itglspd = document.getElementById('e_tglspd').value;
        }else{
            var itglspd = "";
        }

        var inomor = document.getElementById('cb_nospd').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=viewnomorbrdivis",
            data:"unomor="+inomor+"&utgl="+itglspd,
            success:function(data){
                $("#cb_nodivisi").html(data);
            }
        });
    }

    function ShowDataNoBRSlip() {
        document.getElementById('e_idnobr').value="";
        document.getElementById('e_noslip').value="";
        return false; 
        
        var inodivisi = document.getElementById('cb_nodivisi').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=viewbrnoslip",
            data:"unodiv="+inodivisi,
            success:function(data){
                $("#cb_nobrslip").html(data);
            }
        });
    }

    function ShowDebitKreditJenis() {
        var ikodesub = document.getElementById('cb_kodesub').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=viewjenisdebitkredit",
            data:"ukodesub="+ikodesub,
            success:function(data){
                $("#cb_debitkredit").html(data);
                ShowCoaPilihJenis();
                ShowJenisPilihJenisSub();
            }
        });
    }

    function ShowJenisPilihJenisSub() {
        var ikodesub = document.getElementById('cb_kodesub').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=viewjenispilihsubjenis",
            data:"ukodesub="+ikodesub,
            success:function(data){
                $("#cb_jenis").html(data);
            }
        });
    }
    
    function ShowCoaPilihJenis() {
        var ikodesub = document.getElementById('cb_kodesub').value;
        var idebker = document.getElementById('cb_debitkredit').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=viewcoapilihjenis",
            data:"ukodesub="+ikodesub+"&udebker="+idebker,
            success:function(data){
                $("#cb_coa").html(data);
            }
        });
    }
    
    
    function getDataBRSlip(data1, data2){
        var inospd=document.getElementById('cb_nospd').value;
        var inodivisi=document.getElementById('cb_nodivisi').value;
        $.ajax({
            type:"post",
            url:"config/viewdata.php?module=viewdatabrperdivisi&data1="+data1+"&data2="+data2,
            data:"udata1="+data1+"&udata2="+data2+"&unospd="+inospd+"&unodivisi="+inodivisi,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    function getDataModalBRSlip(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
    }

    function disp_confirm(pText_,ket)  {

        var etgl_cls = document.getElementById('e_tgl_cls').value;
        if (etgl_cls!=""){
            alert("Periode (Tgl. Transaksi) yang diisi sudah proses closing...\n\
                Tidak bisa tambah dan edit data.\n\
                Silakan isi Tgl. Transaksi lain...!!!");
            return false;
        }
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_danabank/aksi_danabank.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    
    $('#mytgl01').on('change dp.change', function(e){
        getPeriodeClosing();
    });
    
    function getPeriodeClosing(){
        var etgl = document.getElementById('e_tglmasuk').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=cariprosesclosing",
            data:"utgl="+etgl,
            success:function(data){
                document.getElementById('e_tgl_cls').value=data;
            }
        });
    }
    
</script>
    

<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
</style>


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    .custom-combobox {
        position: relative;
        display: inline-block;
    }
    .custom-combobox-toggle {
        position: absolute;
        top: 0;
        bottom: 0;
        margin-left: -1px;
        padding: 0;
    }
    .custom-combobox-input {
        margin: 0;
        padding: 5px 10px;
        width:300px;
    }
</style>
<script src="js/select_combo.js"></script>
<script>
    $( function() {
        $( "#cb_nodivisi" ).combobox();
    } );
</script>