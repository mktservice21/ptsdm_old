<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan Biaya Rutin Per Tahun</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                            <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tahun <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='thn01'>
                                                <input type='text' id='tahun' name='tahun' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

<script>
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
        
    }
    
    function ShowDataJabatan(){
        var epilposisi = document.getElementById('e_pilihposisi').value;
        
        $.ajax({
            type:"post",
            url:"module/laporan/mod_rutin_pertahun/viewdata.php?module=viewdatajabatan",
            data:"upilposisi="+epilposisi,
            success:function(data){
                $("#kotak-multi3").html(data);
            }
        });
        
    }
</script>
    
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_pilihtipe'>Pilih Posisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' name='e_pilihposisi' id='e_pilihposisi' style='width: 100%;' onchange="ShowDataJabatan()">
                                            <option value='' selected>-- All --</option>
                                            <option value='CAB'>Cabang</option>
                                            <option value='HO'>HO</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12'>Jabatan &nbsp;<input type="checkbox" id="chkbtnjab" value="deselect" onClick="SelAllCheckBox('chkbtnjab', 'chkbox_posisi[]')" checked/><span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <div id="kotak-multi3" class="jarak">
                                        <?PHP
                                            echo "<input type=checkbox value='emptypilih' name='chkbox_posisi[]' id='chkbox_posisi[]' checked> 00 - empty<br/>";
                                            $sql=mysqli_query($cnmy, "select jabatanId, nama from hrd.jabatan order by jabatanId");
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                $npkdjab=$Xt['jabatanId'];
                                                $npnmjab=$Xt['nama'];
                                                echo "<input type=checkbox value='$npkdjab' name='chkbox_posisi[]' id='chkbox_posisi[]' checked> $npkdjab - $npnmjab<br/>";
                                            }
                                        ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12'>Jenis &nbsp;<input type="checkbox" id="chkjenisobat" value="deselect" onClick="SelAllCheckBox('chkjenisobat', 'chkbox_jnsobat[]')" checked/><span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <div id="kotak-multi2" class="jarak">
                                        <?PHP
                                            $query = "select a.* from dbmaster.t_brid a WHERE a.kode='1' ";
                                            $query .= " ORDER BY a.postingid, a.nobrid";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pnobrid=$z['nobrid'];
                                                $pnmbrid=$z['nama'];
                                                echo "&nbsp; <input type=checkbox value='$pnobrid' name='chkbox_jnsobat[]' checked> $pnobrid - $pnmbrid<br/>";
                                            }
                                        ?>
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

<script>
    function disp_confirm(pText)  {
        if (pText == "excel") {
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }else{
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    }
</script>