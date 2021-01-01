
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
        
        
        if (nmbuton=="chkbtndivprod"){
            var mycek="";
            for (var i in checkboxes){
                if (checkboxes[i].checked) {
                    mycek=mycek+"'"+checkboxes[i].value+"',";
                }
            }
            if (mycek==""){
                $("#kotak-multi3").html("");
                return 0;
            }
            var epiltipe = document.getElementById('e_pilihtipe').value;
            $.ajax({
                type:"post",
                url:"config/viewdata2.php?module=viewkodedivisi&data1="+mycek,
                data:"udata1="+mycek+"&upilihtipe="+epiltipe,
                success:function(data){
                    $("#kotak-multi3").html(data);
                }
            });
            
        }
        
        
    }

    function selectKodePosting(spost){
        var epost = document.getElementById(spost).value;
        
        $.ajax({
            type:"post",
            url:"config/viewdata2.php?module=viewkodepostingotc",
            data:"upost="+epost+"&upilihtipe="+epost,
            success:function(data){
                $("#kotak-multi3").html(data);
            }
        });
    }
</script>

<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Rekap Transfer BR OTC</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="eksekusi2.php";
        switch($_GET['act']){
            default:
                include "config/koneksimysqli_it.php";
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('d F Y', strtotime($hari_ini));
                ?>
                <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>"  enctype='multipart/form-data' target="_blank">
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <button type='submit' class='btn btn-success'>Preview</button>
                                </h2>
                                <div class='clearfix'></div>
                            </div>
                            
                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        
                                        <div hidden>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_dokter'>Periode By <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id="cb_tgltipe" name="cb_tgltipe">
                                                    <option value="1">Last Input / Update</option>
                                                    <option value="2" selected>Tanggal Transfer</option>
                                                    <option value="3">Tanggal Pengajuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='tgl01'>
                                                        <input type='text' id='tgl01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    <div class='input-group date' id='tgl02'>
                                                        <input type='text' id='tgl02' name='e_periode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Alokasi BR <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_alokasi' id='cb_alokasi'>
                                                <?PHP
                                                $tampil=mysqli_query($cnmy, "SELECT bralid, nama FROM dbmaster.bral_otc");
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                while($a=mysqli_fetch_array($tampil)){
                                                    echo "<option value='$a[bralid]'>$a[nama]</option>";
                                                }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Cabang &nbsp;<input type="checkbox" id="chkbtncabang" value="deselect" onClick="SelAllCheckBox('chkbtncabang', 'chkbox_cabango[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                    cBoxIsiCabangOFilter("", "",
                                                            "", "$fstsadmin", "$flvlposisi", "$fdivisi", "T");
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        
                                    </div>
                                </div>           
                            </div>           
                            
                            <!--kanan-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                            
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_pilihtipe'>Posting <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id='cb_subpost' name='cb_subpost' onchange="selectKodePosting('cb_subpost')">
                                                    <?PHP
                                                    $tampil=mysqli_query($cnit, "select distinct subpost, nmsubpost from hrd.brkd_otc where ifnull(subpost,'') <> '' order by nmsubpost");
                                                    echo "<option value='none' selected>-- Pilihan --</option>";
                                                    echo "<option value=''>blank_</option>";
                                                    while($a=mysqli_fetch_array($tampil)){ 
                                                        if ($a['subpost']==$subposting)
                                                            echo "<option value='$a[subpost]' selected>$a[nmsubpost]</option>";
                                                        else
                                                            echo "<option value='$a[subpost]'>$a[nmsubpost]</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Kode &nbsp;<input type="checkbox" id="chkbtnkode" value="deselect" onClick="SelAllCheckBox('chkbtnkode', 'chkbox_kodeotc[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi3" class="jarak">
                                                <?PHP
                                                    cBoxIsiKodePostingOTC("");
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Lampiran &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxLampiranAll();
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>CA &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxCAAll();
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Via Surabaya &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxVIAAll();
                                                ?>
                                            </div>
                                        </div>
                                        
                                        
                            
                                    </div>
                                </div>           
                            </div>      
    
                            
                        </div>
                    </div>
                </form>
                <?PHP
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

