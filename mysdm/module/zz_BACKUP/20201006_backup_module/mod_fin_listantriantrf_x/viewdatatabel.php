<?PHP
    session_start();
    
    
    $ptipe=$_POST['utipe'];
    $pbln=$_POST['ubulan'];
    $ptgl=$_POST['utanggal'];
    
    $pbulan= date("Ym", strtotime($pbln));
    $ptanggal= date("Y-m-d", strtotime($ptgl));
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    echo "<input type='hidden' name='cb_tgltipe' id='cb_tgltipe' value='$ptipe'>";
    echo "<input type='hidden' name='xbulan' id='xbulan' value='$pbln'>";
    echo "<input type='hidden' name='xtanggal' id='xtanggal' value='$ptgl'>";
    
    
    if ($ptipe=="T") {
        
        include "../../config/koneksimysqli.php";

        $pjmlbatasp=0;
        $pjmlbatast=0;
        $pjmlsudhinputp=0;
        $pjmlsudhinputt=0;

        $query = "select status_trf, jumlah as jmlbts from dbmaster.t_br_batas_trf GROUP BY 1";
        $ntampil= mysqli_query($cnmy, $query);
        $nketemu= mysqli_num_rows($ntampil);
        if ($nketemu>0) {
            while ($nrx= mysqli_fetch_array($ntampil)) {
                $pststrf=$nrx['status_trf'];
                if ($pststrf=="P") $pjmlbatasp=$nrx['jmlbts'];
                if ($pststrf=="T") $pjmlbatast=$nrx['jmlbts'];
            }
        }

        $query = "select status_trf, sum(jumlah) as jumlah from dbmaster.t_br_antrian WHERE IFNULL(stsnonaktif,'')<>'Y' AND tanggal='$ptanggal'";
        $query .= " GROUP BY 1";
        $ntampilp= mysqli_query($cnmy, $query);
        $nketemup= mysqli_num_rows($ntampilp);
        if ($nketemup>0) {
            while ($nr= mysqli_fetch_array($ntampilp)) {
                $pststrf=$nr['status_trf'];
                if ($pststrf=="P") $pjmlsudhinputp=$nr['jumlah'];
                if ($pststrf=="T") $pjmlsudhinputt=$nr['jumlah'];
            }
        }

        mysqli_close($cnmy);
        
        $psisainputp=(double)$pjmlbatasp-(double)$pjmlsudhinputp;
        $psisainputt=(double)$pjmlbatast-(double)$pjmlsudhinputt;
        
        $psisainputp=number_format($psisainputp,0,",",",");
        $psisainputt=number_format($psisainputt,0,",",",");
        
        echo "<table style='font-weight: bold;'>";
            echo "<tr>";
                echo "<td>Sisa Payroll </td><td> : </td><td> Rp. $psisainputp</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>Sisa Transfer </td><td> : </td><td> Rp. $psisainputt</td>";
            echo "</tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
    
    }
?>

<script>
    $(document).ready(function() {
        var aksi = "module/mod_fin_listantriantrf/aksi_listantriantrf.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etgltipe=document.getElementById('cb_tgltipe').value;
        var ebulan = document.getElementById("xbulan").value;
        var etanggal = document.getElementById("xtanggal").value;
        
        
        var dataTable = $('#datatablebmsby').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "order": [[ 1, "desc" ], [ 2, "desc" ], [ 3, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": true, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": false, "targets": 4 },
                { "orderable": false, "targets": 5 },
                { "orderable": false, "targets": 6 },
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6,7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/mod_fin_listantriantrf/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&utgltipe="+etgltipe+"&ubulan="+ebulan+"&utanggal="+etanggal, // json datasource
                type: "post",  // method  , by default get
                data:"ubulan="+ebulan+"&utanggal="+etanggal+"&utgltipe="+etgltipe,
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
        <table id='datatablebmsby' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='50px'></th>
                    <th width='50px'>Tanggal</th>
                    <th width='50px'>Status Trf.</th>
                    <th width='7px'>No Urut</th>
                    <th width='40px'>Nama</th>
                    <th width='50px'>No Divisi</th>
                    <th width='50px'>Jumlah Trf.</th>
                    <th width='80px'>Keterangan</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>

<style>
    .divnone {
        display: none;
    }
    #datatablebmsby th {
        font-size: 13px;
    }
    #datatablebmsby td { 
        font-size: 11px;
    }
</style>