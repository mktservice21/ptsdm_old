<?php

    session_start();
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_combo.php";
    $act="input";
    $aksi="";
    
    $pdivisipilih=$_POST['udivisi'];
    $pidbr=$_POST['uidbr'];
    
    $ptglpilih1=$_POST['utgl1'];
    $ptglpilih2=$_POST['utgl2'];
    $pviapilih=$_POST['uvia'];
    $ppajakpilih=$_POST['upajak'];
    $pnodivisi=$_POST['unodivisi'];
    
    
    
    $hari_ini = date("Y-m-d");
    $tgl1 = date('Y-m-d', strtotime($hari_ini));
    
    $pketerangan="";
    $pjumlah=0;
    $pjumlahminta=0;
    $ni_nobukti="";
    
    $pjmlawal=1;
    
    
    if ($pdivisipilih=="OTC")
        $query = "select jumlah from hrd.br_otc WHERE brOtcId='$pidbr'";
    elseif ($pdivisipilih=="KD")
        $query = "select jumlah from hrd.klaim WHERE klaimId='$pidbr'";
    else
        $query = "select jumlah from hrd.br0 WHERE brId='$pidbr'";
    
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pjumlahminta=$row['jumlah'];
    
    
    $nm_tabel_pilih=" dbmaster.t_br0_via_sby ";
    if ($pdivisipilih=="OTC") $nm_tabel_pilih=" dbmaster.t_br_otc_via_sby ";
    if ($pdivisipilih=="KD") $nm_tabel_pilih=" dbmaster.t_klaim_via_sby ";

    $query = "SELECT distinct tgltermin, tgltransfersby FROM $nm_tabel_pilih WHERE bridinput='$pidbr'";
    $tampil_= mysqli_query($cnmy, $query);
    $pjmlawal= mysqli_num_rows($tampil_);
    if ($pjmlawal<=0) $pjmlawal=1;
    
    
                                        
?>

