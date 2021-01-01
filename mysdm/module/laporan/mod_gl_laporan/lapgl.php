<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $nmlabel="&nbsp;";
    $divhidden="";
    if ($_SESSION['GROUP']=="22" OR $_SESSION['GROUP']=="34") {
        $divhidden="hidden";
        $nmlabel="Periode";
    }
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan Transaksi Budget Request</h3></div></div><div class="clearfix"></div>
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
                                echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button>";
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
                                
                                
                                <div hidden class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Periode By</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_periode" name="cb_periode">
                                                <?PHP
                                                if ($_SESSION['GROUP']=="22" OR $_SESSION['GROUP']=="34") {
                                                    echo "<option value='1' selected>Tgl. Input</option>";
                                                    echo "<option value='2'>Tgl. Transfer</option>";
                                                }else{
                                                    echo "<option value='1' selected>Tgl. Input</option>";
                                                    echo "<option value='2' selected>Tgl. Transfer</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='col-sm-6'>
                                    <b>Periode</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln01'>
                                            <input type='text' id='e_tgl1' name='e_tgl1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='col-sm-6'>
                                    <b>s/d.</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln02'>
                                            <input type='text' id='e_tgl2' name='e_tgl2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Divisi</b>
                                        <div class="form-group">
                                            <select class='form-control' id="divprodid" name="divprodid" onchange="ShowCOA('divprodid', 'kotak-multi2');">
                                                <?PHP
                                                if ($_SESSION['IDCARD']=="0000000148") $divisi = "HO";
                                                elseif ($_SESSION['IDCARD']=="0000001043") $divisi = "EAGLE";
                                                else{
                                                    if ($_SESSION['DIVISI']=="OTC") $divisi = "OTC";
                                                }
                                                $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId<>'OTHER' ";
                                                $query .=" order by DivProdId";
                                                $tampil = mysqli_query($cnmy, $query);
                                                echo "<option value='' selected>All</option>";
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pdivisi=$z['DivProdId'];
                                                    if ($pdivisi=="CAN") $pdivisi="CANARY";
                                                    if ($z['DivProdId']==$divisi)
                                                        echo "<option value='$z[DivProdId]' selected>$pdivisi</option>";
                                                    else
                                                        echo "<option value='$z[DivProdId]'>$pdivisi</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>COA</b> <input type="checkbox" id="chkbtncoa" value="deselect" onClick="SelAllCheckBox('chkbtncoa', 'chkbox_coa[]')" checked/>
                                        <div class="form-group">
                                            <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                    echo "&nbsp; <input type=checkbox value='' name='chkbox_coa[]' checked> empty<br/>";
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a ";
                                                    $query .= " ORDER BY a.COA4";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pcoa4=$z['COA4'];
                                                        $pnmcoa4=$z['NAMA4'];
                                                        echo "&nbsp; <input type=checkbox value='$pcoa4' name='chkbox_coa[]' checked> $pcoa4 - $pnmcoa4<br/>";
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Pilih Report Dari</b> 
                                        <div class="form-group">
                                            <div id="kotak-multi3" class="jarak">
                                                <?PHP
                                                if ($_SESSION['IDCARD']=="0000000148") {
                                                    echo "&nbsp; <input type=checkbox value='brethical' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                }elseif ($_SESSION['IDCARD']=="0000000566") {
                                                    echo "&nbsp; <input type=checkbox value='brethical' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                }elseif ($_SESSION['IDCARD']=="0000001043") {
                                                    echo "&nbsp; <input type=checkbox value='brethical' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                    echo "&nbsp; <input type=checkbox value='klaimdisc' name='chkbox_rpt2' checked> Klaim Discount<br/>";
                                                }else{
                                                    if ($_SESSION['DIVISI']=="OTC") {
                                                        echo "&nbsp; <input type=checkbox value='brotc' name='chkbox_rpt4' checked> BR OTC<br/>";
                                                    }else{
                                                ?>
                                                        &nbsp; <input type=checkbox value='brethical' name='chkbox_rpt1' checked> BR Ethical<br/>
                                                        &nbsp; <input type=checkbox value='klaimdisc' name='chkbox_rpt2' > Klaim Discount<br/>
                                                        &nbsp; <input type=checkbox value='kaskecil' name='chkbox_rpt3' > Kas Kecil<br/>
                                                        &nbsp; <input type=checkbox value='brotc' name='chkbox_rpt4' > BR OTC<br/>
                                                        &nbsp; <input type=checkbox value='rutin' name='chkbox_rpt5' > Biaya Rutin<br/>
                                                        &nbsp; <input type=checkbox value='blk' name='chkbox_rpt6' > Biaya Luar Kota<br/>
                                                        <!--&nbsp; <input type=checkbox value='ca' name='chkbox_rpt7' > Cash Advance<br/>-->
                                                        &nbsp; <input type=checkbox value='bmsby' name='chkbox_rpt8' > Biaya Marketing SBY<br/>
                                                        &nbsp; <input type=checkbox value='pilbank' name='chkbox_rpt9' > Bank<br/>
                                                        &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt10' name='chkbox_rpt10' > Incentive Ethical<br/>
                                                <?PHP
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <u><b>Report Type</b></u>
                                        <div class="form-group">
                                            <input type="radio" id="radio1" name="radio1" value="D" checked> Detail &nbsp; 
                                            <input type="radio" id="radio1" name="radio1" value="S"> Summary
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Realisasi</b> 
                                        <div class="form-group">
                                            <input id="tags_1" name="tags_real" type="text" class="tags form-control" value="" />
                                            <div id="suggestions-container" style="position: relative; float: left; width: 250px; margin: 10px;"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!--
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Realisasi</b> <input type="checkbox" id="chkbtnreal" value="select" onClick="SelAllCheckBox('chkbtnreal', 'chkbox_real[]')"/>
                                        <div class="form-group">
                                            <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                /*
                                                    echo "&nbsp; <input type=checkbox value='' name='chkbox_real[]' > <b>kosong / tanpa nama realisasi</b><br/>";
                                                    $query = "select DISTINCT realisasi1 FROM hrd.br0 WHERE IFNULL(realisasi1,'') NOT IN ('', '.') AND Year(tgl)>='2019' ";
                                                    $query .= " ORDER BY realisasi1";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pnmreal=$z['realisasi1'];
                                                        echo "&nbsp; <input type=checkbox value='$pnmreal' name='chkbox_real[]'> $pnmreal<br/>";
                                                    }
                                                 * 
                                                 */
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                -->
                                


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
    $(document).ready(function() {
        ShowCOA('divprodid', 'kotak-multi2');
    })
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