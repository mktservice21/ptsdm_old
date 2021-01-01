<?PHP
    session_start();
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);

    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    $divisi=$_POST['udivisi'];
    $kodeid=$_POST['kodeid'];
    $subkodeid=$_POST['subkodeid'];
    $cekhanya=$_POST['cekhanya'];
    
    echo "<input type='hidden' name='cb_tgltipe' id='cb_tgltipe' value='$tgltipe'>";
    echo "<input type='hidden' name='xtgl1' id='xtgl1' value='$tgl1'>";
    echo "<input type='hidden' name='xtgl2' id='xtgl2' value='$tgl2'>";
    echo "<input type='hidden' name='cb_divisi' id='cb_divisi' value='$divisi'>";
    
    echo "<input type='hidden' name='e_kodeid' id='e_kodeid' value='$kodeid'>";
    echo "<input type='hidden' name='e_kodeidsub' id='e_kodeidsub' value='$subkodeid'>";
    echo "<input type='hidden' name='e_hanya' id='e_hanya' value='$cekhanya'>";

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/mod_br_isicoaotc/aksi_brisicoaotc.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etgltipe=document.getElementById('cb_tgltipe').value;
        var etgl1 = document.getElementById("xtgl1").value;
        var etgl2 = document.getElementById("xtgl2").value;
        var edivisi=document.getElementById('cb_divisi').value;
                        
        var ekodeid=document.getElementById('e_kodeid').value;
        var esubkodeid=document.getElementById('e_kodeidsub').value;
        var ek=document.getElementById('e_hanya').value;
                        
        document.getElementById('u_module').value = module;
        document.getElementById('u_idmenu').value = idmenu;
        //alert(etgl2);
        var dataTable = $('#datatable').DataTable( {
            fixedHeader: false,
            fixedHeader: false,
            "processing": true,
            "serverSide": true,
            "lengthMenu": [[10, 50, 100, 100000000], [10, 50, 100, "All"]],
            "displayLength": 100000000,
            "columnDefs": [
                { "orderable": false, "targets": 1 },
                { "visible": false },
                { className: "text-right", "targets": [10, 13, 14] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16] }//nowrap

            ],
            
            "scrollY": 450,
            "scrollX": true,
            
            "ajax":{
                url :"module/mod_br_isicoaotc/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&kodeid="+ekodeid+"&cekhanya="+ek+"&subkodeid="+esubkodeid, // json datasource
                type: "post",  // method  , by default get
                data:"uperiode1="+etgl1+"&uperiode2="+etgl2,
                error: function(){  // error handling
                    $(".data-grid-error").html("");
                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#data-grid_processing").css("display","none");

                }
            }
            
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
</script>

<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 12px;
    }
    #datatable td { 
        font-size: 11px;
    }
</style>


<div class='x_content'>
<form method='POST' action='<?PHP echo "?module='breditcoaotc'&act=input&idmenu=144"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    <input type='hidden' id='u_module' name='u_module' value='' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    <div class='col-sm-4'>
        COA
        <div class="form-group">
            <select class='form-control input-sm' id="cb_coa" name="cb_coa">
                <?PHP
                include "../../config/koneksimysqli_it.php";
                include "../../config/fungsi_sql.php";
                
                $subposting = $_POST['kodeid'];
                if ($subposting=='06')
                    $query = "select distinct COA4 from dbmaster.posting_coa where 1=1 AND subpost = $subposting";
                else
                    $query = "select distinct COA4 from dbmaster.posting_coa where 1=1 AND subpost = '$subposting'";
                
                $tampil=mysqli_query($cnit, $query);
                $x=mysqli_fetch_array($tampil);
                $coa4=$x['COA4'];



                $posting = $_POST['subkodeid'];
                if (!empty($posting)) {
                    if ($subposting=='06')
                        $query = "select distinct COA4 from dbmaster.posting_coa where 1=1 AND (kodeid=$posting AND subpost = $subposting)";
                    else
                        $query = "select distinct COA4 from dbmaster.posting_coa where 1=1 AND (kodeid='$posting' AND subpost = '$subposting')";
                    $tampil=mysqli_query($cnit, $query);
                    $ketemu=  mysqli_num_rows($tampil);
                    if ($ketemu>0) {
                        $x=mysqli_fetch_array($tampil);
                        $coa4=$x['COA4'];
                    }
                }


                $query = "select distinct COA4, NAMA4 from dbmaster.v_coa_all where (DIVISI='OTC' or ifnull(DIVISI,'')='') order by NAMA4";

                $tampil=mysqli_query($cnit, $query);

                echo "<option value='' selected>-- Pilihan --</option>";
                while($a=mysqli_fetch_array($tampil)){
                    if ($a['COA4']==$coa4)
                        echo "<option value='$a[COA4]' selected>$a[NAMA4]</option>";
                    else
                        echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
                }
                ?>

            </select>
        </div>
    </div>
    
    <div class='col-sm-3'>
        <small>&nbsp;</small>
       <div class="form-group">
           <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Save" onclick='disp_confirm("Simpan ?")'>
       </div>
   </div>
    
    
    <table id='datatable' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='7px'>No</th><th><input type="checkbox" id="chkbtnall" value="select" onClick="SelAllCheckBox('chkbtnall', 'chkbox_id[]')"/></th>
                <th width='20px'>No ID</th>
                <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th>
                <th>NoSlip</th>
                <th width='20px'>Alokasi Budget</th><th width='20px'>Cabang</th>
                <th width='50px'>Keterangan</th><th width='50px'>Keterangan</th>
                <th>Usulan</th>
                <th width='50px'>Realisasi</th><th width='50px'>Tgl. Realisasi</th>
                <th>Jumlah Realisasi</th><th width='50px'>Selisih</th>
                <th>Tgl Report SBY</th><th width='50px'>Jenis Report SBY</th>
            </tr>
        </thead>
    </table>

</form>
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
</script>

<script>
    function disp_confirm(pText_)  {
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_isicoaotc/simpandata.php";
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    $('#mytgl01, #mytgl02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
</script>