<!--input mask -->
<script src="js/inputmask.js"></script>
<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Isi Transfer BR Via Surabaya</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=brdanabank&act=input&idmenu=258"; ?>' id='demo-form5' name='form5' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID BR <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                                <input type='hidden' id='e_divisi_p' name='e_divisi_p' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisipilih; ?>' Readonly>
                                                <input type='hidden' id='e_tgl1_p' name='e_tgl1_p' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptglpilih1; ?>' Readonly>
                                                <input type='hidden' id='e_tgl2_p' name='e_tgl2_p' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptglpilih2; ?>' Readonly>
                                                <input type='hidden' id='e_via_p' name='e_via_p' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pviapilih; ?>' Readonly>
                                                <input type='hidden' id='e_pajak_p' name='e_pajak_p' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ppajakpilih; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_nodivisi' name='e_nodivisi' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnodivisi; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Minta <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jmlminta' name='e_jmlminta' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlahminta; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah / Transfer <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <select class='form-control' id="cb_jml" name="cb_jml" onchange="TampilkanJumlahTransfer('<?PHP echo $pidbr; ?>', '<?PHP echo $pdivisipilih; ?>', 'cb_jml')">
                                                    <?PHP
                                                    for ($nx=1;$nx<=10;$nx++) {
                                                        if ($nx==$pjmlawal)
                                                            echo "<option value='$nx' selected>$nx</option>";
                                                        else
                                                            echo "<option value='$nx'>$nx</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                        </div>
                                        
                                        
                                        <div id="div_jml_trans">
                                            <?PHP
                                            $nm_tabel_pilih=" dbmaster.t_br0_via_sby ";
                                            if ($pdivisipilih=="OTC") $nm_tabel_pilih=" dbmaster.t_br_otc_via_sby ";
                                            if ($pdivisipilih=="KD") $nm_tabel_pilih=" dbmaster.t_klaim_via_sby ";

                                            $sql = "SELECT * FROM $nm_tabel_pilih ";
                                            $sql.=" WHERE bridinput='$pidbr' order by tgltermin, tgltransfersby, jumlah";
                                            $query=mysqli_query($cnmy, $sql) or die("error");
                                            $ketemu= mysqli_num_rows($query);
                                            if ($ketemu>0) {
                                                $ix=1;
                                                
                                                $warna1=" style='color:black;' ";
                                                $warna2=" style='color:blue;' ";
                                                $warna=$warna2;
                                                $nwarna1=false;
                                                
                                                while( $row=mysqli_fetch_array($query) ) {  // preparing an array
                                                    $ni_tgltermin=$row['tgltermin'];
                                                    $ni_tgltrans = $row["tgltransfersby"];
                                                    $ni_jumlah = $row["jumlah"];
                                                    $ni_nobukti = $row["nobukti"];
                                                ?>
                                                    <div class='form-group'>
                                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' <?PHP echo $warna; ?>>Tgl. Termin <?PHP echo $ix; ?> <span class='required'></span></label>
                                                        <div class='col-md-4'>
                                                            <input type='date' id='e_tgltermin[<?PHP echo $ix; ?>]' name='e_tgltermin[<?PHP echo $ix; ?>]' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ni_tgltermin; ?>'>
                                                        </div>
                                                    </div>


                                                    <div class='form-group'>
                                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' <?PHP echo $warna; ?>>Tgl. Transfer <?PHP echo $ix; ?> <span class='required'></span></label>
                                                        <div class='col-md-4'>
                                                            <input type='date' id='e_tgltrans[<?PHP echo $ix; ?>]' name='e_tgltrans[<?PHP echo $ix; ?>]' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ni_tgltrans; ?>'>
                                                        </div>
                                                    </div>

                                                    <div class='form-group'>
                                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' <?PHP echo $warna; ?>>Jumlah <?PHP echo $ix; ?> <span class='required'></span></label>
                                                        <div class='col-md-4'>
                                                            <input type='text' id='e_jumlah[<?PHP echo $ix; ?>]' name='e_jumlah[<?PHP echo $ix; ?>]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ni_jumlah; ?>'>
                                                        </div>
                                                    </div>

                                                    <div class='form-group'>
                                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' <?PHP echo $warna; ?>>No Bukti <?PHP echo $ix; ?> <span class='required'></span></label>
                                                        <div class='col-md-4'>
                                                            <input type='text' id='e_nobukti[<?PHP echo $ix; ?>]' name='e_nobukti[<?PHP echo $ix; ?>]' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ni_nobukti; ?>'>
                                                        </div>
                                                    </div>
                                            
                                                <?PHP
                                                    $ix++;
                                                    
                                                    if ($nwarna1==false){ $warna=$warna1; $nwarna1=true; }
                                                    else { $warna=$warna2; $nwarna1=false; }
                                                    
                                                }
                                            }else{
                                                ?>
                                                    <div class='form-group'>
                                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Termin <span class='required'></span></label>
                                                        <div class='col-md-4'>
                                                            <input type='date' id='e_tgltermin[1]' name='e_tgltermin[1]' class='form-control col-md-7 col-xs-12' value='<?PHP //echo $tgl1; ?>'>
                                                        </div>
                                                    </div>


                                                    <div class='form-group'>
                                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Transfer <span class='required'></span></label>
                                                        <div class='col-md-4'>
                                                            <input type='date' id='e_tgltrans[1]' name='e_tgltrans[1]' class='form-control col-md-7 col-xs-12' value='<?PHP //echo $tgl1; ?>'>
                                                        </div>
                                                    </div>

                                                    <div class='form-group'>
                                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                                        <div class='col-md-4'>
                                                            <input type='text' id='e_jumlah[1]' name='e_jumlah[1]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                                        </div>
                                                    </div>

                                                    <div class='form-group'>
                                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Bukti <span class='required'></span></label>
                                                        <div class='col-md-4'>
                                                            <input type='text' id='e_nobukti[1]' name='e_nobukti[1]' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ni_nobukti; ?>'>
                                                        </div>
                                                    </div>
                                                <?PHP
                                            }
                                            ?>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' class='btn btn-success' onclick='disp_confirm_trans_sby("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>


                        </div>
                    </div>


                </form>

                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_content'>
                        <div class='x_panel'>
                            <table id='datatableindb' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='5px'>NO</th>
                                        <th width='30px' nowrap>TGL. TERMIN</th>
                                        <th width='30px' nowrap>TGL. TRANSFER</th>
                                        <th width='20px'>JUMLAH</th>
                                        <th width='20px'>NO BUKTI</th>
                                    </tr>
                                </thead>
                                <body>
                                    <?PHP
                                        
                                        $nm_tabel_pilih=" dbmaster.t_br0_via_sby ";
                                        if ($pdivisipilih=="OTC") $nm_tabel_pilih=" dbmaster.t_br_otc_via_sby ";
                                        if ($pdivisipilih=="KD") $nm_tabel_pilih=" dbmaster.t_klaim_via_sby ";
                                        
                                        $sql = "SELECT * FROM $nm_tabel_pilih ";
                                        $sql.=" WHERE bridinput='$pidbr' order by tgltermin, tgltransfersby, jumlah";
                                        
                                        $query=mysqli_query($cnmy, $sql) or die("error");
                                        $no=1;
                                        while( $row=mysqli_fetch_array($query) ) {  // preparing an array
                                            
                                            $ni_tgltermin=$row['tgltermin'];
                                            $ni_tgltrans = $row["tgltransfersby"];
                                            $ni_jumlah = $row["jumlah"];
                                            $ni_nobukti = $row["nobukti"];

                                            $ni_hapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"disp_hapusdata_spd_all('Hapus Data..?', '')\">";
                                            
                                            $ni_tgltermin = date('d F Y', strtotime($ni_tgltermin));
                                            
                                            if ($ni_tgltrans=="0000-00-00") $ni_tgltrans="";
                                            if (!empty($ni_tgltrans) AND $ni_tgltrans<>"0000-00-00") $ni_tgltrans = date('d F Y', strtotime($ni_tgltrans));
                                            
                                            $ni_jumlah=number_format($ni_jumlah,0,",",",");
                                            
                                            echo "<tr>";
                                            echo "<td nowrap>$no<t/d>";
                                            echo "<td nowrap>$ni_tgltermin<t/d>";
                                            echo "<td nowrap>$ni_tgltrans<t/d>";
                                            echo "<td nowrap>$ni_jumlah<t/d>";
                                            echo "<td nowrap>$ni_nobukti<t/d>";
                                            echo "</tr>";
                                            $no=$no+1;
                                        }
                                     
                                    ?>
                                </body>
                            </table>

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



<script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>


<script>
    $('#mytgl01, #mytgl02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
    
    $('#mytgl01, #mytgl02').on('change dp.change', function(e){
        
    });
    
    function TampilkanJumlahTransfer(nbrid, ndivpilih, njml) {
        var ejml = document.getElementById(njml).value;
        $.ajax({
            type:"post",
            url:"module/surabaya/mod_sby_dbr/viewdata.php?module=viewdatajmltrans",
            data:"ubrid="+nbrid+"&udivpilih="+ndivpilih+"&ujml="+ejml,
            success:function(data){
                $("#div_jml_trans").html(data);
            }
        });
    }
    
    function disp_confirm_trans_sby(pText_,ket)  {
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("demo-form5").action = "module/surabaya/mod_sby_dbr/simpan_jml_trans.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form5").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>