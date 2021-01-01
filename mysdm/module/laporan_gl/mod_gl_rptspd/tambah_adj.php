<?PHP
    session_start();
    include "../../../config/koneksimysqli.php";
    $act="input";
    $aksi="";
    
    $pidnomorspd=$_POST['unomorspd'];
    $pjumlah="";
    
    $sql = "SELECT distinct tglspd as tanggal FROM dbmaster.t_suratdana_br WHERE nomor='$pidnomorspd' AND stsnonaktif<>'Y'";
    $tampil= mysqli_query($cnmy, $sql);
    $row= mysqli_fetch_array($tampil);
    $pptgl=$row['tanggal'];
    $ptglspd = $row['tanggal'];
    $tgl1 = date('d/m/Y', strtotime($pptgl));
    $pnodivisi="";
    $ajsnobr="";
    $pketerangan="";
    $pdivisi="HO";
?>


    <!--input mask -->
    <script src="js/inputmask.js"></script>


    
<script> window.onload = function() { document.getElementById("e_jumlah").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Isi Data Adjustment</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=brdanabank&act=input&idmenu=258"; ?>' id='demo-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_nomorspd' name='e_nomorspd' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidnomorspd; ?>' Readonly>
                                                <input type='hidden' id='e_tglspd' name='e_tglspd' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptglspd; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                            <div class='col-md-3'>
                                                <div class='input-group date' id='mytgl01'>
                                                    <input type="text" class="form-control" id='e_tanggal' name='e_tanggal' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>' readonly>
                                                    <span class='input-group-addon'>
                                                        <span class='glyphicon glyphicon-calendar'></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih No BR/Divisi <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <select class='form-control input-sm' id='cb_pilihnodivisi' name='cb_pilihnodivisi' onchange="BukaTutupPilihan()">
                                                    <option value='Y' selected>Y</option>
                                                    <option value='N'>N</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <script>
                                            function CariDataBRPerdivisi() {
                                                var edivi = document.getElementById("cb_divpil_").value;

                                                $.ajax({
                                                    type:"post",
                                                    url:"module/laporan_gl/mod_gl_rptspd/viewdata.php?module=caridatabrperdivisi",
                                                    data:"udivi="+edivi,
                                                    success:function(data){
                                                        $("#cb_nodivisi").html(data);
                                                    }
                                                });
                                            }
                                        </script>
										
                                        <div id="div_nodivisi1">
                                            
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <select class='form-control input-sm' id='cb_divpil_' name='cb_divpil_' onchange="CariDataBRPerdivisi()">
                                                        <option value="">--All--</option>
                                                        <option value="EAGLE">EAGLE</option>
                                                        <option value="HO">HO</option>
                                                        <option value="PEACO">PEACOK</option>
                                                        <option value="PIGEO">PIGEON</option>
                                                        <option value="OTC">OTC</option>
                                                        <option value="CAN">CANARY / ETHICAL</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No BR/Divisi <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <select class='form-control input-sm' id='cb_nodivisi' name='cb_nodivisi'>
                                                        <option value='' selected>-- Pilihan --</option>
                                                        <?PHP
                                                        $query = "select divisi, year(tgl) tgl, nodivisi, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
                                                                . " AND IFNULL(nodivisi,'')<>'' "//AND  (userid='$_SESSION[IDCARD]' OR nodivisi='$ajsnobr')
                                                                . " AND IFNULL(nomor,'')<>'' "//( DATE_FORMAT(tgl,'%Y-%m')='$ajsbulan' OR DATE_FORMAT(tglspd,'%Y-%m')='$ajsbulan' )
                                                                . "GROUP BY 1,2,3 ORDER BY 1,2,3";
                                                        $tampil = mysqli_query($cnmy, $query);
                                                        while ($z= mysqli_fetch_array($tampil)) {
                                                            $pajsjmlbr=$z['jumlah'];
                                                            if (!empty($pajsjmlbr)) $pajsjmlbr=number_format($pajsjmlbr,0);
                                                            $pajsnobr=$z['nodivisi'];
                                                            $pajsdivisi=$z['divisi'];
                                                            if (empty($pajsdivisi)) $pajsdivisi= "ETHICAL";
                                                            $pajsketjml = "$pajsnobr";//$pajsdivisi -  &nbsp;&nbsp (Rp. $pajsjmlbr)
                                                            if (trim($pajsnobr)==trim($ajsnobr)){
                                                                echo "<option value='$pajsnobr' selected>$pajsketjml</option>";
                                                                $lewatnodivspd2=true;
                                                            }else
                                                                echo "<option value='$pajsnobr'>$pajsketjml</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        
                                        </div>
                                        
                                        <div hidden id="div_nodivisi2">
                                            
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No BR/Divisi <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <input type='text' id='e_nodivisi' name='e_nodivisi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ajsnobr; ?>'>
                                                </div>
                                            </div>

                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <select class='form-control input-sm' id='cb_jenis' name='cb_jenis'>
                                                        <option value='1' selected>ADVANCE</option>
                                                        <option value='2'>KLAIM</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <select class='form-control input-sm' id='cb_divisi' name='cb_divisi'>
                                                        <?PHP
                                                        $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER', '') ";
                                                        $tampil = mysqli_query($cnmy, $query);
                                                        while ($z= mysqli_fetch_array($tampil)) {
                                                            if ($z['DivProdId']==$pdivisi)
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jumlah' name='e_jumlah' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_ket' name='e_ket' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' id='nm_btn_save' class='btn btn-success' onclick='disp_confirm_adj("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
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
                                        <th width='20px'></th>
                                        <th width='20px' nowrap>No BR/DIVISI</th>
                                        <th width='30px'>TANGGAL</th>
                                        <th width='20px'>JENIS</th>
                                        <th width='20px'>JUMLAH</th>
                                        <th width='200px'>KETERANGAN</th>
                                    </tr>
                                </thead>
                                <body>
                                    <?PHP
                                        $sql = "SELECT idinput, nodivisi2, DATE_FORMAT(tgl,'%d %M %Y') as tanggal, kodeid2, subkode2, "
                                                . " divisi2, FORMAT(jumlah,0,'de_DE') as jumlah, "
                                                . " keterangan ";
                                        $sql.=" FROM dbmaster.t_suratdana_br ";
                                        $sql.=" WHERE kodeid='3' AND IFNULL(stsnonaktif,'')<>'Y' AND nomor='$pidnomorspd' order by idinput, divisi, nodivisi";
                                        $query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
                                        $no=1;
                                        while( $row=mysqli_fetch_array($query) ) {  // preparing an array
                                            
                                            $ni_idinput=$row['idinput'];
                                            $ni_nodivisi=$row['nodivisi2'];
                                            $ni_kodeid="Advance";
                                            if ($row["kodeid2"]=="2") $ni_kodeid="Klaim";

                                            $ni_tgl = $row["tanggal"];
                                            $ni_divisi = $row["divisi2"];
                                            $ni_jumlah = $row["jumlah"];
                                            $ni_ket = $row["keterangan"];

                                            $ni_hapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"disp_hapusdata_adj('Hapus Data..?', '$ni_idinput')\">";
                                            
                                            echo "<tr>";
                                            echo "<td nowrap>$no<t/d>";
                                            echo "<td nowrap>$ni_hapus<t/d>";
                                            echo "<td nowrap>$ni_nodivisi<t/d>";
                                            echo "<td nowrap>$ni_tgl<t/d>";
                                            echo "<td nowrap>$ni_kodeid<t/d>";
                                            echo "<td nowrap align='right'>$ni_jumlah<t/d>";
                                            echo "<td>$ni_ket<t/d>";
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

<!-- jquery.inputmask -->
<script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
<!-- Custom Theme Scripts -->

<script src="js/select_combo.js"></script>
<script>
    $( function() {
        $( "#cb_nodivisix" ).combobox();
    } );
</script>

<script>
    $('#mytgl01, #mytgl02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
    
    
    function disp_confirm_adj(pText_,nid)  {
        // pText_, nid
        var eact="inputdataadj";
        var eidnospd = document.getElementById("e_nomorspd").value;
        var etgl = document.getElementById("e_tanggal").value;
        var etglspd = document.getElementById("e_tglspd").value;
        var ejumlah = document.getElementById("e_jumlah").value;
        var eketerangan = document.getElementById("e_ket").value;
        
        var epilih = document.getElementById("cb_pilihnodivisi").value;
        
        var enobrdivisi="";
        var ejenis="";
        var edivisi="";
        
        if (epilih=="N") {
            enobrdivisi=document.getElementById("e_nodivisi").value;
            ejenis=document.getElementById("cb_jenis").value;
            edivisi=document.getElementById("cb_divisi").value;
        }else{
            enobrdivisi=document.getElementById("cb_nodivisi").value;
        }
        
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
                    url:"module/laporan_gl/mod_gl_rptspd/simpan_adj.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidnospd="+eidnospd+"&utgl="+etgl+"&utglspd="+etglspd+"&ujumlah="+ejumlah+
                         "&uketerangan="+eketerangan+"&upilih="+epilih+"&unobrdivisi="+enobrdivisi+"&ujenis="+ejenis+"&udivisi="+edivisi,
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

    function BukaTutupPilihan() {
        var ipilihnodiv = document.getElementById('cb_pilihnodivisi').value;
        if (ipilihnodiv=="N") {
            div_nodivisi1.style.display="none";
            div_nodivisi2.style.display="block";
        }else{
            div_nodivisi1.style.display="block";
            div_nodivisi2.style.display="none";
        }
    }
    
    function disp_hapusdata_adj(pText_,nid)  {
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
                    url:"module/laporan_gl/mod_gl_rptspd/simpan_adj.php?module="+module+"&act=hapus&idmenu="+idmenu,
                    data:"uid="+nid,
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

