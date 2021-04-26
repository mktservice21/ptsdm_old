<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Master Kendaraan";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                elseif ($_GET['act']=="lihatpemakai")
                    echo "Pemakai $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli.php";
        $aksi="module/md_m_kendaraan/aksi_kendaraan.php";
        switch($_GET['act']){
            default:
                ?>
                <script>
                    $(document).ready(function() {
                        //alert(etgl1);
                        var dataTable = $('#datatablemsken, #datatablemskenpmk').DataTable( {
                            "stateSave": true,
                            "order": [[ 0, "asc" ]],
                            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
                            "displayLength": 10,
                            "columnDefs": [
                                { "visible": false },
                                { "orderable": false, "targets": 1 }

                            ],
                            "language": {
                                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                            }
                        } );
                        $('div.dataTables_filter input', dataTable.table().container()).focus();
                    } );
                    
                    
                    function ProsesData(ket, noid){
                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                            if (r==true) {

                                var txt="";
                                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                                    /*
                                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                                    if (textket == null || textket == "") {
                                        txt = textket;
                                    } else {
                                        txt = textket;
                                    }
                                    */
                                }


                                //document.write("You pressed OK!")
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                document.getElementById("d-form2").action = "module/md_m_kendaraan/aksi_kendaraan.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
                                document.getElementById("d-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                    }
                </script>
                
                <style>
                    .divnone {
                        display: none;
                    }
                    #datatablemsken th, #datatablemskenpmk th {
                        font-size: 13px;
                    }
                    #datatablemsken td, #datatablemskenpmk td { 
                        font-size: 11px;
                    }
                    .imgzoom:hover {
                        -ms-transform: scale(3.5); /* IE 9 */
                        -webkit-transform: scale(3.5); /* Safari 3-8 */
                        transform: scale(3.5);

                    }
                </style>
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2>
                                <?PHP
                                if ($_GET['act']=="lihatpemakai") {
                                ?>
                                    <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                                <?PHP
                                }
                                else
                                {
                                ?>
                                    <input class='btn btn-default' type=button value='Tambah Baru'
                                    onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                    <small>Klik tambah baru untuk menambah data</small>
                                <?PHP
                                }
                                ?>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        
                        <div class='x_content'>
                            <form method='POST' action='' id='d-form2' name='form2'>
                                <?PHP
                                if ($_GET['act']=="lihatpemakai") {
                                ?>
                                
                                    <table id='datatablemskenpmk' class='table table-striped table-bordered'>
                                        <thead>
                                            <tr>
                                                <th width='10px'>No</th>
                                                <th width='60px'>PLAT NOMOR</th>
                                                <th width='100px'>NAMA</th><th width='80px'>TGL. AWAL</th>
                                                <th width='80px'>TGL. AKHIR</th><th width='50px'>STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?PHP
                                            $no=1;
                                            $query = "SELECT a.noid, a.nourut, a.nopol, b.nama, a.tglawal, a.tglakhir, a.stsnonaktif "
                                                    . " from dbmaster.t_kendaraan_pemakai as a LEFT JOIN hrd.karyawan b "
                                                    . " ON a.karyawanid=b.karyawanId WHERE a.noid='$_GET[id]'";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($r= mysqli_fetch_array($tampil)) {
                                                $idno=$r['nourut'];
                                                $nopol=$r['nopol'];
                                                $nama=$r['nama'];
                                                $tglawal = date('d F Y', strtotime($r['tglawal']));
                                                $tglakhir="";
                                                if ($r['tglakhir']!="0000-00-00")
                                                    $tglakhir = date('d F Y', strtotime($r['tglakhir']));
                                                $sts="AKTIF";
                                                if ($r['stsnonaktif']=="Y") $sts="NON AKTIF";
                                                
                                                echo "<tr>";
                                                echo "<td>$no</td>";
                                                echo "<td>$nopol</td>";
                                                echo "<td>$nama</td>";
                                                echo "<td>$tglawal</td>";
                                                echo "<td>$tglakhir</td>";
                                                echo "<td>$sts</td>";
                                                echo "</tr>";
                                                $no++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                
                                <?PHP
                                }
                                else
                                {
                                ?>
                                
                                    <table id='datatablemsken' class='table table-striped table-bordered'>
                                        <thead>
                                            <tr>
                                                <th width='10px'>No</th><th  width='100px'>Aksi</th>
                                                <th width='60px'>PLAT NOMOR</th>
                                                <th width='40px'>JENIS</th><th width='80px'>MERK</th>
                                                <th width='100px'>TIPE</th><th width='50px'>WARNA</th><th width='50px'>TAHUN</th>
                                                <th width='70px'>STATUS</th>
                                                <th width='70px'>NORANGKA</th>
                                                <th width='70px'>NOMESIN</th>
                                                <th width='70px'>TGL. AKHIR STNK</th>
                                                <th width='70px'>JENIS ASURANSI</th>
                                                <th width='70px'>NAMA ASURANSI</th>
                                                <th width='70px'>NO. POLIS</th>
                                                <th width='70px'>PERIDOE</th>
                                                <th width='70px'>S/D.</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?PHP
                                            $no=1;
                                            $query = "SELECT a.*, b.nama_jenis from dbmaster.t_kendaraan as a LEFT JOIN dbmaster.t_kendaraan_jenis b "
                                                    . " ON a.jenis=b.jenis WHERE stsnonaktif<>'Y' ORDER BY a.sysnow , b.nama_jenis, a.merk";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($r= mysqli_fetch_array($tampil)) {
                                                $idno=$r['noid'];
                                                $nopol=$r['nopol'];
                                                $jenis=$r['jenis'];
                                                $nmjenis=$r['nama_jenis'];
                                                $merk=$r['merk'];
                                                $tipe=$r['tipe'];
                                                $warna=$r['warna'];
                                                $nstsken=$r['statuskendaraan'];
                                                //$thnbeli = date('Y', strtotime($r['tglbeli']));
                                                $thnbeli=$r['tahun'];
                                                $pnorangka=$r['norangka'];
                                                $pnomesin=$r['nomesin'];
                                                $ptglstnk=$r['tgltempostnk'];
                                                $pjnsasuransi=$r['jenis_asuransi'];
                                                $pnmasuransi=$r['nama_asuransi'];
                                                $nnopolis=$r['nopolis_asuransi'];
                                                $nperiode1=$r['polis_periode1'];
                                                $nperiode2=$r['polis_periode2'];
                                                
                                                
                                                
                                                if ($ptglstnk=="0000-00-00") $ptglstnk="";
                                                if ($nperiode1=="0000-00-00") $nperiode1="";
                                                if ($nperiode2=="0000-00-00") $nperiode2="";
                                                
                                                if (!empty($ptglstnk)) $ptglstnk= date('d/m/Y', strtotime($ptglstnk));
                                                if (!empty($nperiode1)) $nperiode1= date('d/m/Y', strtotime($nperiode1));
                                                if (!empty($nperiode2)) $nperiode2= date('d/m/Y', strtotime($nperiode2));
                                                
                                                $edit="<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&id=$idno'>Edit</a>";
                                                $hapus="<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesData('hapus', '$idno')\">";
                                                $lihatpemakai="<a class='btn btn-info btn-xs' href='?module=$_GET[module]&act=lihatpemakai&idmenu=$_GET[idmenu]&id=$idno'>Pemakai</a>";
                                                echo "<tr>";
                                                echo "<td>$no</td>";
                                                echo "<td nowrap>$edit $hapus $lihatpemakai</td>";
                                                echo "<td>$nopol</td>";
                                                echo "<td>$nmjenis</td>";
                                                echo "<td>$merk</td>";
                                                echo "<td>$tipe</td>";
                                                echo "<td>$warna</td>";
                                                echo "<td>$thnbeli</td>";
                                                echo "<td>$nstsken</td>";
                                                echo "<td>$pnorangka</td>";
                                                echo "<td>$pnomesin</td>";
                                                echo "<td>$ptglstnk</td>";
                                                echo "<td>$pjnsasuransi</td>";
                                                echo "<td>$pnmasuransi</td>";
                                                echo "<td nowrap>$nnopolis</td>";
                                                echo "<td>$nperiode1</td>";
                                                echo "<td>$nperiode2</td>";
                                                echo "</tr>";
                                                $no++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                
                                <?PHP
                                }
                                ?>
                                
                            </form>
                        </div>
                        
                    </div>
                </div>
                
                
                

                <?PHP

            break;

            case "tambahbaru":
                include "tambah.php";
            break;

            case "editdata":
                include "tambah.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

