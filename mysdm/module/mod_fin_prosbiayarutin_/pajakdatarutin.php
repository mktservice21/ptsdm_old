<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    #nwinbaru .form-group, #nwinbaru .input-group, #nwinbaru .control-label {
        margin-bottom:3px;
    }
    #nwinbaru .control-label {
        font-size:12px;
    }
    #nwinbaru input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    #nwinbaru select.soflow {
        font-size:12px;
        height: 30px;
    }
    #nwinbaru .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    #nwinbaru .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
</style>

<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/library.php";
    $act="input";
    $aksi="";
    $hari_ini = date("Y-m-d");
    
    $pidbrno=$_POST['uidbr'];
    $pidinput="";
    
    
    $sql = "SELECT * FROM dbmaster.v_brrutin0 WHERE idrutin='$pidbrno'";
    $tampil= mysqli_query($cnmy, $sql);
    $row= mysqli_fetch_array($tampil);
    $pptgl=$row['tgl'];
    $tgl1 = date('d/m/Y', strtotime($pptgl));
    $pjumlah=$row['jumlah'];
    
    $pkaryawan=$row['karyawanid'];
    $nama=$row['nama'];
    if ($_SESSION['KRYNONE']==$pkaryawan) $nama=$row["nama_karyawan"];
                
    $phari=date("w", strtotime($row['tgl']));
    $pdate=date("d", strtotime($row['tgl']));
    $pbln=(int)date("m", strtotime($row['tgl']));
    $pthn=date("Y", strtotime($row['tgl']));

    $tglpengajuan=$seminggu[$phari]." ".$pdate." ".$nama_bln[$pbln]." ".$pthn;

    $phari1=date("w", strtotime($row['periode1']));
    $pdate1=date("d", strtotime($row['periode1']));
    $pbln1=(int)date("m", strtotime($row['periode1']));
    $pthn1=date("Y", strtotime($row['periode1']));

    $phari2=date("w", strtotime($row['periode2']));
    $pdate2=date("d", strtotime($row['periode2']));
    $pbln2=(int)date("m", strtotime($row['periode2']));
    $pthn2=date("Y", strtotime($row['periode2']));

    //$pp01 =  date("d F Y", strtotime($row['periode1']));
    //$pp02 =  date("d F Y", strtotime($row['periode2']));

    $pp01=$pdate1." ".$nama_bln[$pbln1]." ".$pthn1;
    $pp02=$pdate2." ".$nama_bln[$pbln2]." ".$pthn2;
    
    $pbridpilih="08";
    $nbridpilih="";
    
    $prptotal="";
    $pjumlahrpusul="";
    
    $pjmldpp=0;
    $pjmlppn="";
    $pjmlrpppn=0;
    $pjnspph="";
    $pjmlpph=5;
    $pjmlrppph=0;
    $pjmlbulat=0;
    $pjmlmaterai=0;
    $ptglfakturpajak = date('d/m/Y', strtotime($hari_ini));
    $pnoseripajak="";
    $pkenapajak="";
    $prpjumlahjasa="";
    $pchkjasa="";
    $pchkatrika="";
?>


    <!--input mask -->
    <script src="js/inputmask.js"></script>


    
