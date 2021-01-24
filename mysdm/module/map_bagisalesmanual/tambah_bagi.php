<?php

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];
$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 

?>



<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Distributor <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='form-control input-sm' id="cb_dist" name="cb_dist" onchange="ShowDataEcabangDist()">
                                            <?PHP
                                                echo "<option value=''>--Piihan--</option>";

                                                $query_aktif ="select distid, nama from dbtemp.distrib0 ";
                                                $query_aktif .=" order by nama";
                                                $tampil= mysqli_query($cnms, $query_aktif);
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $piddis=$row['distid'];
                                                    $pnmdist=$row['nama'];
                                                    $pditint=(INT)$piddis;
                                                    if ($piddistpl==$piddis)
                                                        echo "<option value='$piddis' selected>$pnmdist ($pditint)</option>";
                                                    else
                                                        echo "<option value='$piddis'>$pnmdist ($pditint)</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='form-control input-sm' id="cb_ecabang" name="cb_ecabang" onchange="">
                                            <?PHP
                                            echo "<option value=''>--All--</option>";
                                            if (!empty($piddistpl)) {
                                                $query="SELECT distid, ecabangid, nama from dbtemp.ecabang where distid='$piddistpl' ";
                                                $query .=" order by nama";
                                                $tampil= mysqli_query($cnms, $query);
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $pidecab=$row['ecabangid'];
                                                    $pnmecab=$row['nama'];
                                                    if ($pidecab==$pidecabpl)
                                                        echo "<option value='$pidecab' selected>$pnmecab ($pidecab)</option>";
                                                    else
                                                        echo "<option value='$pidecab'>$pnmecab ($pidecab)</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                               <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='thnbln01x'>
                                            <input type='text' class='form-control' id='e_bulan' name='e_bulan' autocomplete='off' value='<?PHP echo $pblnpilih; ?>' />
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Faktur  <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_namafilter' name='e_namafilter' class='form-control col-md-7 col-xs-12' value='<?PHP echo "$pfilterpl"; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        &nbsp; 
                                    </label>
                                    <div class='col-md-4'>
                                        <button type='button' class='btn btn-dark' onclick='disp_viewdata()'>View Data</button>
                                        
                                    </div>
                                </div>
                                
                                
                            </div>
                            
                            
                        </div>
                    </div>
                    
                    
                    <div id="div_detail">
                        
                        <div id='loading3'></div>
                        <div id='c-fakturdata'>
                        
                        </div>
                        
                        <div id='loading'></div>
                        <div id='c-datamaping'>
                        
                        </div>
                        
                        <div id='loading2'></div>
                        <div id='c-databagi'>
                        
                        </div>
                        
                    </div>
                    
                </form>
                
            </div>
            
        </div>
        
        
        
    </div>
    
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>

<script>

    $(document).ready(function() {

        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
        
        $('#e_bulan').datepicker({
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

    function disp_viewdata() {
        distp_datafaktur();
        disp_datamaping();
        $("#c-databagi").html("");
    }

    function distp_datafaktur() {
        var edistid=document.getElementById('cb_dist').value;
        var ecabid=document.getElementById('cb_ecabang').value;
        var enamafilter=document.getElementById('e_namafilter').value;
        var ebln=document.getElementById('e_bulan').value;

        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");

        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/viewdatatabelefaktur.php?module="+module+"&idmenu="+idmenu+"&act="+act,
            data:"udistid="+edistid+"&ucabid="+ecabid+"&unamafilter="+enamafilter+"&ubln="+ebln,
            success:function(data){
                $("#c-fakturdata").html(data);
                $("#loading3").html("");
            }
        });
    }
    
    function disp_datamaping() {
        var edistid=document.getElementById('cb_dist').value;
        var ecabid=document.getElementById('cb_ecabang').value;
        var enamafilter=document.getElementById('e_namafilter').value;
        var ebln=document.getElementById('e_bulan').value;

        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");

        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/viewdatatabelebagi.php?module="+module+"&idmenu="+idmenu+"&act="+act,
            data:"udistid="+edistid+"&ucabid="+ecabid+"&unamafilter="+enamafilter+"&ubln="+ebln,
            success:function(data){
                $("#c-datamaping").html(data);
                $("#loading").html("");
            }
        });
    }

    function ShowDataEcabangDist() {
        var ecabang=document.getElementById('cb_dist').value;

        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/viewdatabagi.php?module=caridataecust",
            data:"ucabang="+ecabang,
            success:function(data){
                $("#cb_ecabang").html(data);
            }
        });
    }
</script>
                
                