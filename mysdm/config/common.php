<?php
function add_space($str_,$length_)
{
  $return_ = rtrim($str_);
  $i_ = $length_ - strlen($return_);
  for ($j=0; $j < $i_; $j++) {
    $return_ = $return_ . '&nbsp;';
  }
  return $return_;
}


function nama_bulan($bln_) {
	$return = '';
	switch ($bln_) {
		case '01' :
			$return = 'Januari';
			break;
		case '02' :
			$return = 'Februari';
			break;
		case '03' :
			$return = 'Maret';
			break;
		case '04' :
			$return = 'April';
			break;
		case '05' :
			$return = 'Mei';
			break;
		case '06' :
			$return = 'Juni';
			break;
		case '07' :
			$return = 'Juli';
			break;
		case '08' :
			$return = 'Agustus';
			break;
		case '09' :
			$return = 'September';
			break;
		case '10' :
			$return = 'Oktober';
			break;
		case '11' :
			$return = 'November';
			break;
		case '12' :
			$return = 'Desember';
			break;
	}
	return $return;
}

function plus1($pVar_,$pDigit_)
{
  if ($pVar_ == str_repeat('9',$pDigit_)) {
     $myVar_ = str_repeat('0',$pDigit_);
  } else {
     $myVar_ = intval($pVar_) + 1;
     $myVar_ = str_repeat('0',$pDigit_) . strval($myVar_);
     $myVar_ = substr($myVar_,0-$pDigit_);
  }
  return $myVar_;
}

function plus1_d($pTgl) {
    $tgl_ = mktime(0,0,0,date('m',$pTgl),date('d',$pTgl)+1,date('Y',$pTgl));
    return $tgl_;
}
function plus_d($pTgl,$pNumber) {
    $tgl_ = mktime(0,0,0,date('m',$pTgl),date('d',$pTgl)+$pNumber,date('Y',$pTgl));
    return $tgl_;
}

function start_of_month($time_)
{
  if (is_null($time_)) {
     $return_ = date('Y-m-') . '01';
  } else {
      if (empty($time_))
        $return_ = date('Y-m-',$time_) . '01';
      else
          $return_ = date('Y-m-') . '01';
  }
  return $return_;
}

function end_of_month($time_)
{
  $return_ = 0;
  if (is_null($time_)) {
     $date_ = date('Y-m-d');
  } else {
     $date_ = date('Y-m-d',$time_);
  }
  $month_ = substr($date_,5,2);
  $day_ = substr($date_,8,2);
  $year_ = substr($date_,0,4);
  if ($month_ == 12) {
     $month_ = 1;
     $year_ = $year_ + 1;
  } else {
     $month_ = $month_ + 1;
  }
  $return_ = mktime(0,0,0,$month_,0,$year_);
  return date('Y-m-d',$return_);
}
?>

