<style>
    .divnone {
        display: none;
    }
    #datatableview th {
        font-size: 12px;
    }
    #datatableview td { 
        font-size: 11px;
    }
</style>

<script>
    $(document).ready(function() {
        var table = $('#datatableview').DataTable({
            "ordering": false,
            "columnDefs": [
                { "visible": false },
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7] }//nowrap

            ],
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true

        } );
    } );

    function ProsesData(ket, noid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var txt;
                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                    if (textket == null || textket == "") {
                        txt = textket;
                    } else {
                        txt = textket;
                    }
                }


                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                document.getElementById("demo-form2").action = "module/mod_br_entrybrluarkota/aksi_entrybrluarkota.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }



    }
</script>


        
<div class='col-md-12 col-sm-12 col-xs-12'>
    <div class='x_content'>
        <div class='x_panel'>
            <b>Data yang terakhir diinput (max 5 data)</b>
            <table id='datatableview' class='table table-striped table-bordered' width='100%'>
                <thead>
                    <tr>
                        <th width='70px'>Aksi</th>
                        <th width='40px'>No ID</th>
                        <th width='80px'>Yang Membuat</th>
                        <th width='50px'>Area</th>
                        <th width='40px'>Bulan</th>
                        <th width='40px'>Periode</th>
                        <th width='50px'>Jumlah</th>
                        <th width='50px'>Keterangan</th>

                    </tr>
                </thead>
                <tbody>
                    <?PHP
                    include "config/koneksimysqli.php";
                    $sql = "SELECT idrutin, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(bulan,'%M %Y') as bulan, DATE_FORMAT(periode1,'%d/%m/%Y') as periode1, "
                            . " DATE_FORMAT(periode2,'%d/%m/%Y') as periode2, "
                            . " divisi, karyawanid, nama, areaid, nama_area, FORMAT(jumlah,0,'de_DE') as jumlah, keterangan, "
                            . " COA4, NAMA4 ";
                    $sql.=" FROM dbmaster.v_brrutin0 ";
                    $sql.=" WHERE kode=2 AND stsnonaktif <> 'Y' and userid=$_SESSION[USERID] ";
                    $sql.=" order by idrutin desc limit 5 ";
                    $tampil=mysqli_query($cnmy, $sql);
                    while ($row=  mysqli_fetch_array($tampil)) {
                        $idno=$row['idrutin'];
                        $faksi = ""
                                . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]&id=$idno'>Edit</a> "
                                . "<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesData('hapus', '$idno')\">"
                            . "<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=$_GET[module]&brid=$idno&iprint=print',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Print</a>
                        ";
                        $nama = $row["nama"];
                        $namaarea = $row["nama_area"];
                        $bulan = $row["bulan"];
                        $periode = $row["periode1"]." - ".$row["periode2"];
                        $jumlah = $row["jumlah"];
                        $ket = $row["keterangan"];

                        echo "<tr>";
                        echo "<td>$faksi</td>";
                        echo "<td>$idno</td>";
                        echo "<td>$nama</td>";
                        echo "<td>$namaarea</td>";
                        echo "<td>$bulan</td>";
                        echo "<td>$periode</td>";
                        echo "<td>$jumlah</td>";
                        echo "<td>$ket</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