<script> window.onload = function() { document.getElementById("e_jumlah").focus(); } </script>
<div id="nwinbaru">
<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Isi Data Pajak</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=entrybrotcpajak&act=input&idmenu=87"; ?>' id='demo-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_brid' name='e_brid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbrno; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_karyawan' name='e_karyawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nama; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_periode' name='e_periode' class='form-control col-md-7 col-xs-12' value='<?PHP echo "$pp01 s/d. $pp02"; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah USUL Rp. <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jumlahminta' name='e_jumlahminta' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <select class='form-control input-sm' id='e_idinput' name='e_idinput' onchange="disp_edit_data('cari', 'e_idinput', '<?PHP echo $pidbrno; ?>', '1')">
                                                <?PHP
                                                    $sql = "SELECT DISTINCT a.nourut, a.nobrid, b.nama FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brid b on a.nobrid=b.nobrid WHERE a.idrutin='$pidbrno' order by 2,1";
                                                    $tampil= mysqli_query($cnmy, $sql);
                                                    echo "<option value='' selected>-- Pilih --</option>";
                                                    while ($nr= mysqli_fetch_array($tampil)) {
                                                        $nnourut=$nr['nourut'];
                                                        $nnobrid=$nr['nobrid'];
                                                        $nnmbrid=$nr['nama'];
                                                        if ($nnobrid==$pbridpilih) {
                                                            echo "<option value='$nnourut' selected>$nnmbrid</option>";
                                                            $nbridpilih=$nnourut;
                                                        }else{
                                                            echo "<option value='$nnourut'>$nnmbrid</option>";
                                                        }
                                                    }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <?PHP
                                        if (!empty($nbridpilih)) {
                                            $sql = "SELECT * FROM dbmaster.t_brrutin1 WHERE idrutin='$pidbrno' AND nourut='$nbridpilih'";// AND IFNULL(pajak,'')='Y'
                                            $tampil= mysqli_query($cnmy, $sql);
                                            $row= mysqli_fetch_array($tampil);
                                            $ppajak=$row['pajak'];
                                            if ($ppajak=="Y") {
                                                $pjmldpp=$row['dpp'];
                                                $pjmlppn=$row['ppn'];
                                                $pjmlrpppn=$row['ppn_rp'];
                                                $pjnspph=$row['pph_jns'];
                                                $pjmlpph=$row['pph'];
                                                $pjmlrppph=$row['pph_rp'];
                                                $pjmlbulat=$row['pembulatan'];
                                                $pjmlmaterai=$row['materai_rp'];
                                                $ptglfakturpajak="";
                                                if (!empty($row['tgl_fp']) AND $row['tgl_fp']<>"0000-00-00") $ptglfakturpajak = date('d/m/Y', strtotime($row['tgl_fp']));
                                                $pnoseripajak=$row['noseri'];
                                                $pkenapajak=$row['nama_pengusaha'];
                                                $prpjumlahjasa=$row['jasa_rp'];
                                                $pjumlahrpusul=$row['jumlah'];
                                            }
                                            $prptotal=$row['rptotal'];

                                            //if ((double)$pjmldpp==0) $pjmldpp=$prptotal;
                                            //if ((double)$pjumlahrpusul==0) $pjumlahrpusul=$prptotal;
                                        }
                                        ?>

                                        
                                        
                                <div id="n_input_div">
                                    
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Rp. <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_rptotal' name='e_rptotal' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prptotal; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pajak <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <select class='form-control input-sm' id='cb_pajak' name='cb_pajak' onchange="">
                                                    <option value='Y' selected>Y</option>
                                                    <option value='N'>N</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Pengusaha Kena Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_kenapajak' name='e_kenapajak' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pkenapajak; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">No Seri Faktur Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_noserifp' name='e_noserifp' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pnoseripajak; ?>'>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Tgl Faktur Pajak </label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div class='input-group date' id='mytgl01'>
                                                <input type="text" class="form-control" id='mytgl05' name='e_tglpajak' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglfakturpajak; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                        
                                        
                                        
                                    <!--- untuk jasa -->
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">&nbsp;<span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div hidden>
                                                <input type="checkbox" value="jasa" id="chk_jasa" name="chk_jasa" onclick="cekBoxPilihDPP('chk_jasa')" <?PHP echo $pchkjasa; ?>> DPP Dari Jumlah Awal 
                                                <br/>
                                            </div>
                                            <input type="checkbox" value="atrika" id="chk_atrika" name="chk_atrika" onclick="cekBoxPilihDPP('chk_atrika')" <?PHP echo $pchkatrika; ?>> DPP Khusus
                                        </div>
                                    </div>
                                    

                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Jumlah Awal (Rp.) <span clasJumlah Awal (Rp.)s='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_rpjmljasa' name='e_rpjmljasa' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpjumlahjasa; ?>' onblur="HitungJumlahDPP()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                        
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">DPP (Rp.) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmldpp' name='e_jmldpp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmldpp; ?>' onblur="HitungJumlah()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">PPN (%) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlppn' name='e_jmlppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlppn; ?>' onblur="HitungPPN()">
                                            <input type='hidden' id='e_jmlrpppn' name='e_jmlrpppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrpppn; ?>' Readonly>
                                        </div><!--disabled='disabled'-->
                                    </div>
                                        
                                        
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">PPH <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <div style="margin-bottom:2px;">
                                                <select class='soflow' name='cb_pph' id='cb_pph' onchange="ShowPPH()">
                                                    <?php
                                                    if ($pjnspph=="pph21") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21' selected>PPH21</option>";
                                                        echo "<option value='pph23'>PPH23</option>";
                                                    }elseif ($pjnspph=="pph23") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21'>PPH21</option>";
                                                        echo "<option value='pph23' selected>PPH23</option>";
                                                    }else{
                                                        echo "<option value='' selected></option>";
                                                        echo "<option value='pph21'>PPH21</option>";
                                                        echo "<option value='pph23'>PPH23</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <input type='hidden' id='e_jmlpph' name='e_jmlpph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlpph; ?>' readonly>
                                                <input type='hidden' id='e_jmlrppph' name='e_jmlrppph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrppph; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>
                                        
                                        
                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Pembulatan <span class='required'></span></label>
                                            <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <input type='text' id='e_jmlbulat' name='e_jmlbulat' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlbulat; ?>' onblur="HitungJumlahUsulan()">
                                            </div><!--disabled='disabled'-->
                                        </div>


                                        <div hidden>
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Biaya Materai (Rp.) <span class='required'></span></label>
                                                <div class='col-md-6 col-sm-6 col-xs-12'>
                                                    <input type='text' id='e_jmlmaterai' name='e_jmlmaterai' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlmaterai; ?>' onblur="HitungJumlahUsulan()">
                                                </div><!--disabled='disabled'-->
                                            </div>
                                        </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlahrpusul; ?>'>
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' id='nm_btn_save' class='btn btn-success' onclick='disp_confirm_pajak("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    
                                </div>
                                        


                                    </div>

                                </div>
                            </div>


                        </div>
                    </div>


                </form>
                
                
                <div class='col-md-12 col-sm-12 col-xs-12' style="">
                    <div class='x_content'>
                        <div class='x_panel'>
                        <div id='xxpanel'>
                            <table id='dtableviewpajak' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='5px'>NO</th>
                                        <th width='20px'></th>
                                        <th width='20px' nowrap>ID</th>
                                        <th width='20px'>JUMLAH USUL</th>
                                        <th width='20px'>DPP</th>
                                        <th width='20px'>PPN</th>
                                        <th width='20px'>PPN RP</th>
                                        <th width='20px'>PPH</th>
                                        <th width='20px'>PPH RP</th>
                                        
                                        <th width='20px'>PENGUSAHA</th>
                                        <th width='20px'>NOSERI</th>
                                        <th width='20px'>TGL. FP</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?PHP
                                        $query = "select a.*, b.nama nama_brid from dbmaster.t_brrutin1 a "
                                                . " JOIN dbmaster.t_brid b on a.nobrid=b.nobrid WHERE a.idrutin='$pidbrno' AND IFNULL(a.pajak,'')='Y' ";
                                        $query .=" order by b.nama";
                                        $tampil=mysqli_query($cnmy, $query) or die("mydata.php: get data");
                                        $no=1;
                                        while( $row=mysqli_fetch_array($tampil) ) {  // preparing an array
                                            
                                            $ni_idinput=$row['nourut'];
                                            $ni_brid=$row['idrutin'];
                                            $ni_jumlah = $row["rptotal"];
                                            $ni_nmpengusaha = $row["nama_pengusaha"];
                                            $ni_noseri = $row["noseri"];
                                            $ni_tglfp = $row["tgl_fp"];
                                            $ni_jumlahawal = $row["jasa_rp"];
                                            $ni_dpp = $row["dpp"];
                                            $ni_ppn = $row["ppn"];
                                            $ni_ppnrp = $row["ppn_rp"];
                                            $ni_pphjns = $row["pph_jns"];
                                            $ni_pph = $row["pph"];
                                            $ni_pphrp = $row["pph_rp"];
                                            
                                            
                                            
                                            if (!empty($ni_tglfp) AND $ni_tglfp<>"0000-00-00") $ni_tglfp= date("d/m/Y", strtotime($ni_tglfp));
                                            
                                            $ni_jumlah=number_format($ni_jumlah,0,",",",");
                                            $ni_jumlahawal=number_format($ni_jumlahawal,0,",",",");
                                            $ni_dpp=number_format($ni_dpp,0,",",",");
                                            $ni_ppnrp=number_format($ni_ppnrp,0,",",",");
                                            $ni_pphrp=number_format($ni_pphrp,0,",",",");
                                            
                                            $ni_hapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"disp_hapusdata_pajak('Hapus Data..?', '$ni_idinput', '$pidbrno')\">";
                                            $ni_edit="<input type='button' value='Edit' class='btn btn-success btn-xs' onClick=\"disp_edit_data('Edit ..?', '$ni_idinput', '$pidbrno', '2')\">";
                                            
                                            if ($ni_idinput=="0") {
                                                $ni_hapus="";
                                                $ni_edit="";
                                            }
                                            
                                            echo "<tr>";
                                            echo "<td nowrap>$no</td>";
                                            echo "<td nowrap>$ni_edit $ni_hapus</td>";
                                            echo "<td nowrap>$ni_brid</td>";
                                            echo "<td nowrap align='right'>$ni_jumlah</td>";
                                            
                                            echo "<td nowrap align='right'>$ni_dpp</td>";
                                            echo "<td nowrap align='right'>$ni_ppn</td>";
                                            echo "<td nowrap align='right'>$ni_ppnrp</td>";
                                            echo "<td nowrap align='right'>$ni_pphjns</td>";
                                            echo "<td nowrap align='right'>$ni_pphrp</td>";
                                            
                                            echo "<td nowrap>$ni_nmpengusaha</td>";
                                            echo "<td nowrap>$ni_noseri</td>";
                                            echo "<td nowrap>$ni_tglfp</td>";
                                            
                                            echo "</tr>";
                                            $no++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                            
                        </div>
                        </div>
                    </div>
                </div>
                
                
                
            </div>
            <!--end row-->
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
    </div>
</div>
</div>

<!-- jquery.inputmask -->
<script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
<!-- Custom Theme Scripts -->


<script>
    
    $(document).ready(function() {
        var dataTable = $('#dtableviewpajak').DataTable( {
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": false,
            "bInfo": false,
            "ordering": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3,4,5,6,7,8] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9,10,11] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            
            /*,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true*/
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    $('#mytgl01, #mytgl02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
    
    
    function disp_confirm_pajak(pText_,nid)  {
        var eact="inputdatapajak";
        var eidbr = document.getElementById("e_brid").value;
        var eidinput = document.getElementById("e_idinput").value;
        var cbpajak = document.getElementById("cb_pajak").value;
        var ekenapajak = document.getElementById("e_kenapajak").value;
        var enoserifp = document.getElementById("e_noserifp").value;
        var etglfp = document.getElementById("mytgl05").value;
        var erpjmljasa = document.getElementById("e_rpjmljasa").value;
        var ejmldpp = document.getElementById("e_jmldpp").value;
        var ejmlppn = document.getElementById("e_jmlppn").value;
        var ejmlrpppn = document.getElementById("e_jmlrpppn").value;
        var cbpph = document.getElementById("cb_pph").value;
        var ejmlpph = document.getElementById("e_jmlpph").value;
        var ejmlrppph = document.getElementById("e_jmlrppph").value;
        var ejmlbulat = document.getElementById("e_jmlbulat").value;
        var ejmlmaterai = document.getElementById("e_jmlmaterai").value;
        var ejmlusulan = document.getElementById("e_jmlusulan").value;
        
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                $.ajax({
                    type:"post",
                    url:"module/mod_fin_prosbiayarutin/simpan_pajakrutin.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidbr="+eidbr+"&uidinput="+eidinput+"&cbpajak="+cbpajak+"&ekenapajak="+ekenapajak+"&enoserifp="+enoserifp+
                         "&etglfp="+etglfp+"&erpjmljasa="+erpjmljasa+"&ejmldpp="+ejmldpp+"&ejmlppn="+ejmlppn+"&ejmlrpppn="+ejmlrpppn+
                         "&cbpph="+cbpph+"&ejmlpph="+ejmlpph+"&ejmlrppph="+ejmlrppph+"&ejmlbulat="+ejmlbulat+"&ejmlmaterai="+ejmlmaterai+
                         "&ejmlusulan="+ejmlusulan,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                        nm_btn_save.style.display='none';
                        $('#myModal').modal('hide');
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }

    
    function disp_edit_data(pText_,nid, nidrutin, npilih)  {
        if (npilih=="1") {
            var enourut = document.getElementById(nid).value;
        }else{
            var enourut = nid;
        }
        $.ajax({
            type:"post",
            url:"module/mod_fin_prosbiayarutin/viewdata.php?module=caridatapajak",
            data:"unourut="+enourut+"&uidrutin="+nidrutin,
            success:function(data){
                $("#n_input_div").html(data);
            }
        });
    }
    
    
    function disp_hapusdata_pajak(pText_,nid, nidrutin)  {
        //alert(nid); return false;
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                $.ajax({
                    type:"post",
                    url:"module/mod_fin_prosbiayarutin/simpan_pajakrutin.php?module="+module+"&act=hapus&idmenu="+idmenu,
                    data:"uidbr="+nid+"&uidrutin="+nidrutin,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                        $('#myModal').modal('hide');
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>

<script>
    
    function ShowPajak(){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        
        var epajak = document.getElementById('cb_pajak').value;

        if (epajak=="" || epajak=="N"){
            n_pajak1.style.display = 'none';
        }else{
            n_pajak1.style.display = 'block';
            if (nact!="editdata") {
                ShowInputJasa();
            }
        }
        
        
        
        //document.getElementById('e_kenapajak').focus();
        /*
        if (epajak==""){
            n_pajak.classList.add("disabledDiv");
        }else{
            n_pajak.classList.remove("disabledDiv");
        }
        */
    }
    
    function cekBoxPilihDPP(nmcekbox){
        var nm = document.getElementById(nmcekbox);
        var chkjasa = document.getElementById('chk_jasa');
        var chkatrika = document.getElementById('chk_atrika');
        if (nm.checked) {
            if (nm.value=="jasa") {
                chkatrika.checked='';
            }else if (nm.value=="atrika") {
                chkjasa.checked='';
            }
        }
        ShowInputJasa();
    }
    
    function ShowInputJasa(){
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        if (echkjasa==true || echkatrika==true) {
            n_pajakjasa.style.display = 'block';
        }else{
            n_pajakjasa.style.display = 'none';
        }
        HitungJumlah();
    }
    
    
    function HitungJumlahDPP(){
        var newchar = '';
        var e_totrpdpp = "0";
        erpjmldpp = document.getElementById("e_rpjmljasa").value;
        if (erpjmldpp!="" && erpjmldpp != "0") {
            var nrpjmldpp = erpjmldpp; 
            nrpjmldpp = nrpjmldpp.split(',').join(newchar);
            e_totrpdpp=nrpjmldpp*10/100;
        }
        document.getElementById("e_jmldpp").value=e_totrpdpp;
        HitungJumlah();
    }
    
    
    function HitungJumlah(){
        HitungPPN();
        HitungPPH();
        HitungJumlahUsulan();
    }

    function HitungPPN(){
        var newchar = '';
        var e_totrpppn = "0";

        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        ejmldpp = document.getElementById("e_jmldpp").value;
        if (ejmldpp!="" && ejmldpp != "0") {
            var njmldpp = ejmldpp; 
            njmldpp = njmldpp.split(',').join(newchar);

            eppn = document.getElementById("e_jmlppn").value;
            if (eppn!="" && eppn != "0") {
                var nppn = eppn; 
                nppn = nppn.split(',').join(newchar);
                
                //khusus
                if (echkjasa==true || echkatrika==true) {
                    njmldpp=erpjmljasa;
                }
                e_totrpppn = njmldpp * nppn / 100;
            }

        }

        document.getElementById("e_jmlrpppn").value = e_totrpppn;
        HitungPPH();
    }

    function ShowPPH(){
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlrppph").value = "0";
        

        
        var epph = document.getElementById("cb_pph").value;
        if (epph=="pph21") {
            document.getElementById("e_jmlpph").value = "5";
            HitungPPH();
        }else if (epph=="pph23") {
            document.getElementById("e_jmlpph").value = "2";
            HitungPPH();
        }else{
            document.getElementById("e_jmlpph").value = "0";
            document.getElementById("e_jmlrppph").value = "0";
            HitungJumlahUsulan();
        }
    }
    
    
    function HitungPPH(){
        var newchar = '';
        
        
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        var e_totrppph = "0";
        var epph = document.getElementById("cb_pph").value;
        
        if (epph!="") {
            ejmldpp = document.getElementById("e_jmldpp").value;
            if (ejmldpp!="" && ejmldpp != "0") {
                var njmldpp = ejmldpp; 
                njmldpp = njmldpp.split(',').join(newchar);

                
                var idpp_pilih=njmldpp;
                if (echkatrika==true) {
                    //idpp_pilih=erpjmljasa;
                }
                
                e_totrppph = idpp_pilih;
                
                if (epph=="pph21") {
                    npph = "5";
                    e_totrppph = (idpp_pilih * npph / 100)*50/100;   
                }else if (epph=="pph23") {
                    npph = "2";
                    e_totrppph = (idpp_pilih * npph / 100);
                }
            }
        }
        document.getElementById("e_jmlrppph").value = e_totrppph;
        HitungJumlahUsulan();
    }


    function HitungJumlahUsulan(){

        var newchar = '';
        
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        ejmldpp = document.getElementById("e_jmldpp").value;
        var e_totrpusulan = ejmldpp;
        erpppn = document.getElementById("e_jmlrpppn").value;
        erppph = document.getElementById("e_jmlrppph").value;
        erpbulat = document.getElementById("e_jmlbulat").value;
        erpmaterai = document.getElementById("e_jmlmaterai").value;
        if (erpppn=="") erpppn="0";
        if (erppph=="") erppph="0";
        if (erpbulat=="") erpbulat="0";
        if (erpmaterai=="") erpmaterai="0";

        var epph = document.getElementById("cb_pph").value;

        var njmldpp = ejmldpp; 
        njmldpp = njmldpp.split(',').join(newchar);

        var nrpppn = erpppn; 
        nrpppn = nrpppn.split(',').join(newchar);

        var nrppph = erppph; 
        nrppph = nrppph.split(',').join(newchar);

        var nrpbulat = erpbulat; 
        nrpbulat = nrpbulat.split(',').join(newchar);

        var nrpmaterai = erpmaterai; 
        nrpmaterai = nrpmaterai.split(',').join(newchar);
        
        var idpp_pilih=njmldpp;
        /*if (echkjasa==true) {
            idpp_pilih=erpjmljasa;
        }*/
        
        if (epph=="pph21" || epph=="pph23") {
            e_totrpusulan=( ( parseInt(idpp_pilih)+parseInt(nrpppn) - parseInt(nrppph) ) );
        }else{
            e_totrpusulan=( ( parseInt(idpp_pilih)+parseInt(nrpppn)));
        }
        e_totrpusulan=parseInt(e_totrpusulan)+parseInt(nrpbulat)+parseInt(nrpmaterai);
        
        
        
        if (echkjasa==true) {
            e_totrpusulan=parseInt(e_totrpusulan);//-parseInt(njmldpp)
            e_totrpusulan=parseInt(erpjmljasa)+parseInt(e_totrpusulan);
        }else if (echkatrika==true) {
            e_totrpusulan=parseInt(e_totrpusulan)-parseInt(njmldpp);
            e_totrpusulan=parseInt(erpjmljasa)+parseInt(e_totrpusulan);
        }
        
        
        document.getElementById("e_jmlusulan").value = e_totrpusulan;

    }
</script>

<style>
    .divnone {
        display: none;
    }
    #dtableviewpajak th {
        font-size: 13px;
    }
    #dtableviewpajak td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
    
        #nwinbaru #dtableviewpajak input[type=text], #nwinbaru #tabelnobr input[type=text] {
            box-sizing: border-box;
            color:#000;
            font-size:11px;
            height: 25px;
        }
        #nwinbaru select.soflow {
            font-size:12px;
            height: 30px;
        }
        #nwinbaru .disabledDiv {
            pointer-events: none;
            opacity: 0.4;
        }

        #nwinbaru table.dtableviewpajak, #nwinbaru table.tabelnobr {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 100%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 0px solid #000;
        }

        #nwinbaru table.dtableviewpajak td, #nwinbaru table.tabelnobr td {
            border: 1px solid #000; /* No more visible border */
            height: 10px;
            transition: all 0.1s;  /* Simple transition for hover effect */
        }

        #nwinbaru table.dtableviewpajak th, #nwinbaru table.tabelnobr th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        #nwinbaru table.dtableviewpajak td, #nwinbaru table.tabelnobr td {
            background: #FAFAFA;
        }

        /* Cells in even rows (2,4,6...) are one color */
        #nwinbaru tr:nth-child(even) #nwinbaru td { background: #F1F1F1; }

        /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
        #nwinbaru tr:nth-child(odd) #nwinbaru td { background: #FEFEFE; }

        #nwinbaru tr td:hover.biasa { background: #666; color: #FFF; }
        #nwinbaru tr td:hover.left { background: #ccccff; color: #000; }

        #nwinbaru tr td.center1, #nwinbaru td.center2 { text-align: center; }

        #nwinbaru tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
        #nwinbaru tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
        /* Hover cell effect! */
        #nwinbaru tr td {
            padding: -10px;
        }
</style>