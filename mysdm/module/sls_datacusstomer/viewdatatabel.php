<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    
    $pidcabang=$_POST['ucabang'];
    $pidarea=$_POST['uarea'];
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    echo "<input type='hidden' name='cb_cabang' id='cb_cabang' value='$pidcabang'>";
    echo "<input type='hidden' name='cb_area' id='cb_area' value='$pidarea'>";
    
?>

<script>
    $(document).ready(function() {
        var aksi = "module/sls_datacusstomer/aksi_datacusstomer.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var ecabang=document.getElementById('cb_cabang').value;
        var earea=document.getElementById('cb_area').value;
        var idisply="10";
        if (earea!="") {
            idisply="10";
        }
        var dataTable = $('#datatablecustotl').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "order": [[ 2, "asc" ], [ 3, "asc" ], [ 4, "asc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": idisply,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 3 },
                { "orderable": true, "targets": 4 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 490,
            "scrollX": true,

            "ajax":{
                url :"module/sls_datacusstomer/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&ucabang="+ecabang+"&uarea="+earea, // json datasource
                type: "post",  // method  , by default get
                data:"ucabang="+ecabang+"&uarea="+earea,
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



<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatablecustotl' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='30px'></th>
                    <th width='50px'>Status</th>
                    <th width='50px'>Discount</th>
                    <th width='80px'>Area</th>
                    <th width='80px'>Nama Sektor</th>
                    <th width='30px'>Id Cust</th>
                    <th width='100px'>Nama Customer</th>
                    <th width='100px'>Alamat 1</th>
                    <th width='100px'>Alamat 2</th>
                    <th width='50px'>Kode Pos</th>
                    <th width='80px'>Telp.</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>

<style>
    .divnone {
        display: none;
    }
    #datatablecustotl th {
        font-size: 13px;
    }
    #datatablecustotl td { 
        font-size: 11px;
    }
</style>

<script>
    function ProsesDataSimpan(_text, inid, istatus, idisc){
        var nstatus=document.getElementById(istatus).value;
        var ndisc=document.getElementById(idisc).value;
        
        if (inid=="") {
            alert("ID Customer Kosong...");
            return false;
        }
        
       $.ajax({
            type:"post",
            url:"module/sls_datacusstomer/aksi_datacusstomer.php?module=simpanperubahancust",
            data:"unid="+inid+"&ustatus="+nstatus+"&udisc="+ndisc,
            success:function(data){
                alert(data);
            }
        });
        
    }
</script>

