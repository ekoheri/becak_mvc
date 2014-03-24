<?php defined('SYS') or exit('Access Denied!');

/*******************************************
* Jawa Calendar
* Ver : 0.1
* dodi [at] bausastra [dot] com
********************************************/
class Java_calendar {
	private $local_time;
	public function __construct()
	{	
		$this->local_time = time();
	}
	private function intPart($floatNum) {
	  return($floatNum<-0.0000001? ceil($floatNum-0.0000001) : floor($floatNum+0.0000001));
	}
	private function jvdate($day,$month,$year) {
	  $julian = GregorianToJD($month, $day, $year);
	  if($julian>=1937808 && $julian<=536838867) {
	    $date = cal_from_jd($julian, CAL_GREGORIAN);
	    $d = $date['day']; $m = $date['month'] - 1; $y = $date['year'];
	
	    $mPart = ($m-13)/12;
	    $jd = $this->intPart((1461*($y+4800+$this->intPart($mPart)))/4)+
	          $this->intPart((367*($m-1-12*($this->intPart($mPart))))/12)-
	          $this->intPart((3*($this->intPart(($y+4900+$this->intPart($mPart))/100)))/4)+$d-32075;
	          
	    $l = $jd-1948440+10632;
	    $n = $this->intPart(($l-1)/10631);
	    $l = $l-10631*$n+354;
	    $j = ($this->intPart((10985-$l)/5316))*($this->intPart((50*$l)/17719))+($this->intPart($l/5670))*($this->intPart((43*$l)/15238));
	    $l = $l-($this->intPart((30-$j)/15))*($this->intPart((17719*$j)/50))-($this->intPart($j/16))*($this->intPart((15238*$j)/43))+29;
	    $m = $this->intPart((24*$l)/709);
	    $d = $l-$this->intPart((709*$m)/24);
	    $y = 30*$n+$j-30;
	    $yj = $y;//+512; Tahun jawa Tahun Hijriyah + 512
	    $h = ($julian+3)%5;
		$i = $yj;
		if ($i >= 8) {
		while ($i > 7):
	    $i = $i - 8;
		$yn = $i;
	    endwhile;
		} else {
		$yn = $i;
		}
	    if($julian<=1948439) $y--;
	    return array(
		  'dday'=>$date['dow'],
		  'javaDay'=>$d,
	    'javaMonth'=>$m,
		  'javaYear'=>$yj,
		  'javaDday'=>$h,
		  'yearName'=>$yn
	    );
	  }
	  else return false;
	}
	
	public function generate($intable = FALSE, $year = '', $month = '', $day='')
	{
		if ($year == '')
			$year  = date("Y", $this->local_time);
		
		if ($month == '')
			$month = date("m", $this->local_time);
		if ($day == '')
			$day = date("d", $this->local_time);
		$idMonth = array('01' => 'Januari', 
			'02' => 'Februari',
			'03' => 'Maret',
			'04' => 'April', 
			'05' => 'Mei', 
			'06' => 'Juni',
			'07' => 'Juli', 
			'08' => 'Agustus',
			'9' => 'September',
			'10' => 'Oktober', 
			'11' => 'November', 
			'12' => 'Desember');
		$jvMonth = Array( 
			'Sura','Sapar','Mulud','Bakdamulud','Jumadilawal',
			'Jumadilakhir','Rejeb','Ruwah','Pasa','Sawal','Dulkaidah','Besar');
		$hjMonth = array('Muharram',
			'Safar',
			'Rabiul awal',
			'Rabiul akhir',
			'Jumadil awal',
			'Jumadil akhir',
			'Rajab',
			'Sya\'ban',
			'Ramadhan',
			'Syawal',
			'Dzulkaidah',
			'Dzulhijjah');
		
		$idDay = Array('Minggu','Senin','Selasa','Rabu','Kamis','Jum\'at','Sabtu');
		$neptuD = Array('Minggu'=>5,'Senin'=>4,'Selasa'=>3,'Rabu'=>7,'Kamis'=>8,'Jum\'at'=>6,'Sabtu'=>9);
		$neptuP = Array('Pon'=>7,'Wage'=>5,'Kliwon'=>8,'Legi'=>5,'Pahing'=>9);
		$jvDay = Array('Pon','Wage','Kliwon','Legi','Pahing');
		$yName = Array('Be','Wawu','Jimakir','Alip','Ehe','Jimawal','Je','Dal');

		$date = $this->jvdate($day, $month, $year);
		if($intable) {	
			$ret = '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;" >';
			$ret .= '<tr><td rowspan="2">Tanggal</td><td>'.$day." ".$idMonth[$month]." ".$year.' Masehi</td></tr>';
			$ret .= '<tr><td>'. $date['javaDay']." ".$hjMonth[ $date['javaMonth']-1 ]." ".$date['javaYear'].' Hijriyah</td></tr>';
			$ret .= '<tr><td>Hari</td><td>'.$idDay[ $date['dday'] ]." ".$jvDay[ $date['javaDday'] ].'</td></tr>';
			$ret .= '<tr><td>Neptu</td><td>'.$neptuD[$idDay[ $date['dday'] ]]." + ". $neptuP[$jvDay[$date['javaDday'] ] ].' = '.($neptuD[$idDay[ $date['dday'] ]]+$neptuP[$jvDay[$date['javaDday'] ] ]).'</td></tr>';
			$ret .= '<tr><td>Wuku</td><td>'.$yName[ $date['yearName'] ].'</td></tr>';
		}
		else 
			$ret = $idDay[ $date['dday'] ]." ".$jvDay[ $date['javaDday'] ].", "
			.$day." ".$idMonth[$month]." ".$year.
			" (".$date['javaDay']." ".
			$hjMonth[ $date['javaMonth']-1 ]." ".$date['javaYear']." Hijriyah)";
				
			return $ret;
		}
}//end class
?>
