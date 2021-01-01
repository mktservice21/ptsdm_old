<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $tgl_firstthnlalu = date('01 F Y', strtotime('-1 year', strtotime($hari_ini)));
    $tgl_lstthnlalu = date('t F Y', strtotime('-1 year', strtotime($hari_ini)));
    
    $mythn=date("Y");
    $hari_ini2 = date($mythn."-01-01");
    $tgl_thnlalu = date('01 F Y', strtotime('-1 year', strtotime($hari_ini2)));
    
    $aksi="eksekusi3.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Update Jakarta Hospital</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan</label>
                                    <div class='col-md-6'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_tglskrang' name='e_tglskrang' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $tgl_pertama; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="div_periode">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Awal Bln. Thn. Lalu</label>
                                        <div class='col-md-6'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_frtthnlalu' name='e_frtthnlalu' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_firstthnlalu; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>


                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Akhir Bln. Thn. Lalu</label>
                                        <div class='col-md-6'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_lstthnlala' name='e_lstthnlala' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_lstthnlalu; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Awal Thn. Lalu</label>
                                        <div class='col-md-6'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_thnlalu' name='e_thnlalu' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_thnlalu; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>

                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm_pros()'>Proses</button>
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

<script type="text/javascript">
    $(function() {
        $('#e_tglberlakux').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                //////ShowDataKode();
            } 
        });
    });
    
    $(document).ready(function() {
        $('#e_tglskrang').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'MM yy',
            onSelect: function(dateStr) {
                
            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                ShowDataPeriode();
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
    
    
    function ShowDataPeriode() {
        var ebulan = document.getElementById("e_tglskrang").value;

        $.ajax({
            type:"post",
            url:"module/sls_pdatejkthsopt/viewdata.php?module=caridataperiode",
            data:"ubulan="+ebulan,
            success:function(data){
                $("#div_periode").html(data);
            }
        });
    }
    
    
    function disp_confirm_pros() {
        var ebln1 = document.getElementById("e_tglskrang").value;
        var ebln2 = document.getElementById("e_frtthnlalu").value;
        var ebln3 = document.getElementById("e_lstthnlala").value;
        var ebln4 = document.getElementById("e_thnlalu").value;

        if (ebln1=="") {
            alert("BULAN Kosong...")
        }

        if (ebln2=="") {
            alert("Awal Bulan Tahun Lalu Kosong...")
        }

        if (ebln3=="") {
            alert("Akhir Bulan Tahun Lalu Kosong...")
        }

        if (ebln4=="") {
            alert("Bulan Tahun Lalu Kosong...")
        }
        
        var cmt = confirm('Apakah akan melakukan proses ...?');
        if (cmt == false) {
            return false;
        }else{
        
            $.ajax({
                type:"post",
                url:"module/sls_pdatejkthsopt/simpandataproses.php?module=simpandataproses",
                data:"ubln1="+ebln1+"&ubln2="+ebln2+"&ubln3="+ebln3+"&ubln4="+ebln4,
                success:function(data){
                    alert(data);
                }
            });
            
        }
        
    }
    
</script>


<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>