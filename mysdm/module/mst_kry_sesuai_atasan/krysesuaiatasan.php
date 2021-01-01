<?PHP
    $hari_ini = date("Y-m-d");
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Penyesuaian Atasan Karyawan";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "$judul";
                ?>
            </h3>
            <?PHP
            if ($_GET['act']=="tambahbaru" OR $_GET['act']=="editdata") {
            ?>
            *) Silakan diisi data atasannya sesuai yang terbaru.<br/>
            Data ini akan dipakai untuk Approve Biaya Rutin, Biaya Luar Kota dan Cash Advance.<br/>
            Jika ada perubahan, pengguna dapat langsung mengubah sendiri data atasannya sehingga lebih cepat dalam mendapatkan approve (self service).
            <?PHP }else{ ?>
            *) Klik ID untuk edit penyesuaian data.
            <?PHP } ?>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_brrutin/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div id='loading'></div>
                        <div id='c-data'>

                            <form method='POST' action='<?PHP echo "?module='mstsesuaidatakry'&act=input&idmenu=299"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

                                <div class='x_content'>
                                    <table id='datatablerut' class='table table-striped table-bordered' width='100%'>
                                        <?PHP
                                        echo "<thead><tr><th width='10px'>ID</th><th width='100px'>Karyawan</th>"
                                                . "<th width='100px'>Cabang</th><th width='100px'>Divisi</th>"
                                                . ""
                                                . "<th width='100px'>SPV</th><th width='100px'>DM</th>"
                                                . "<th width='100px'>SM</th><th width='100px'>GSM</th>"
                                                . "</tr></thead>";
                                        ?>
                                        <tbody>
                                            <?PHP
                                            $query = "select a.karyawanId, a.nama, a.iCabangId, c.nama nmcabang, a.areaId, d.nama nmarea, a.divisiId, a.divisiId2, 
                                                b.divisi1, b.divisi2, b.divisi3, b.atasanId, i.nama nmatasan, b.spv, e.nama nmspv, b.dm, f.nama nmdm, b.sm, g.nama nmsm, b.gsm, h.nama nmgsm 
                                                from hrd.karyawan a LEFT JOIN dbmaster.t_karyawan_posisi b on 
                                                a.karyawanId=b.karyawanId 
                                                LEFT JOIN mkt.icabang c on a.iCabangId=c.iCabangId
                                                LEFT JOIN mkt.iarea d on a.areaId=d.areaId and a.iCabangId=d.iCabangId 
                                                LEFT JOIN hrd.karyawan e on b.spv=e.karyawanId 
                                                LEFT JOIN hrd.karyawan f on b.dm=f.karyawanId 
                                                LEFT JOIN hrd.karyawan g on b.sm=g.karyawanId 
                                                LEFT JOIN hrd.karyawan h on b.gsm=h.karyawanId 
                                                LEFT JOIN hrd.karyawan i on b.atasanId=i.karyawanId 
                                                WHERE a.karyawanid='$_SESSION[IDCARD]'";
                                            $tampil= mysqli_query($cnmy, $query);
                                            $ketemu = mysqli_num_rows($tampil);
                                            if ($ketemu>0) {
                                                $row= mysqli_fetch_array($tampil);
                                                
                                                $nidkaryawan=$row['karyawanId'];
                                                $nnmkaryawan=$row['nama'];
                                                $nnmcab=$row['nmcabang'];
                                                $nnmarea=$row['nmarea'];
                                                $ndivisi=$row['divisiId'];
                                                $ndivisi1=$row['divisi1'];
                                                $ndivisi2=$row['divisi2'];
                                                
                                                $nnmatasan=$row['nmatasan'];
                                                $nnmspv=$row['nmspv'];
                                                $nnmdm=$row['nmdm'];
                                                $nnmsm=$row['nmsm'];
                                                $nnmgsm=$row['nmgsm'];
                                                
                                                $link_ = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]&id=$nidkaryawan'>".$nidkaryawan."</a>";
                                                
                                                echo "<tr>";
                                                echo "<td nowrap>$link_</td>";
                                                echo "<td nowrap>$nnmkaryawan</td>";
                                                echo "<td nowrap>$nnmcab</td>";
                                                echo "<td nowrap>$ndivisi</td>";
                                                //echo "<td nowrap>$nnmatasan</td>";
                                                echo "<td nowrap>$nnmspv</td>";
                                                echo "<td nowrap>$nnmdm</td>";
                                                echo "<td nowrap>$nnmsm</td>";
                                                echo "<td nowrap>$nnmgsm</td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                        
                                    </table>

                                </div>
                            </form>
                            
                        </div>

                    </div>
                </div>
                
                
                <style>
                    .divnone {
                        display: none;
                    }
                    #datatablerut th {
                        font-size: 13px;
                    }
                    #datatablerut td { 
                        font-size: 11px;
                    }
                    .imgzoom:hover {
                        -ms-transform: scale(3.5); /* IE 9 */
                        -webkit-transform: scale(3.5); /* Safari 3-8 */
                        transform: scale(3.5);

                    }
                </style>

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

