<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CommonModel extends Model
{

	public static function getecctionBYid($id)
	{
		$r = DB::table('election_master')->where('election_id', $id)->first();
		return ($r);
	}

	public static function getstatebystatecode($st)
	{
		return DB::table('m_state')->where('ST_CODE', $st)->first();
	}

	public static function getdistrictbydistrictno($st, $disno)
	{
		return DB::table('m_district')->where('ST_CODE', $st)->where('DIST_NO', $disno)->first();
	}

	public static function getacbyacno($st, $acno)
	{
		return DB::table('m_ac')->where('ST_CODE', $st)->where('AC_NO', $acno)->first();
	}

	public static function getpcbypcno($st, $pcno)
	{
		return DB::table('m_pc')->where('ST_CODE', $st)->where('PC_NO', $pcno)->first();
	}
	public static function getpcname($st, $pcno)
	{
		return DB::table('m_pc')->where('ST_CODE', $st)->where('PC_NO', $pcno)->first();
	}

	public static function getTotalPhasesForState($state_code)
	{
		$data = DB::table('pd_schedulemaster')->select('state_phase_no  as total_phase')->where('st_code', $state_code)->orderBy('state_phase_no', 'DESC')->limit(1)->first();
		if ($data) {
			return  $data->total_phase;
		} else {
			return  'N/A';
		}
	}

	public static function setDataBase($election_id)
	{
		$m_ele_his = ElectionHistory::where('id', '=', $election_id)->first();
		if ($m_ele_his) {
			$const_type = $m_ele_his->const_type;
			if ($const_type == 'PC') {
				Config::set('database.default', "pc");
				DB::reconnect('pc');
			} else {
				Config::set('database.default', "ac");
				DB::reconnect('ac');
			}
		}
	}
}
