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

    <div class="page-title"><div class="title_left"><h3>Informasi Dokter</h3></div></div><div class="clearfix"></div>
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
                            <?PHP
                            if ($_SESSION['MOBILE']!="Y") {
                                echo "<span hidden><button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button></span>";
                            }
                            ?>
                            <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Spesialis</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_spesialis" name="cb_spesialis">
                                                <?PHP
                                                echo "<option value='' selected>-- All --</option>";
                                                
                                                $query = "select spid as spid, nama as nama from hrd.spesial ";
                                                $query .= " ORDER BY nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pnidsp=$z['spid'];
                                                    $pnamasp=$z['nama'];
                                                    echo "<option value='$pnidsp'>$pnamasp</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Nama Dokter</b>
                                        <div class="form-group">
                                            <input type='text' id='e_nmuserdr' name='e_nmuserdr' class='form-control col-md-7 col-xs-12' value='<?PHP echo ""; ?>'>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-6'>
                                        <b>ID Dokter</b>
                                        <div class="form-group">
                                            <input type='text' id='e_iddr' name='e_iddr' class='form-control col-md-7 col-xs-12' value='<?PHP echo ""; ?>' maxlength="10">
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
    
    function ShowCOA(udiv, ucoa) {
        var icar = "";
        var idiv = document.getElementById(udiv).value;
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_generalledger/viewdata.php?module=viewcoadivisichk",
            data:"umr="+icar+"&udivi="+idiv,
            success:function(data){
                $("#"+ucoa).html(data);
            }
        });
    }
</script>