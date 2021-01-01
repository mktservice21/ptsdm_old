<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $aksi="eksekusi3.php";
    
    
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Rekap Budget Request Klaim Discount</h3></div></div><div class="clearfix"></div>
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
                    <div class='col-md-12 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <select class='form-control input-sm' id='e_stsspd' name='e_stsspd' onchange="ShowData()">
                                            <option value="1">--All--</option>
                                            <option value="2" selected>Permintaan Dana</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="div_tipeperiode">                                    
                                    
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
    
    $(document).ready(function() {
        ShowData();
    } );
    
    $(function() {
        $('#bulan1').datepicker({
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
    
    function ShowData()  {
        var istsspd = document.getElementById('e_stsspd').value;
        var imodule="divnonspd";
        if (istsspd=="2") imodule="divspd";
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_rekapbrklaim/viewdata.php?module="+imodule,
            data:"ustsspd="+istsspd,
            success:function(data){
                $("#div_tipeperiode").html(data);
            }
        });
        if (istsspd=="2") {
            ShowDataNoBR('1');
        }
    }
    
    function ShowDataNoBR(nno)  {
        if (nno=="1") {
            var itgl = moment().format('YYYY-MM-D');
        }else{
            var itgl = document.getElementById('bulan1').value;
        }
        
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_rekapbrklaim/viewdata.php?module=showdatanobr",
            data:"utgl="+itgl+"&unom="+nno,
            success:function(data){
                $("#kotak-multi9").html(data);
            }
        });
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

<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>