<?php

function get_10_from_60_exif( $ref , $gps )
{
	// 60進数から10進数に変換
	$data = convert_float( $gps[0] ) + ( convert_float($gps[1])/60 ) + ( convert_float($gps[2])/3600 ) ;
	$data = round($data,6);

	//南緯、または西経の場合はマイナスにして返却
	return ( $ref=='S' || $ref=='W' ) ? ( $data * -1 ) : $data ;
}

// [例:986/100]という文字列を[986÷100=9.86]というように数値に変換する関数
function convert_float( $str )
{
	$val = explode( '/' , $str ) ;
	return ( isset($val[1]) ) ? $val[0] / $val[1] : $str ;
}
?>