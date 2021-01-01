

<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Rekap Data Permohonan Dana BR</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                
                include "config/koneksimysqli_it.php";
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('d F Y', strtotime($hari_ini));
                ?>
                <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
                <!--<form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>"  enctype='multipart/form-data' target="_blank">-->
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
                                        
                                        <!--
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_dokter'>Periode By <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id="cb_tgltipe" name="cb_tgltipe">
                                                    <option value="1">Last Input / Update</option>
                                                    <option value="2" selected>Tanggal Transfer</option>
                                                    <option value="3">Tanggal Pengajuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        -->
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Tanggal BR <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='tgl01'>
                                                        <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Lampiran <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id="cb_jenis" name="cb_jenis">
                                                    <option value="Y" selected>Ya</option>
                                                    <option value="N" >Tidak</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Terakhir <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <?PHP
                                                $pnomor=1;
                                                $query = "select max(tno) tnomor from dbmaster.t_otc_norekapdanabr";
                                                $tampil= mysqli_query($cnit, $query);
                                                $ketemu= mysqli_num_rows($tampil);
                                                if ($ketemu>0) {
                                                    $mr= mysqli_fetch_array($tampil);
                                                    if (!empty($mr['tnomor'])) {
                                                        $pnomor=$mr['tnomor'];
                                                    }
                                                }
                                                ?>
                                                <input type="text" id="t_nomor" name="t_nomor" value="<?PHP echo $pnomor; ?>">
                                                <input type="button" id="btnsave" name="btnsave" value="Simpan No Terakhir" onclick="savedatanomor('simpan')"><br/><br/>
                                                <input type="button" id="btndelete" name="btndelete" value="Delete No Sesuai Tanggal BR" onclick="savedatanomor('hapus')">
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

<script>
    function savedatanomor(ket)  {
        if (ket == "simpan") {
            var cmt = confirm('Apakah akan simpan no terakhir...?');
        }else{
            var cmt = confirm('Apakah akan hapus no sesuai tanggal br yang dipilih...?');
        }
        if (cmt == false) {
            return false;
        }
        var etgl=document.getElementById('e_periode01').value;
        var eno=document.getElementById('t_nomor').value;
        
        $.ajax({
            type:"post",
            url:"module/lap_br_otcpermohonan/simpannoterakhir.php?module="+ket,
            data:"ket="+ket+"&tglbr="+etgl+"&tno="+eno,
            success:function(data){
                alert(data);
            }
        });
            
    }
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