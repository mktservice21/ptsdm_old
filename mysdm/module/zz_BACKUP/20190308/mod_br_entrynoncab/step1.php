<!--kiri-->
<div class='col-md-6 col-xs-12'>
    <div class='x_panel'>
        <div class='x_content form-horizontal form-label-left'>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                <div class='col-xs-9'>
                    <input type='text' id='e_nobr' name='e_nobr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $noid; ?>' Readonly>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Tanggal </label>
                <div class='col-md-9'>
                    <div class='input-group date' id='tgl01'>
                        <input type='text' id='e_tglinput' name='e_tglinput' required='required' class='form-control col-md-7 col-xs-12' placeholder='tanggal input' value='<?PHP echo $tglinput; ?>' placeholder='dd mmm yyyy' Readonly>
                        <span class='input-group-addon'>
                            <span class='glyphicon glyphicon-calendar'></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                <div class='col-xs-9'>
                    <select class='form-control' id='cb_divisi' name='cb_divisi' onchange="HapusDataKaryawan('e_idkaryawan', 'e_karyawan', 'e_idcabang', 'e_cabang', 'e_akun', 'e_namaakun')">
                        <?PHP
                        $fildiv="";
                        if ($_GET['act']=="editdata"){
                            $fildiv=" and DivProdId='$divprodid' ";
                        }
                        $tampil=mysqli_query($cnmy, "SELECT DivProdId, nama FROM 1it.divprod where br='Y' $fildiv order by nama");
                        //echo "<option value='' selected>-- Pilihan --</option>";
                        while($a=mysqli_fetch_array($tampil)){ 
                            if ($a['DivProdId']==$divprodid)
                                echo "<option value='$a[DivProdId]' selected>$a[nama]</option>";
                            else{
                                if ($a['DivProdId']==$_SESSION['DIVISI'])
                                    echo "<option value='$a[DivProdId]' selected>$a[nama]</option>";
                                else
                                    echo "<option value='$a[DivProdId]'>$a[nama]</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Yang Membuat <span class='required'></span></label>
                <div class='col-xs-9'>
                    <div class='input-group '>
                        <span class='input-group-btn'>
                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' 
                                    onClick="getDataKaryawanDiv('e_idkaryawan', 'e_karyawan', 
                            '<?PHP echo $_SESSION['STSADMIN']; ?>',
                            '<?PHP echo $_SESSION['LVLPOSISI']; ?>',
                            '<?PHP echo $_SESSION['DIVISI']; ?>',
                            'cb_divisi'
                            )">Go!</button>
                        </span>
                        <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $idajukan; ?>' Readonly>
                        <input type='text' class='form-control' id='e_karyawan' name='e_karyawan' value='<?PHP echo $nmajukan; ?>' Readonly>
                    </div>
                </div>
            </div>
            
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_cabang'>Cabang SDM <span class='required'></span></label>
                <div class='col-sm-9'>
                    <div class='input-group '>
                    <span class='input-group-btn'>
                        <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataCabangFmr('e_idcabang', 'e_cabang', 'e_idkaryawan')">Go!</button>
                    </span>
                    <input type='hidden' class='form-control' id='e_idcabang' name='e_idcabang' value='<?PHP echo $idcab; ?>' Readonly>
                    <input type='text' class='form-control' id='e_cabang' name='e_cabang' value='<?PHP echo $nmcab; ?>' Readonly>
                    </div>

                </div>
            </div>



        </div>
    </div>
</div>

<!--kanan-->
<div class='col-md-6 col-xs-12'>
    <div class='x_panel'>
        <div class='x_content form-horizontal form-label-left'>


            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl02'>Tanggal Perlu </label>
                <div class='col-md-9'>
                    <div class='input-group date' id='tgl01'>
                        <input type='text' id='e_tglperlu' name='e_tglperlu' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl perlu' value='<?PHP echo $tglperlu; ?>' placeholder='dd mmm yyyy' Readonly>
                        <span class='input-group-addon'>
                            <span class='glyphicon glyphicon-calendar'></span>
                        </span>
                    </div>
                </div>
            </div>
            
            
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Mata Uang <span class='required'></span></label>
                <div class='col-xs-9'>
                    <select class='form-control' name='cb_jenis'>
                        <?php
                        $tampil=mysqli_query($cnmy, "SELECT ccyId, nama FROM 1it.ccy");
                        while($c=mysqli_fetch_array($tampil)){
                            if ($c['ccyId']==$ccy)
                                echo "<option value='$c[ccyId]' selected>$c[ccyId] - $c[nama]</option>";
                            else{
                                if ($c['ccyId']=="IDR")
                                    echo "<option value='$c[ccyId]' selected>$c[ccyId] - $c[nama]</option>";
                                else    
                                    echo "<option value='$c[ccyId]'>$c[ccyId] - $c[nama]</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>


            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                <div class='col-xs-9'>
                    <input type='text' id='e_jmlusulan' name='e_jmlusulan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' readonly="">
                </div><!--disabled='disabled'-->
            </div>



            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Aktivitas <span class='required'></span></label>
                <div class='col-xs-9'>
                    <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $aktivitas; ?></textarea>
                </div>
            </div>


        </div>
    </div>
</div>