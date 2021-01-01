<?php
$abc=5;
if ($abc==5) {
    $sdfsdf=10;
}
?>
<!DOCTYPE html>
<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.2/css/select.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js"></script>
<script type="text/javascript">
$(document).ready( function () {
    $('#report').DataTable({
        paging:false,
        searching:false,
        ordering:false,
        fixedHeader: true,
        "info":false,
        select:true
    });
} );

</script>
</head>

<body>
<div class="container-fluid">
  <h3>Report Sales YTD per Daerah<br>
  Cabang Jakarta 2<br>
  Agustus 2017</h3><hr>
<table id="report" class="table table-bordered table-hovered">
    <thead>
        <tr>
            <th rowspan="2">Produk</th>
            <th rowspan="2">HNA</th>
            <th colspan="5">Monthly</th>
            <th colspan="5">Year to Date</th>
            <th colspan="2">Year</th>
        <tr>
            <th>Target</th>
            <th>Sales</th>
            <th>Ach</th>
            <th>Last Year</th>
            <th>Growth</th>
            <th>Target</th>
            <th>Sales</th>
            <th>Ach</th>
            <th>Last Year</th>
            <th>Growth</th>
            <th>Year Target</th>
            <th>Ach Year</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        <tr>
            <td><b>Data 1</b></td>
            <td><b>Data 2</b></td>
            <td><b>Data 3</b></td>
            <td><b>Data 4</b></td>
            <td><b>Data 5</b></td>
            <td><b>Data 6</b></td>
            <td><b>Data 7</b></td>
            <td><b>Data 8</b></td>
            <td><b>Data 9</b></td>
            <td><b>Data 10</b></td>
            <td><b>Data 11</b></td>
            <td><b>Data 12</b></td>
            <td><b>Data 13</b></td>
            <td><b>Data 14</b></td>
        </tr>
        
    </tbody>
</table>
</div>

</body>

</html>
