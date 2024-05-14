<?php
/////////////////////////////////////////////////////
//  Code By Chanderkant for Encore Voter_Turnout App
//////////////////////////////////////////////////////

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommonModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public $commonModel = null;
    public $actualScheduleIds = [];

    public function __construct()
    {
    }

    public $successStatus = 200;
    public $createdStatus = 201;
    public $nocontentStatus = 204;
    public $notmodifiedStatus = 304;
    public $badrequestStatus = 400;
    public $unauthorizedStatus = 401;
    public $notfoundStatus = 404;
    public $intservererrorStatus = 500;

    function setActualPhaseForPcGenral($scheduleid)
    {
        if ($scheduleid == 1) {
            $this->actualScheduleIds = [1, 2];
        } else if ($scheduleid == 2) {
            $this->actualScheduleIds = [3, 4];
        } else if ($scheduleid == 3) {
            $this->actualScheduleIds = [5];
        } else if ($scheduleid == 4) {
            $this->actualScheduleIds = [6];
        } else if ($scheduleid == 5) {
            $this->actualScheduleIds = [7];
        } else if ($scheduleid == 6) {
            $this->actualScheduleIds = [8];
        } else if ($scheduleid == 7) {
            $this->actualScheduleIds = [9];
        }
    }

    function setActualPhaseForAcBye($scheduleid)
    {
        if ($scheduleid == 1) {
            $this->actualScheduleIds = [10];
        } else if ($scheduleid == 2) {
            $this->actualScheduleIds = [3];
        } else if ($scheduleid == 3) {
            $this->actualScheduleIds = [5];
        } else if ($scheduleid == 4) {
            $this->actualScheduleIds = [6];
        } else if ($scheduleid == 5) {
            $this->actualScheduleIds = [7];
        } else if ($scheduleid == 6) {
            $this->actualScheduleIds = [8];
        } else if ($scheduleid == 7) {
            $this->actualScheduleIds = [9];
        }
    }

    function setActualPhaseForAc($scheduleid, $st_code = '')
    {
        if ($st_code == 'S02') {
            $this->actualScheduleIds = [10];
        } else if ($st_code == 'S21') {
            $this->actualScheduleIds = [1];
        } else if ($scheduleid == 1) {
            $this->actualScheduleIds = [6];
        } else if ($scheduleid == 2) {
            $this->actualScheduleIds = [7];
        } else if ($scheduleid == 3) {
            $this->actualScheduleIds = [8];
        } else if ($scheduleid == 4) {
            $this->actualScheduleIds = [9];
        }
    }
    function getActualPhaseForPcGenral($scheduleid)
    {
        if ($scheduleid == 1 || $scheduleid == 2) {
            return 1;
        } else if ($scheduleid == 3 | $scheduleid == 4) {
            return 2;
        } else if ($scheduleid == 5) {
            return 3;
        } else if ($scheduleid == 6) {
            return 4;
        } else if ($scheduleid == 7) {
            return 5;
        } else if ($scheduleid == 8) {
            return 6;
        } else if ($scheduleid == 9) {
            return 7;
        }
    }

    function getActualPhaseForAcBye($scheduleid)
    {
        if ($scheduleid == 10) {
            return 1;
        } else if ($scheduleid == 3) {
            return 2;
        } else if ($scheduleid == 5) {
            return 3;
        } else if ($scheduleid == 6) {
            return 4;
        } else if ($scheduleid == 7) {
            return 5;
        } else if ($scheduleid == 8) {
            return 6;
        } else if ($scheduleid == 9) {
            return 7;
        }
        return 0;
    }

    function getActualPhaseForAc($scheduleid)
    {
        if ($scheduleid == 1) {
            return 1;
        } else if ($scheduleid == 2) {
            return 2;
        } else if ($scheduleid == 6) {
            return 1;
        } else if ($scheduleid == 7) {
            return 2;
        } else if ($scheduleid == 8) {
            return 3;
        } else if ($scheduleid == 9) {
            return 4;
        }
        return 0;
    }



    private static function compareByStateName($a, $b)
    {
        return strcmp($a["st_name"], $b["st_name"]);
    }

    private static function compareByStateName2($a, $b)
    {
        return strcmp($a["statename"], $b["statename"]);
    }

    private static function compareByDistName($a, $b)
    {
        return strcmp($a["dist_name"], $b["dist_name"]);
    }

    private static function compareByAcName($a, $b)
    {
        return strcmp($a["ac_name"], $b["ac_name"]);
    }

    private static function compareByPcName($a, $b)
    {
        return strcmp($a["ac_name"], $b["ac_name"]);
    }


    ///DrillDown Filters

    //#####Filter # 1 ELECTION Type Dropdown
    public function ElectionTypePt(Request $request)
    {
        try {
            $summary = array();
            $summary['success'] = true;
            $summary['message'] = "Election Master Details";
            $electiontype = DB::table('election_master')->get();
            $summary['electiontype'] = $electiontype;
            return $summary;
        } ///EndTry
        catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    } ///EndFunction getElectionTypeDetails


    //#######Filter # 2 Get Phase Name and ID be Election ID
    public function PhaseListPt(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'election_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            $userInputs = $request->all();
            $election_id = trim($userInputs['election_id']);
            $electionId = trim($userInputs['electiontype']);
            $statecode = trim($request->input('statecode', 0));
            $summary = array();
            $summary['success'] = true;
            CommonModel::setDataBase($userInputs['election_id']);
            $summary['message'] = "Phase by Electionid : $electionId";
            if (!empty($electionId)) {


                $date = date('Y-m-d');
                // $date = '2022-12-05';


                $phase_details = DB::table('m_election_details')
                    ->join('m_schedule', 'm_election_details.PHASE_NO', '=', 'm_schedule.SCHEDULEID')
                    ->select('m_election_details.ScheduleID', 'm_election_details.ELECTION_TYPE', 'm_election_details.PHASE_NO', 'm_election_details.ELECTION_TYPEID', 'm_schedule.DATE_POLL', 'm_election_details.StatePHASE_NO')
                    ->where(function ($q) use ($electionId, $date, $statecode) {
                        $q->where('m_election_details.ELECTION_TYPEID', $electionId);
                        $q->where('DATE_POLL', '<=', $date);
                        if ($statecode != '0') {
                            $q->where('m_election_details.ST_CODE', $statecode);
                        }
                    })
                    // ->groupBy('m_election_details.PHASE_NO')
                    ->groupBy('DATE_POLL')
                    ->get();

                if (count($phase_details) == 0) {
                    $phase_details = DB::table('m_election_details')
                        ->join('m_schedule', 'm_election_details.PHASE_NO', '=', 'm_schedule.SCHEDULEID')
                        ->select('m_election_details.ScheduleID', 'm_election_details.ELECTION_TYPE', 'm_election_details.PHASE_NO', 'm_election_details.ELECTION_TYPEID', 'm_schedule.DATE_POLL', 'm_election_details.StatePHASE_NO')
                        ->where(function ($q) use ($electionId, $statecode) {
                            $q->where('m_election_details.ELECTION_TYPEID', $electionId);
                            if ($statecode != 0) {
                                $q->where('m_election_details.ST_CODE', $statecode);
                            }
                        })
                        ->groupBy('m_election_details.PHASE_NO')
                        // ->groupBy('DATE_POLL')
                        ->limit(1)
                        ->get();
                }
                if (count($phase_details) > 0) {
                    $phaselist = array();
                    foreach ($phase_details as $phase) {
                        if ($phase->ScheduleID > 0) {
                            if ($electionId == '1') {
                                $scheduleid = $this->getActualPhaseForPcGenral($phase->ScheduleID);
                            } else if ($electionId == '4') {
                                $scheduleid = $this->getActualPhaseForAcBye($phase->ScheduleID);
                            } else {
                                $scheduleid = $this->getActualPhaseForAc($phase->ScheduleID);
                            }

                            $phaselist[] = array("schedule_id" => $scheduleid, "name" => $phase->StatePHASE_NO, "poll_date" => $phase->DATE_POLL);
                        }
                    }
                    return $phaselist;
                } else {
                    return array();
                }
            } else {
                $summary['message'] = "Blank or invalid Electionid";
                return $summary;
            }
            return $summary;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    } ///EndFunction 

    public function PhaseListPtNew(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'election_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            $userInputs = $request->all();
            $election_id = trim($userInputs['election_id']);
            $electionId = trim($userInputs['electiontype']);
            $statecode = trim($request->input('statecode', 0));
            $summary = array();
            $summary['success'] = true;
            $summary['message'] = "Phase by Electionid : $electionId";
            if (!empty($electionId)) {


                $date = date('Y-m-d');
                // $date = '2022-12-05';


                $phase_details = DB::table('m_election_details')
                    ->join('m_schedule', 'm_election_details.PHASE_NO', '=', 'm_schedule.SCHEDULEID')
                    ->select('m_election_details.ScheduleID', 'm_election_details.ELECTION_TYPE', 'm_election_details.PHASE_NO', 'm_election_details.ELECTION_TYPEID', 'm_schedule.DATE_POLL')
                    ->where(function ($q) use ($electionId, $date, $statecode) {
                        $q->where('m_election_details.ELECTION_TYPEID', $electionId);
                        $q->where('DATE_POLL', '<=', $date);
                        if ($statecode != '0') {
                            $q->where('m_election_details.ST_CODE', $statecode);
                        }
                    })
                    ->groupBy('m_election_details.PHASE_NO')
                    // ->groupBy('DATE_POLL')
                    ->get();

                if (count($phase_details) == 0) {
                    $phase_details = DB::table('m_election_details')
                        ->join('m_schedule', 'm_election_details.PHASE_NO', '=', 'm_schedule.SCHEDULEID')
                        ->select('m_election_details.ScheduleID', 'm_election_details.ELECTION_TYPE', 'm_election_details.PHASE_NO', 'm_election_details.ELECTION_TYPEID', 'm_schedule.DATE_POLL', 'm_election_details.StatePHASE_NO')
                        ->where(function ($q) use ($electionId, $statecode) {
                            $q->where('m_election_details.ELECTION_TYPEID', $electionId);
                            if ($statecode != 0) {
                                $q->where('m_election_details.ST_CODE', $statecode);
                            }
                        })
                        ->groupBy('m_election_details.PHASE_NO')
                        // ->groupBy('DATE_POLL')
                        ->limit(1)
                        ->get();
                }

                if (count($phase_details) > 0) {
                    $phaselist = array();
                    foreach ($phase_details as $phase) {
                        if ($phase->ScheduleID > 0) {
                            $phaselist[] = array("schedule_id" => $phase->ScheduleID, "name" => $phase->StatePHASE_NO, "poll_date" => $phase->DATE_POLL);
                        }
                    }

                    $success['success'] = true;
                    $success['phaselist'] = $phaselist;
                } else {
                    $success['success'] = false;
                    $success['phaselist'] = array();
                    return $success;
                }
                return $success;
            } else {
                $summary['message'] = "Blank or invalid Electionid";
                return $summary;
            }

            return $summary;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    } ///EndFunction 



    //########3 Filter # 3  Get Polling states list based on selected Election Type and Phase	
    public function StateListingPT(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'election_id' => 'required',
                'electionphase' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $election_id = trim($userInputs['election_id']);
            $electiontypeid = trim($userInputs['electiontype']);
            $date = date('Y-m-d');
            CommonModel::setDataBase($userInputs['election_id']);
            if ($electiontypeid == '1') {
                $this->setActualPhaseForPcGenral($scheduleid);
            } else if ($electiontypeid == '4') {
                $this->setActualPhaseForAcBye($scheduleid);
            } else {
                $this->setActualPhaseForAc($scheduleid);
            }


            if (!empty($electiontypeid)) {
                if ($scheduleid == 0) {

                    $phase_details = DB::table('pd_schedulemaster')->select(
                        'pd_schedulemaster.st_code',
                        'm_state.ST_NAME_HI',
                        'm_state.ST_NAME',
                        'pd_schedulemaster.schedule_id',
                        'm_schedule.DATE_POLL'
                    )
                        ->join('m_state', 'pd_schedulemaster.st_code', 'm_state.st_code')
                        ->join('m_schedule', 'pd_schedulemaster.schedule_id', '=', 'm_schedule.SCHEDULEID')
                        ->orderBy('election_type_id', 'ASC')
                        ->orderBy('schedule_id', 'DESC')
                        ->groupby('pd_schedulemaster.st_code')
                        ->where('election_type_id', $electiontypeid)
                        ->whereDate('m_schedule.DATE_POLL', '<=', $date)
                        ->get();
                    // dd($phase_details);
                } else {
                    $phase_details = DB::table('pd_schedulemaster')->select('pd_schedulemaster.st_code', 'm_state.ST_NAME_HI', 'm_state.ST_NAME', 'pd_schedulemaster.schedule_id', 'm_schedule.DATE_POLL')
                        ->join('m_state', 'pd_schedulemaster.st_code', 'm_state.st_code')
                        ->join('m_schedule', 'pd_schedulemaster.schedule_id', '=', 'm_schedule.SCHEDULEID')
                        ->whereIn('schedule_id', $this->actualScheduleIds)
                        ->orderBy('election_type_id', 'ASC')
                        ->orderBy('schedule_id', 'DESC')
                        ->groupby('pd_schedulemaster.st_code')
                        ->where('election_type_id', $electiontypeid)
                        ->whereDate('m_schedule.DATE_POLL', '<=', $date)
                        ->get();
                }

                if (count($phase_details) > 0) {
                    $statelist = array();
                    foreach ($phase_details as $state) {
                        if ($electiontypeid == '1') {
                            $sid = $this->getActualPhaseForPcGenral($state->schedule_id);
                        } else if ($electiontypeid == '4') {
                            $sid = $this->getActualPhaseForAcBye($state->schedule_id);
                        } else {
                            $sid = $this->getActualPhaseForAc($state->schedule_id);
                        }
                        if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                            $statelist[] = array(
                                "statename" => trim($state->ST_NAME_HI),
                                "statecode" => $state->st_code,
                                "poll_date" => $state->DATE_POLL,
                                "total_phase" => CommonModel::getTotalPhasesForState($state->st_code),
                                "schedule_id" => $sid
                            );
                        } else {

                            $statelist[] = array(
                                "statename" => trim($state->ST_NAME),
                                "statecode" => $state->st_code,
                                "poll_date" => $state->DATE_POLL,
                                "total_phase" => CommonModel::getTotalPhasesForState($state->st_code),
                                "schedule_id" => $sid
                            );
                        }
                    }
                    // usort($statelist, array($this, 'compareByStateName2'));
                    $success['statelist'] = $statelist;
                } else {
                    $success['statelist'] = array();
                    return $success['statelist'];
                }
                return $success['statelist'];
            }
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }


    //########4 Filter # 4  Get Polling PC based on selected Election Type, Phase and State
    public function PcListingPT(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'election_id' => 'required',
                'electionphase' => 'required',
                'statecode' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            $election_id = trim($userInputs['electiontype']);
            $statecode = trim($userInputs['statecode']);
            $eldata = CommonModel::getecctionBYid($electiontypeid);

            if ($eldata->election_sort_name == "PC") {
                if ($scheduleid == 0)
                    $pc_list_filtered = DB::table('m_election_details')->select('CONST_NO')->where('CONST_TYPE', '=', $eldata->election_sort_name)->where('ELECTION_TYPE', "PC")->where('ST_CODE', $statecode)->get();
                else
                    $pc_list_filtered = DB::table('m_election_details')->select('CONST_NO')->where('CONST_TYPE', '=', $eldata->election_sort_name)
                        //->where('ELECTION_TYPE',"PC")
                        ->where('ST_CODE', $statecode)
                        //->where('ScheduleID',$scheduleid)
                        ->get();

                if (count($pc_list_filtered) > 0) {
                    $pclisting = array();
                    foreach ($pc_list_filtered as $aclist) {
                        $pclisting[] = array("pcname" => trim(CommonModel::getpcbypcno($statecode, $aclist->CONST_NO)->PC_NAME), "pcno" => ($aclist->CONST_NO));
                    }
                    usort($pclisting, function ($a, $b) {
                        return strcmp($a['pcname'], $b['pcname']);
                    });
                }

                $success['success'] = true;
                $success['pclist'] = $pclisting;
            } else {
                if ($scheduleid == 0)
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $statecode)->where('const_type', "AC")->get();
                else {
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $statecode)->where('const_type', "AC")
                        //->where('schedule_id',$scheduleid)
                        ->get();
                }

                $pdilist = array();
                if (count($pddata)) {
                    foreach ($pddata as $pditem) {
                        $pdilist[] = $pditem->pd_scheduleid;
                    }
                }
                $mdtlist = DB::table('pd_scheduledetail_publish')->join('m_district', 'pd_scheduledetail.dist_no', '=', 'm_district.DIST_NO')->select('pd_scheduledetail.dist_no', 'm_district.DIST_NAME')->whereIn('pd_scheduleid', $pdilist)->groupby('dist_no')->get();
                $district_listing = array();
                foreach ($mdtlist as $drec) {
                    $district_listing[] = array("dist_no" => $drec->dist_no, "district_name" => $drec->DIST_NAME);
                }
                $success['success'] = true;
                $success['districtlist'] = $district_listing;
            }

            return $success;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }


    //########4 Filter # 5  Get Polling District based on selected Election Type, Phase and State
    public function DistListingPT(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'election_id' => 'required',
                'electionphase' => 'required',
                'statecode' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }


            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            $election_id = trim($userInputs['electiontype']);
            $statecode = trim($userInputs['statecode']);
            $eldata = CommonModel::getecctionBYid($electiontypeid);
            if ($eldata->election_sort_name == "PC") {
                if ($scheduleid == 0)
                    $pc_list_filtered = DB::table('m_election_details')->select('CONST_NO')->where('CONST_TYPE', '=', $eldata->election_sort_name)->where('ELECTION_TYPE', "PC")->where('ST_CODE', $statecode)->get();
                else {
                    $pc_list_filtered = DB::table('m_election_details')->select('CONST_NO')->where('CONST_TYPE', '=', $eldata->election_sort_name)->where('ELECTION_TYPE', "PC")->where('ST_CODE', $statecode)
                        //->where('ScheduleID',$scheduleid)
                        ->get();
                }
                if (count($pc_list_filtered) > 0) {
                    $pclisting = array();
                    foreach ($pc_list_filtered as $aclist) {
                        $pclisting[] = array("pcname" => trim(CommonModel::getpcbypcno($statecode, $aclist->CONST_NO)->PC_NAME), "pcno" => ($aclist->CONST_NO));
                    }
                    usort($pclisting, function ($a, $b) {
                        return strcmp($a['pcname'], $b['pcname']);
                    });
                }

                $success['success'] = true;
                $success['pclist'] = $pclisting;
            } else {
                if ($scheduleid == 0)
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $statecode)->where('const_type', "AC")->get();
                else {
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $statecode)->where('const_type', "AC")
                        //->where('schedule_id',$scheduleid)
                        ->get();
                }

                $pdilist = array();
                if (count($pddata)) {
                    foreach ($pddata as $pditem) {
                        $pdilist[] = $pditem->pd_scheduleid;
                    }
                }
                $mdtlist = DB::table('pd_scheduledetail_publish')->join('m_district', 'pd_scheduledetail.dist_no', '=', 'm_district.DIST_NO')->select('pd_scheduledetail.dist_no', 'm_district.DIST_NAME')->whereIn('pd_scheduleid', $pdilist)->groupby('dist_no')->get();
                $district_listing = array();
                foreach ($mdtlist as $drec) {
                    $district_listing[] = array("dist_no" => $drec->dist_no, "district_name" => $drec->DIST_NAME);
                }
                $success['success'] = true;
                $success['districtlist'] = $district_listing;
            }

            return $success;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }



    //########5 Filter # 6  Get Polling AC based on selected Election Type, Phase, State and PC
    public function PC2AcListingPT(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'electionphase' => 'required',
                'statecode' => 'required',
                'pc_no' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            $statecode = trim($userInputs['statecode']);
            $pcno = trim($userInputs['pc_no']);
            $eldata = CommonModel::getecctionBYid($electiontypeid);
            $success['phase_name'] = $scheduleid;
            $success['phase'] = $scheduleid;
            if ($scheduleid != 0) {
                $success['poll_date'] = DB::table('m_schedule')->select('DATE_POLL')->where('scheduleid', $scheduleid)->first()->DATE_POLL;
            }

            $success['total_phase'] = CommonModel::getTotalPhasesForState($statecode);
            if ($eldata->election_sort_name == "PC") {
                $pc_list_filtered = DB::table('m_ac')->where('ST_CODE', $statecode)->where('PC_NO', $pcno)->get();
                if (count($pc_list_filtered) > 0) {
                    $pclisting = array();
                    foreach ($pc_list_filtered as $aclist) {
                        $pclisting[] = array("acname" => $aclist->AC_NAME, "acno" => $aclist->AC_NO);
                    }
                    usort($pclisting, function ($a, $b) {
                        return strcmp($a["acname"], $b["acname"]);
                    });
                    $success['success'] = true;
                    $success['aclist'] = $pclisting;
                    return $success;
                } else {
                    $success['success'] = false;
                    $success['aclist'] = array();
                    return $success;
                }
            } else {
                $pc_list_filtered = DB::table('m_election_details')->where('ST_CODE', $statecode)
                    ->where('ScheduleID', $scheduleid)
                    ->get();
                if (count($pc_list_filtered) > 0) {
                    $pclisting = array();
                    foreach ($pc_list_filtered as $aclist) {

                        $pclisting[] = array("acname" => trim(CommonModel::getacbyacno($statecode, $aclist->CONST_NO)->AC_NAME), "acno" => $aclist->CONST_NO);
                    }
                    usort($pclisting, function ($a, $b) {
                        return strcmp($a["acname"], $b["acname"]);
                    });
                    $success['success'] = true;
                    $success['aclist'] = $pclisting;
                } else {
                    $success['success'] = false;
                    $success['aclist'] = array();
                    return $success;
                }

                return $success;
            }
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }

    //########5 Filter # 7  Get Polling AC based on selected Election Type, Phase, State and District
    public function Dist2AcListingPT(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'electionphase' => 'required',
                'statecode' => 'required',
                'district_no' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            $statecode = trim($userInputs['statecode']);
            $distno = trim($userInputs['district_no']);

            $eldata = CommonModel::getecctionBYid($electiontypeid);
            if ($scheduleid != 0) {
                $success['poll_date'] = DB::table('m_schedule')->select('DATE_POLL')->where('scheduleid', $scheduleid)->first()->DATE_POLL;
            }
            if ($eldata->election_sort_name == "PC") {
                $pc_list_filtered = DB::table('m_ac')->where('ST_CODE', $statecode)->where('PC_NO', $distno)->get();
                if (count($pc_list_filtered) > 0) {
                    $pclisting = array();
                    foreach ($pc_list_filtered as $aclist) {
                        $pclisting[] = array("acname" => $aclist->AC_NAME, "acno" => $aclist->AC_NO);
                    }
                    usort($pclisting, function ($a, $b) {
                        return strcmp($a["acname"], $b["acname"]);
                    });
                    $success['success'] = true;
                    $success['aclist'] = $pclisting;
                    $success['total_phase'] = CommonModel::getTotalPhasesForState($statecode);
                } else {
                    $success['success'] = false;
                    $success['aclist'] = array();
                    return $success;
                }
            } else {
                if ($distno == 0) {
                    $aclist = DB::table('m_ac')->select('AC_NO', 'AC_NAME', 'AC_NAME_V1')->where('ST_CODE', $statecode)->orderBy('AC_NAME')->get();
                } else {
                    $aclist = DB::table('m_ac')->select('AC_NO', 'AC_NAME', 'AC_NAME_V1')->where('ST_CODE', $statecode)->where('DIST_NO_HDQTR', $distno)->orderBy('AC_NAME')->get();
                }

                $mac = DB::table('m_election_details')->select('CONST_NO')->where('ST_CODE', $statecode)->where('CONST_TYPE', $eldata->election_sort_name)->where('ELECTION_TYPE', $eldata->election_type)->get();
                $constarray = array();
                if (count($mac) > 0) {
                    foreach ($mac as $crec) {
                        $constarray[] = $crec->CONST_NO;
                    }
                }
                $ac_listing = array();
                foreach ($aclist as $acrec) {
                    if (in_array($acrec->AC_NO, $constarray))
                        $ac_listing[] = array("acno" => $acrec->AC_NO, "acname" => $acrec->AC_NAME, "ac_name_regional" => $acrec->AC_NAME_V1);
                }
                $success['success'] = true;
                $success['aclist'] = $ac_listing;
                $success['total_phase'] = CommonModel::getTotalPhasesForState($statecode);
            }
            return $success;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }


    //########6 Filter # 8  Get All Phase for given State
    public function PollDate(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'electionphase' => 'required',
                'election_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            $userInputs = $request->all();
            $electiontype = trim($userInputs['electiontype']);
            $phase = trim($userInputs['electionphase']);
            $electionid = trim($userInputs['election_id']);

            $date_data = DB::table('m_schedule')->select('DATE_POLL')->where('SCHEDULEID', $phase)->first();

            if (count($date_data) > 0) {

                $success['success'] = true;
                $success['poll_date'] = $date_data->DATE_POLL;
            } else {
                $success['success'] = false;
                $success['poll_date'] = NULL;
                return $success;
            }
            return $success;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }

    ############ PAGE CALLS #################

    public function HomeDashboard(Request $request)
    {
        /* ------------------ FOR AC --------------*/
        try {
            $userInputs = $request->all();
            $scheduleid = 0;
            $electiontypeid = 0;

            $date = date('Y-m-d');
            //$date ='2021-10-29';


            $schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
                ->where('DATE_POLL', '<=', $date)
                ->orderBy('DATE_POLL', 'DESC')
                ->first();

            if (@$schedule->SCHEDULEID) {
                $scheduleid = @$schedule->SCHEDULEID;
            } else {
                $schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
                    ->where('DATE_POLL', '>=', $date)
                    ->first();
                $scheduleid = @$schedule->SCHEDULEID;
                @Carbon::parse(@$schedule->DATE_POLL)->format('d-m-Y');
            }


            $summary = array();
            $summary['success'] = true;
            $summary['message'] = "Poll TurnOut Home Dashboard [StateWise]";
            $summary['phase'] = $scheduleid;

            $final_per_ios = 0;
            $result = array();
            if ($scheduleid == 0)
                $pddata = DB::table('pd_schedulemaster')
                    //->where('election_type_id',$electiontypeid)
                    ->get();
            else
                $pddata = DB::table('pd_schedulemaster')
                    ->where('schedule_id', $scheduleid)
                    //->where('election_type_id',$electiontypeid)
                    ->get();


            $total_est = 0;
            $pdilist = array();
            if (count($pddata)) {
                foreach ($pddata as $pditem) {
                    $pdilist[] = $pditem->pd_scheduleid;
                }
            }

            if ($scheduleid == 0)
                $stlist = DB::table('pd_schedulemaster')->select('pd_schedulemaster.st_code', 'election_type_id as ELECTION_TYPEID', 'schedule_id as PHASE_NO')
                    ->join('m_state', 'pd_schedulemaster.st_code', 'm_state.st_code')
                    //->where('schedule_id',$scheduleid)
                    ->orderBy('election_type_id', 'ASC')
                    ->orderBy('m_state.st_name', 'ASC')
                    ->groupby('pd_schedulemaster.election_type_id', 'pd_schedulemaster.st_code')
                    //->where('election_type_id',$electiontypeid)
                    ->get();
            else
                $stlist = DB::table('pd_schedulemaster')->select('pd_schedulemaster.st_code', 'election_type_id as ELECTION_TYPEID', 'schedule_id as PHASE_NO')
                    ->join('m_state', 'pd_schedulemaster.st_code', 'm_state.st_code')
                    //->where('schedule_id',$scheduleid)
                    ->orderBy('election_type_id', 'ASC')
                    ->orderBy('m_state.st_name', 'ASC')
                    ->groupby('pd_schedulemaster.election_type_id', 'pd_schedulemaster.st_code')

                    //->where('election_type_id',$electiontypeid)
                    ->get();

            $statids = array();
            $tcnt = count($stlist);
            $st_aggr = 0;
            $gt_voter = 0;
            $gt_elector = 0;
            foreach ($stlist as $sttid) {

                $staclist = DB::table('pd_scheduledetail_publish')->select('ac_no')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->get();
                $acount = count($staclist);
                $tempar = array();
                $tempar['st_code'] = $sttid->st_code;
                if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME_HI));
                } else {
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME));
                }
                $tempar['total_ac'] = $acount;
                $tempar['voters'] = 0;
                $tempar['r1_total'] = 0;
                $tempar['final_total'] = 0;

                $tempar['final_per'] = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('est_turnout_total');
                $telec = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->where('ELECTION_TYPEID', $sttid->ELECTION_TYPEID)->whereIn('pd_scheduleid', $pdilist)->sum('electors_total');
                $tvoter = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->where('ELECTION_TYPEID', $sttid->ELECTION_TYPEID)->whereIn('pd_scheduleid', $pdilist)->sum('est_voters');
                if (($tvoter > 0) && ($telec > 0))
                    $tempar['final_per'] = number_format(($tvoter * 100) / $telec, 2, ".", "");
                else
                    $tempar['final_per'] = 0;

                $final_per_ios = $tempar['final_per'];
                $tempar['electiontype'] = $sttid->ELECTION_TYPEID;
                $tempar['electionphase'] = '' . $sttid->PHASE_NO . '';
                $tempar['election_id'] = 21;
                $gt_voter += $tvoter;
                $gt_elector += $telec;
                $st_aggr = $st_aggr + $tempar['final_per'];
                $statids[] = $tempar;
            }
            $tempdata = DB::table('pd_scheduledetail_publish')->orderBy('updated_at', 'DESC')->first();

            /* ------------------ FOR AC END --------------*/

            /* ---------------------- FOR PC ----------------------- */
            $summary['data'] = $statids;
            $aclist = DB::table('pd_scheduledetail_publish')->select('ac_no')->whereIn('pd_scheduleid', $pdilist)->get();
            $tcnt = count($aclist);
            $oall = array();
            $oall['voters'] = $gt_voter;
            $oall['total'] = $gt_elector;
            if (($gt_voter > 0) && ($gt_elector > 0))
                $oall['percentage'] = $final_per_ios;
            else
                $oall['percentage'] = $final_per_ios;
            $summary['overall'] = $oall;
            $summary['total_ac'] = $tcnt;
            $summary['total_state'] = $tcnt;
            $summary['poll_date'] = @Carbon::parse(@$schedule->DATE_POLL)->format('d-m-Y');
            $summary['last_update_time'] = @$tempdata->updated_at;
            $summary['current_time'] = date('h:i A d-m-Y');
            return $summary;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }





    public function HomePt(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'electionphase' => 'required',
                'election_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }
            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            CommonModel::setDataBase($userInputs['election_id']);
            $summary = array();
            $summary['success'] = true;
            $summary['message'] = "Poll TurnOut Home [StateWise]";
            $summary['phase'] = $scheduleid;
            $summary['phase_name'] = $scheduleid;
            $eldata = CommonModel::getecctionBYid($electiontypeid);
            if ($electiontypeid == '1') {
                $this->setActualPhaseForPcGenral($scheduleid);
            } else if ($electiontypeid == '4') {
                $this->setActualPhaseForAcBye($scheduleid);
            } else {
                $this->setActualPhaseForAc($scheduleid);
            }

            if ($scheduleid == 0) {
                $pddata = DB::table('pd_schedulemaster')
                    ->where('election_type_id', $electiontypeid)
                    ->get();
            } else {

                $pddata = DB::table('pd_schedulemaster')
                    ->whereIn('schedule_id', $this->actualScheduleIds)
                    ->where('election_type_id', $electiontypeid)
                    ->get();
            }
            $pdilist = array();
            if (count($pddata)) {
                foreach ($pddata as $pditem) {
                    $pdilist[] = $pditem->pd_scheduleid;
                }
            }
            if ($scheduleid == 0) {
                $stlist = DB::table('pd_schedulemaster')->select('pd_schedulemaster.st_code')
                    ->join('m_state', 'pd_schedulemaster.st_code', 'm_state.st_code')
                    ->orderBy('election_type_id', 'ASC')
                    ->orderBy('m_state.st_name', 'ASC')
                    ->groupby('pd_schedulemaster.st_code')
                    ->where('election_type_id', $electiontypeid)
                    ->get();
            } else {
                $stlist = DB::table('pd_schedulemaster')->select('pd_schedulemaster.st_code')
                    ->join('m_state', 'pd_schedulemaster.st_code', 'm_state.st_code')
                    ->whereIn('schedule_id', $this->actualScheduleIds)
                    ->orderBy('election_type_id', 'ASC')
                    ->orderBy('m_state.st_name', 'ASC')
                    ->groupby('pd_schedulemaster.st_code')
                    ->where('election_type_id', $electiontypeid)
                    ->get();
            }
            $statids = array();
            $tcnt = count($stlist);
            $st_aggr = 0;
            $gt_voter = 0;
            $gt_elector = 0;
            foreach ($stlist as $sttid) {
                $staclist = DB::table('pd_scheduledetail_publish')->select('ac_no')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->get();
                $acount = count($staclist);
                $tempar = array();
                $tempar['st_code'] = $sttid->st_code;
                if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME_HI));
                } else {
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME));
                }
                $tempar['total_ac'] = $acount;
                $tempar['voters'] = 0;
                $tempar['r1_total'] = 0;
                $tempar['final_total'] = 0;
                $tempar['total_phase'] = CommonModel::getTotalPhasesForState($sttid->st_code);

                $tempar['final_per'] = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('est_turnout_total');
                $telec = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('electors_total');
                $tvoter = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('est_voters');
                //print_r("\nFinal Electors : ");print_r($telec);
                //print_r("\nFinal Vorters : ");print_r($tvoter);
                if (($tvoter > 0) && ($telec > 0))
                    $tempar['final_per'] = number_format(($tvoter * 100) / $telec, 2, ".", "");
                else
                    $tempar['final_per'] = 0;
                //print_r($tempar['final_per']);die;
                //DB::selectRaw(select ROUND((SUM(est_voters) * 100 )/SUM(electors_total),2) as total_percent from `pd_scheduledetail` where  `scheduleid` = 2); 
                $gt_voter += $tvoter;
                $gt_elector += $telec;
                $st_aggr = $st_aggr + $tempar['final_per'];
                $statids[] = $tempar;
            }

            usort($statids, array($this, 'compareByStateName'));

            $schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
                ->whereIn('SCHEDULEID', $this->actualScheduleIds)
                ->orderBy('DATE_POLL', 'DESC')
                ->first();

            //die;
            $summary['data'] = $statids;
            $aclist = DB::table('pd_scheduledetail_publish')->select('ac_no')->whereIn('pd_scheduleid', $pdilist)->get();
            $tcnt = count($aclist);
            $oall = array();
            $oall['voters'] = $gt_voter;
            $oall['total'] = $gt_elector;
            if (($gt_voter > 0) && ($gt_elector > 0)) {
                $oall['percentage'] = round(($gt_voter / $gt_elector) * 100, 2);
            } else {
                $oall['percentage'] = 0;
            }
            $summary['overall'] = $oall;
            $summary['total_ac'] = $tcnt;
            $summary['total_state'] = $tcnt;
            $tempdata = DB::table('pd_scheduledetail_publish')->orderBy('updated_at', 'DESC')->first();
            $summary['last_update_time'] = @$tempdata->updated_at;
            $summary['poll_date'] = Carbon::parse(@$schedule->DATE_POLL)->format('d-m-Y');
            return $summary;
        } catch (Exception $ex) {
            return $ex->getMessage();
            Log::error($ex);

            throw $ex;
        }
    }

    public function DISTwisePt(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'electionphase' => 'required',
                'election_id' => 'required',
                'statecode' => 'required',
                'district_no' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }
            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            $stcode = trim($userInputs['statecode']);
            $distno = trim($userInputs['district_no']);
            CommonModel::setDataBase($userInputs['election_id']);
            if ($electiontypeid == '1') {
                $this->setActualPhaseForPcGenral($scheduleid);
            } else if ($electiontypeid == '4') {
                $this->setActualPhaseForAcBye($scheduleid);
            } else {
                $this->setActualPhaseForAc($scheduleid);
            }
            if ($scheduleid == 0) {
                if ($distno == 0)
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $stcode)->get();
                else
                    $pddata = DB::table('pd_schedulemaster')
                        ->where('st_code', $stcode)
                        ->where('district_no', $distno)
                        ->get();
            } else {
                $totatlPhaseCount = $this->getTotalPhasesCount($stcode);
                if ($distno == 0) {
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $stcode)
                        ->where(function ($q) use ($totatlPhaseCount, $scheduleid) {
                            if ($totatlPhaseCount > 1) {
                                $q->whereIn('schedule_id', $this->actualScheduleIds);
                            }
                        })
                        ->where('election_type_id', $electiontypeid)
                        ->get();
                } else {
                    $pddata = DB::table('pd_schedulemaster')
                        ->where('st_code', $stcode)

                        ->where(function ($q) use ($totatlPhaseCount, $electiontypeid, $distno) {
                            if ($totatlPhaseCount > 1) {
                                $q->whereIn('schedule_id', $this->actualScheduleIds);
                            }
                            if ($electiontypeid == '1' || $electiontypeid == '2') {
                                $q->where('pc_no', $distno);
                            } else {
                                $q->where('district_no', $distno);
                            }
                        })
                        ->where('election_type_id', $electiontypeid)
                        ->get();
                }
            }

            $eldata = CommonModel::getecctionBYid($electiontypeid);
            $pdilist = array();
            if (count($pddata)) {
                foreach ($pddata as $pditem) {
                    $pdilist[] = $pditem->pd_scheduleid;
                }
            }

            $totatlPhaseCount = $this->getTotalPhasesCount($stcode);
            $summary = array();

            $summary['success'] = true;
            $summary['message'] = "Poll TurnOut Home [District Wise]";
            $summary['phase'] = $scheduleid;
            if ($scheduleid != 0) {
                $summary['poll_date'] = DB::table('m_schedule')->select('DATE_POLL')->whereIn('scheduleid', $this->actualScheduleIds)->first()->DATE_POLL;
            }
            if ($electiontypeid == '1' || $electiontypeid == '2') {
                $distlist = DB::table('pd_scheduledetail_publish')->select('st_code', 'dist_no', 'pc_no')->whereIn('pd_scheduleid', $pdilist)->groupby('pc_no')->get();
            } else {
                $distlist = DB::table('pd_scheduledetail_publish')->select('st_code', 'dist_no', 'pc_no')->whereIn('pd_scheduleid', $pdilist)->groupby('dist_no')->get();
            }

            $aclist = DB::table('pd_scheduledetail_publish')->select('st_code', 'dist_no')->whereIn('pd_scheduleid', $pdilist)->get();
            $statids = array();
            $tpcnt = count($distlist);
            $tacnt = count($aclist);
            $st_name = '';

            foreach ($distlist as $pcdata) {
                $tempar = array();
                $tempar['st_code'] = $pcdata->st_code;
                if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($pcdata->st_code)->ST_NAME_HI));
                } else {
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($pcdata->st_code)->ST_NAME));
                }
                if ($electiontypeid == '1' || $electiontypeid == '2') {
                    $tempar['dist_no'] = $pcdata->pc_no;

                    if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getpcname($pcdata->st_code, $pcdata->pc_no)->PC_NAME_HI));
                    } else {
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getpcname($pcdata->st_code, $pcdata->pc_no)->PC_NAME));
                    }
                } else {
                    $tempar['dist_no'] = $pcdata->dist_no;

                    if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getdistrictbydistrictno($pcdata->st_code, $pcdata->dist_no)->DIST_NAME_HI));
                    } else {
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getdistrictbydistrictno($pcdata->st_code, $pcdata->dist_no)->DIST_NAME));
                    }
                }
                $tempar['ac_name'] = $tempar['dist_name'];
                $tempar['ac_no'] = $tempar['dist_no'];
                $tempar['voters'] = 0;
                $tempar['final_total'] = 0;
                $sql = DB::table('pd_scheduledetail_publish')->where('st_code', $pcdata->st_code);
                if ($electiontypeid == '1' || $electiontypeid == '2') {
                    $sql->where('pc_no', $pcdata->pc_no);
                } else {
                    $sql->where('dist_no', $pcdata->dist_no);
                }
                $sql->whereIn('pd_scheduleid', $pdilist);
                $telec = $sql->sum('electors_total');
                $sql2 = DB::table('pd_scheduledetail_publish')->where('st_code', $pcdata->st_code);
                if ($electiontypeid == '1' || $electiontypeid == '2') {
                    $sql2->where('pc_no', $pcdata->pc_no);
                } else {
                    $sql2->where('dist_no', $pcdata->dist_no);
                }
                $tvoter = $sql2->whereIn('pd_scheduleid', $pdilist)->sum('est_voters');
                // dd($tvoter);
                if (($tvoter > 0) && ($telec > 0))
                    $tempar['final_per'] = number_format(($tvoter * 100) / $telec, 2, ".", "");
                else
                    $tempar['final_per'] = 0;
                $statids[] = $tempar;
                $st_name = $tempar['st_name'];
            }

            usort($statids, array($this, 'compareByDistName'));
            // dd($statids);
            $summary['data'] = $statids;
            $oall = array();



            $telec = DB::table('pd_scheduledetail_publish')->where(function ($q) use ($totatlPhaseCount, $scheduleid) {
                if ($totatlPhaseCount > 1) {
                    $q->whereIn('scheduleid', $this->actualScheduleIds);
                }
            })->whereIn('pd_scheduleid', $pdilist)->sum('electors_total');
            $tvoter = DB::table('pd_scheduledetail_publish')->where(function ($q) use ($totatlPhaseCount, $scheduleid) {
                if ($totatlPhaseCount > 1) {
                    $q->whereIn('scheduleid', $this->actualScheduleIds);
                }
            })->whereIn('pd_scheduleid', $pdilist)->sum('est_voters');
            if (($tvoter > 0) && ($telec > 0)) {
                $fper = number_format(($tvoter * 100) / $telec, 2, ".", "");
            } else {
                $fper = 0;
            }

            $oall['voters'] = $tvoter;
            $oall['total'] = $telec;
            $oall['percentage'] = $fper;
            $oall['st_name'] = $st_name;
            $summary['overall'] = $oall;
            $summary['total_pc'] = $tpcnt;
            $summary['total_ac'] = $tacnt;
            $summary['total_phase'] = CommonModel::getTotalPhasesForState($stcode);
            $summary['phase_name'] = $scheduleid;
            $tempdata = DB::table('pd_scheduledetail_publish')->orderBy('updated_at', 'DESC')->first();
            $summary['last_update_time'] = @$tempdata->updated_at;
            return $summary;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }

    public function Dist2AcwisePt(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'electionphase' => 'required',
                'election_id' => 'required',
                'statecode' => 'required',
                'district_no' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }
            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            CommonModel::setDataBase($userInputs['election_id']);
            $stcode = trim($userInputs['statecode']);
            $distno = trim($userInputs['district_no']);
            // Show PC Details in UT's where there is no ac present
            if ($electiontypeid == '1' && in_array(strtoupper($stcode), ['U01', 'U02', 'U06', 'U09'])) {
                $data = $this->DISTwisePt($request);
                $data['data'] = $data['data'];
                $data['data'][0]['acno'] = $data['data'][0]['ac_no'];
                $data['overall']['district_name'] = $data['overall']['st_name'];
                return $data;
            }

            if ($electiontypeid == '1') {
                $this->setActualPhaseForPcGenral($scheduleid);
            } else if ($electiontypeid == '4') {
                $this->setActualPhaseForAcBye($scheduleid);
            } else {
                $this->setActualPhaseForAc($scheduleid, $stcode);
            }
            if ($scheduleid == 0) {
                if ($distno == 0)
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $stcode)->get();
                else
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $stcode)->where('district_no', $distno)->get();
            } else {
                $totatlPhaseCount = $this->getTotalPhasesCount($stcode);
                if ($distno == 0) {
                    if ($electiontypeid == '3' || $electiontypeid == '4') {
                        $pddata = DB::table('pd_schedulemaster')->where('st_code', $stcode)
                            ->where(function ($q) use ($totatlPhaseCount) {
                                if ($totatlPhaseCount > 1) {
                                    $q->whereIn('schedule_id', $this->actualScheduleIds);
                                }
                            })
                            ->get();
                    } else {
                        $pddata = DB::table('pd_schedulemaster')->where('st_code', $stcode)
                            ->where(function ($q) use ($totatlPhaseCount, $scheduleid) {
                                if ($totatlPhaseCount > 1) {
                                    $q->whereIn('schedule_id', $this->actualScheduleIds);
                                }
                            })
                            // ->groupby('pc_no', 'st_code')
                            ->get();
                    }
                } else {
                    if ($electiontypeid == '1' || $electiontypeid == '2') {
                        $pddata = DB::table('pd_schedulemaster')->where('st_code', $stcode)->where('pc_no', $distno)
                            ->where(function ($q) use ($totatlPhaseCount, $stcode) {
                                if ($totatlPhaseCount > 1) {
                                    $q->whereIn('schedule_id', $this->actualScheduleIds);
                                }

                                if (strtoupper($stcode) == 'S14') {
                                    $q->whereNotIn('ac_no', [61, 62]);
                                }
                                if (strtoupper($stcode) == 'U08') {
                                    $q->whereNotIn('ac_no', [91, 92, 93]);
                                }
                            })
                            ->get();
                    } else {

                        $pddata = DB::table('pd_schedulemaster')->where('st_code', $stcode)->where('district_no', $distno)
                            ->where(function ($q) use ($totatlPhaseCount) {
                                if ($totatlPhaseCount > 1) {
                                    $q->whereIn('schedule_id', $this->actualScheduleIds);
                                }
                            })
                            // ->groupby('pc_no', 'st_code')
                            ->get();
                    }
                }
            }


            $pdilist = array();
            if (count($pddata)) {
                foreach ($pddata as $pditem) {
                    $pdilist[] = $pditem->pd_scheduleid;
                }
            }

            $summary = array();
            $eldata = CommonModel::getecctionBYid($electiontypeid);
            $summary['success'] = true;
            $summary['message'] = "Poll TurnOut Home [District 2 ACWise]";
            $summary['phase'] = $scheduleid;

            $stlist = DB::table('pd_scheduledetail_publish')->whereIn('pd_scheduleid', $pdilist)->get();

            $acount = count($stlist);
            $statids = array();
            $district_name = '';
            $state_name = '';
            foreach ($stlist as $sttid) {
                $tempar = array();
                $tempar['acno'] = $sttid->ac_no;
                if ($electiontypeid == '3' || $electiontypeid == '4') {
                    if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                        $tempar['ac_name'] = ucwords(strtolower(CommonModel::getacbyacno($sttid->st_code, $sttid->ac_no)->AC_NAME_HI));
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getdistrictbydistrictno($sttid->st_code, $sttid->dist_no)->DIST_NAME_HI));
                        $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME_HI));
                    } else {
                        $tempar['ac_name'] = ucwords(strtolower(CommonModel::getacbyacno($sttid->st_code, $sttid->ac_no)->AC_NAME));
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getdistrictbydistrictno($sttid->st_code, $sttid->dist_no)->DIST_NAME));
                        $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME));
                    }
                } else {
                    if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                        $tempar['ac_name'] = ucwords(strtolower(CommonModel::getacbyacno($sttid->st_code, $sttid->ac_no)->AC_NAME_HI));
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getpcbypcno($sttid->st_code, $sttid->pc_no)->PC_HNAME));
                        $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME_HI));
                    } else {
                        $tempar['ac_name'] = ucwords(strtolower(CommonModel::getacbyacno($sttid->st_code, $sttid->ac_no)->AC_NAME));
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getpcbypcno($sttid->st_code, $sttid->pc_no)->PC_NAME));
                        $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME));
                    }
                }

                $tempar['dist_no'] = $sttid->dist_no;
                $tempar['st_code'] = $sttid->st_code;

                $tempar['voters'] = 0;
                $tempar['final_total'] = 0;
                $tempar['final_per'] = number_format($sttid->est_turnout_total, 2, ".", "");
                $district_name = $tempar['dist_name'];
                $state_name = $tempar['st_name'];
                $statids[] = $tempar;
            }
            usort($statids, array($this, 'compareByAcName'));
            $summary['data'] = $statids;
            $oall = array();
            if (empty($distno) || $distno == 0 || empty($stcode)) {
                $telec = DB::table('pd_scheduledetail_publish')->where('st_code', $stcode)->sum('electors_total');
                $tvoter = DB::table('pd_scheduledetail_publish')->where('st_code', $stcode)->sum('est_voters');
                if (($tvoter > 0) && ($telec > 0))
                    $fper = number_format(($tvoter * 100) / $telec, 2, ".", "");
                else
                    $fper = 0;
            } else {
                $telec = DB::table('pd_scheduledetail_publish')->where('st_code', $stcode)->where(function ($q) use ($electiontypeid, $distno, $totatlPhaseCount) {
                    if ($electiontypeid == '3' || $electiontypeid == '4') {
                        $q->where('dist_no', $distno);
                    } else {
                        $q->where('pc_no', $distno);
                    }
                    if ($totatlPhaseCount > 1) {
                        $q->whereIn('scheduleid', $this->actualScheduleIds);
                    }
                })->sum('electors_total');
                $tvoter = DB::table('pd_scheduledetail_publish')->where('st_code', $stcode)->where(function ($q) use ($electiontypeid, $distno, $totatlPhaseCount) {
                    if ($electiontypeid == '3' || $electiontypeid == '4') {
                        $q->where('dist_no', $distno);
                    } else {
                        $q->where('pc_no', $distno);
                    }
                    if ($totatlPhaseCount > 1) {
                        $q->whereIn('scheduleid', $this->actualScheduleIds);
                    }
                })->sum('est_voters');
                if (($tvoter > 0) && ($telec > 0))
                    $fper = number_format(($tvoter * 100) / $telec, 2, ".", "");
                else
                    $fper = 0;
            }
            $oall['voters'] = $tvoter;
            $oall['total'] = $telec;
            $oall['percentage'] = $fper;
            $oall['district_name'] = $district_name;
            $oall['state_name'] = $state_name;
            if ($scheduleid != 0) {
                $summary['poll_date'] = DB::table('m_schedule')->select('DATE_POLL')->whereIn('scheduleid', $this->actualScheduleIds)->first()->DATE_POLL;
            }
            $summary['overall'] = $oall;
            $summary['total_ac'] = $acount;
            $summary['total_phase'] = CommonModel::getTotalPhasesForState($stcode);
            $tempdata = DB::table('pd_scheduledetail_publish')->orderBy('updated_at', 'DESC')->first();
            $summary['last_update_time'] = @$tempdata->updated_at;
            $summary['current_time'] = @$tempdata->updated_at;
            $summary['phase_name'] = $scheduleid;
            $summary['actualphase'] = (in_array($stcode, ['S01', 'S18'])) ? "4" : $scheduleid;
            return $summary;
        } catch (Exception $ex) {
            return $ex;
            Log::error($ex);

            throw $ex;
        }
    }


    public function PC2AcwisePt(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'electionphase' => 'required',
                'election_id' => 'required',
                'statecode' => 'required',
                'pcno' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
            }

            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            if (isset($userInputs['statecode'])) {
                $stcode = trim($userInputs['statecode']);
            } else {
                $stcode = "S00";
            }
            if (isset($userInputs['pcno'])) {
                $pcno = trim($userInputs['pcno']);
            } else {
                $pcno = 0;
            }
            if ($stcode != "S00") {
                $pddata = DB::table('pd_schedulemaster')
                    ->where('schedule_id', $scheduleid)
                    ->get();
            } else {
                $pddata = DB::table('pd_schedulemaster')
                    ->where('schedule_id', $scheduleid)
                    ->where('st_code', $stcode)->get();
            }
            $pdilist = array();
            if (count($pddata)) {
                foreach ($pddata as $pditem) {
                    $pdilist[] = $pditem->pd_scheduleid;
                }
            }
            $summary = array();

            $summary['success'] = true;
            $summary['message'] = "Poll TurnOut Home [PC 2 ACWise]";
            $summary['phase'] = $scheduleid;
            $result = array();
            $stlist = $stlist = DB::table('pd_scheduledetail_publish')->where('st_code', $stcode)->get();
            $acount = count($stlist);
            $statids = array();
            $tcnt = count($stlist);
            foreach ($stlist as $sttid) {
                $tempar = array();
                $tempar['acno'] = $sttid->ac_no;
                $tempar['ac_name'] = ucwords(strtolower(CommonModel::getacbyacno($sttid->st_code, $sttid->ac_no)->AC_NAME));
                $tempar['ac_name_hi'] = ucwords(strtolower(CommonModel::getacbyacno($sttid->st_code, $sttid->ac_no)->AC_NAME_HI));
                $tempar['pcno'] = 0; //$sttid->pc_no;
                $tempar['pc_name'] = ''; //CommonModel::getpcbypcno($sttid->st_code,$sttid->pc_no)->PC_NAME;
                $tempar['st_code'] = $sttid->st_code;
                if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME_HI));
                } else {
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME));
                }
                $tempar['voters'] = 0;
                $tempar['final_total'] = 0;
                $tempar['final_per'] = $sttid->est_turnout_total;
                $statids[] = $tempar;
            }

            usort($statids, array($this, 'compareByAcName'));
            $summary['data'] = $statids;
            $oall = array();
            if (empty($pcno) || $pcno == 0 || empty($stcode) || $stcode == "S00") {
                $telec = DB::table('pd_scheduledetail_publish')->sum('electors_total');
                $tvoter = DB::table('pd_scheduledetail_publish')->sum('est_voters');
                if (($tvoter > 0) && ($telec > 0))
                    $fper = number_format(($tvoter * 100) / $telec, 2, ".", "");
                else
                    $fper = 0;
            } else {
                $telec = DB::table('pd_scheduledetail_publish')->where('st_code', $stcode)->sum('electors_total');
                $tvoter = DB::table('pd_scheduledetail_publish')->where('st_code', $stcode)->sum('est_voters');
                if (($tvoter > 0) && ($telec > 0))
                    $fper = number_format(($tvoter * 100) / $telec, 2, ".", "");
                else
                    $fper = 0;
            }
            $oall['voters'] = $tvoter;
            $oall['total'] = $telec;
            $oall['percentage'] = $fper;
            $summary['overall'] = $oall;
            $summary['total_ac'] = $acount;
            $tempdata = DB::table('pd_scheduledetail_publish')->orderBy('updated_at', 'DESC')->first();
            $summary['last_update_time'] = @$tempdata->updated_at;
            return $summary;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }

    public function AcPt(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'electionphase' => 'required',
                'statecode' => 'required',
                'acno' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
            }

            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            $stcode = trim($userInputs['statecode']);
            if (isset($userInputs['pcno']))
                $pcno = trim($userInputs['pcno']);
            else
                $pcno = 0;

            $acno = trim($userInputs['acno']);
            $eldata = CommonModel::getecctionBYid($electiontypeid);

            $pddata = DB::table('pd_schedulemaster')
                //->where('schedule_id',$scheduleid)
                ->get();
            $total_est = 0;
            $pdilist = array();
            if (count($pddata)) {
                foreach ($pddata as $pditem) {
                    $pdilist[] = $pditem->pd_scheduleid;
                }
            }
            $summary = array();

            $summary['success'] = true;
            $summary['message'] = "Poll TurnOut for AC";
            $summary['phase'] = $scheduleid;
            $esubtype = $eldata->election_type;
            $tempar = array();
            if (!empty($acno) && !empty($stcode)) {


                $tempar['acno'] = $acno;
                $acdata = CommonModel::getacbyacno($stcode, $acno);
                $tempar['ac_name'] = ucwords(strtolower($acdata->AC_NAME));
                $tempar['st_code'] = $stcode;
                $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($stcode)->ST_NAME));
                $tempdata = DB::table('pd_scheduledetail_publish')->where('st_code', $stcode)->where('ac_no', $acno)->first();
                if (isset($tempdata)) {
                    $tempar['final_total'] = 0;
                    $tempar['final_per'] = $tempdata->est_turnout_total;
                    $tempar['final_time'] = $tempdata->update_at_final;
                    $tempar['final_device'] = $tempdata->update_device_final;
                    $tempar['timestamp'] = now();
                }
            }
            $summary['acdata'] = $tempar;
            $summary['last_update_time'] = ""; //$tempdata->updated_at;
            return $summary;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }


    public function PhaseWiseState(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required', 'electionphase' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Please Check the Input Details']);
            }

            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            $eldata = CommonModel::getecctionBYid($electiontypeid);
            if ($eldata->election_sort_name == "PC") {
                $pddata = DB::table('pd_schedulemaster')
                    //->where('schedule_id',$scheduleid)
                    ->get();
            } else {
                $pddata = DB::connection('mysql_Old')->table('pd_schedulemaster')
                    //->where('schedule_id',$scheduleid)
                    ->get();
            }
            $total_est = 0;
            $pdilist = array();
            if (count($pddata)) {
                foreach ($pddata as $pditem) {
                    $pdilist[] = $pditem->pd_scheduleid;
                }
            }
            $summary = array();

            $summary['success'] = true;
            $summary['message'] = "Final TurnOut Home [StateWise]";
            $summary['phase'] = $scheduleid;
            $summary['poll_date'] = DB::table('m_schedule')->select('DATE_POLL')
                //->where('scheduleid',$scheduleid)
                ->first()->DATE_POLL;
            $esubtype = $eldata->election_type;
            $result = array();
            if ($eldata->election_sort_name == "PC") {
                $stlist = DB::table('pd_scheduledetail_publish')->select('st_code')
                    //->where('scheduleid',$scheduleid)
                    ->whereIn('pd_scheduleid', $pdilist)->groupby('st_code')->orderby('st_code')->get();
            } else {
                $stlist = DB::connection('mysql_Old')->table('pd_scheduledetail_publish')->select('st_code')
                    //->where('scheduleid',$scheduleid)
                    ->whereIn('pd_scheduleid', $pdilist)->groupby('st_code')->orderby('st_code')->get();
            }
            $statids = array();
            $tcnt = count($stlist);
            $st_aggr = 0;
            $gt_voter = 0;
            $gt_voter_male = 0;
            $gt_voter_female = 0;
            $gt_voter_other = 0;
            $gt_elector = 0;
            foreach ($stlist as $sttid) {
                if ($eldata->election_sort_name == "PC") {
                    $staclist = DB::table('pd_scheduledetail_publish')->select('ac_no')
                        //->where('scheduleid',$scheduleid)
                        ->whereIn('pd_scheduleid', $pdilist)->where('st_code', $sttid->st_code)->get();
                    $tempar = array();
                    $tempar['st_code'] = $sttid->st_code;
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME));
                    $tempar['total_electors'] = DB::table('electors_cdac')->where('st_code', $sttid->st_code)
                        //->where('scheduledid', $scheduleid)
                        ->where('year', 2019)->sum('electors_total');
                    $tempar['turnout_male'] = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_male');
                    $tempar['turnout_female'] = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_female');
                    $tempar['turnout_other'] = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_other');
                    $tempar['turnout_total'] = DB::table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total');
                    if ($tempar['total_electors'] > 0)
                        $tempar['turnout_per'] = number_format(($tempar['turnout_total'] * 100) / $tempar['total_electors'], 2, ".", "");
                    else
                        $tempar['turnout_per'] = 0;
                    $gt_voter += $tempar['turnout_total'];
                    $gt_voter_male += $tempar['turnout_male'];
                    $gt_voter_female += $tempar['turnout_female'];
                    $gt_voter_other += $tempar['turnout_other'];
                    $gt_elector += $tempar['total_electors'];
                    $statids[] = $tempar;
                } else {
                    $staclist = DB::connection('mysql_Old')->table('pd_scheduledetail_publish')->select('ac_no')
                        //->where('scheduleid',$scheduleid)
                        ->whereIn('pd_scheduleid', $pdilist)->where('st_code', $sttid->st_code)->get();
                    $tempar = array();
                    $tempar['st_code'] = $sttid->st_code;
                    $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME));
                    $tempar['total_electors'] = DB::connection('mysql_Old')->table('electors_cdac')->where('st_code', $sttid->st_code)
                        //->where('scheduledid', $scheduleid)
                        ->where('year', 2019)->sum('electors_total');
                    $tempar['turnout_male'] = DB::connection('mysql_Old')->table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_male');
                    $tempar['turnout_female'] = DB::connection('mysql_Old')->table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_female');
                    $tempar['turnout_other'] = DB::connection('mysql_Old')->table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total_other');
                    $tempar['turnout_total'] = DB::connection('mysql_Old')->table('pd_scheduledetail_publish')->where('st_code', $sttid->st_code)->whereIn('pd_scheduleid', $pdilist)->sum('total');
                    if ($tempar['total_electors'] > 0)
                        $tempar['turnout_per'] = number_format(($tempar['turnout_total'] * 100) / $tempar['total_electors'], 2, ".", "");
                    else
                        $tempar['turnout_per'] = 0;
                    $gt_voter += $tempar['turnout_total'];
                    $gt_voter_male += $tempar['turnout_male'];
                    $gt_voter_female += $tempar['turnout_female'];
                    $gt_voter_other += $tempar['turnout_other'];
                    $gt_elector += $tempar['total_electors'];
                    $statids[] = $tempar;
                }
            }
            usort($statids, function ($a, $b) {
                return strcmp($a["st_name"], $b["st_name"]);
            });
            $summary['data'] = $statids;

            $oall = array();
            $oall['total_voters'] = $gt_voter;
            $oall['total_voters_male'] = $gt_voter_male;
            $oall['total_voters_female'] = $gt_voter_female;
            $oall['total_voters_other'] = $gt_voter_other;
            $oall['total_electors'] = $gt_elector;
            if ($gt_elector > 0)
                $oall['percentage'] = number_format(($gt_voter * 100) / $gt_elector, 2, ".", "");
            else
                $oall['percentage'] = 0;
            $summary['overall'] = $oall;
            return $summary;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }

    public function decrypt_data(Request $request)
    {
        $get_all_input = Crypt::decryptString($request->text);
        return response()->json($get_all_input, $this->successStatus);
    }

    public function app_vtr_message(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'election_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            $userInputs = $request->all();
            $election_id = trim($userInputs['election_id']);
            $summary = array();
            $summary['success'] = true;
            $summary['message'] = "Time And Message";
            if (!empty($election_id)) {


                $date = date('Y-m-d');
                //$date ='2021-10-29';

                $schedule = DB::table('m_schedule')->select('SCHEDULENO', 'DATE_POLL')
                    ->where('DATE_POLL', '<=', $date)
                    ->orderBy('DATE_POLL', 'DESC')
                    ->first();

                if (@$schedule->SCHEDULENO) {
                    $scheduleid = $this->getActualPhaseForPcGenral(@$schedule->SCHEDULENO);
                } else {
                    $schedule = DB::table('m_schedule')->select('SCHEDULENO', 'DATE_POLL')
                        ->where('DATE_POLL', '>=', $date)
                        ->orderBy('SCHEDULENO', 'desc')
                        ->first();
                    $scheduleid = $this->getActualPhaseForPcGenral(@$schedule->SCHEDULENO);
                    @Carbon::parse(@$schedule->DATE_POLL)->format('d-m-Y');
                }


                if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                    $result = DB::table('app_vtr_message')->select('start_alt_msg_hi as msg', 'position', 'firebase_key', DB::raw("$scheduleid as phase"))
                        ->where('app_vtr_message.status', '1')->get()->toArray();
                } else {
                    $result = DB::table('app_vtr_message')->select('start_alt_msg_en as msg', 'position', 'firebase_key', DB::raw("$scheduleid as phase"))
                        ->where('app_vtr_message.status', '1')->get()->toArray();
                }
                if (count($result) > 0) {
                    $result[0]->phase_name = $result[0]->phase;
                    $success['firebase_key'] = $result[0]->firebase_key;
                    $success['success'] = true;
                    $success['time'] = time();
                    $success['result'] = $result;
                } else {
                    $success['success'] = false;
                    $success['firebase_key'] = 0;
                    $success['time'] = time();
                    $success['result'] = array();
                    return $success;
                }
                return $success;
            }

            return $summary;
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    } ///EndFunction

    public function ElectionTypeState_Pt(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'electiontype' => 'required',
                'electionphase' => 'required',
                'election_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            $statecode = "";
            $userInputs = $request->all();
            $scheduleid = trim($userInputs['electionphase']);
            $electiontypeid = trim($userInputs['electiontype']);
            $statecode = trim($userInputs['statecode']);
            CommonModel::setDataBase($userInputs['election_id']);
            $date = date('Y-m-d');

            if ($electiontypeid == 2)     /*-----------------for PC-----------------*/ {
                if (!empty($statecode))
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $statecode)->where('schedule_id', $scheduleid)->groupby('pc_no', 'st_code')->get();
                else
                    $pddata = DB::table('pd_schedulemaster')->where('schedule_id', $scheduleid)->groupby('pc_no', 'st_code')->get();

                $pdilist = array();
                if (count($pddata)) {
                    foreach ($pddata as $pditem) {
                        $pdilist[] = $pditem->pd_scheduleid;
                    }
                }
                $schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
                    ->where('DATE_POLL', '<=', $date)
                    ->orderBy('DATE_POLL', 'DESC')
                    ->first();

                if (@$schedule->SCHEDULEID) {
                    $scheduleid = @$schedule->SCHEDULEID;
                } else {
                    $schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
                        ->where('DATE_POLL', '>=', $date)
                        ->first();
                    $scheduleid = @$schedule->SCHEDULEID;
                    @Carbon::parse(@$schedule->DATE_POLL)->format('d-m-Y');
                }

                $summary = array();
                $eldata = CommonModel::getecctionBYid($electiontypeid);
                $summary['success'] = true;
                $summary['message'] = "Poll TurnOut Home [State 2 ACWise]";
                $summary['phase'] = $scheduleid;
                $summary['poll_date'] = @Carbon::parse(@$schedule->DATE_POLL)->format('d-m-Y');

                $stlist = DB::table('pd_scheduledetail_publish as a')->join('m_state as b', 'b.ST_CODE', 'a.st_code')->whereIn('a.pd_scheduleid', $pdilist)->orderBy('b.ST_NAME')->get();
                $acount = count($stlist);

                $statids = array();
                foreach ($stlist as $sttid) {
                    $tempar = array();
                    $tempar['acno'] = $sttid->pc_no;
                    if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                        $tempar['ac_name'] = ucwords(strtolower(CommonModel::getpcbypcno($sttid->st_code, $sttid->pc_no)->PC_NAME_HI));
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getdistrictbydistrictno($sttid->st_code, $sttid->dist_no)->DIST_NAME_HI));
                        $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME_HI));
                    } else {
                        $tempar['ac_name'] = ucwords(strtolower(CommonModel::getpcbypcno($sttid->st_code, $sttid->pc_no)->PC_NAME));
                        $tempar['district_name'] = ucwords(strtolower(CommonModel::getdistrictbydistrictno($sttid->st_code, $sttid->dist_no)->DIST_NAME));
                        $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME));
                    }

                    $tempar['dist_no'] = $sttid->dist_no;
                    $tempar['st_code'] = $sttid->st_code;

                    $tempar['voters'] = 0;

                    $tempar['final_total'] = 0;
                    $telec = DB::table('pd_scheduledetail_publish')->where('scheduleid', $scheduleid)->where('st_code', $sttid->st_code)->where('pc_no', $sttid->pc_no)->sum('electors_total');
                    $tvoter = DB::table('pd_scheduledetail_publish')->where('scheduleid', $scheduleid)->where('st_code', $sttid->st_code)->where('pc_no', $sttid->pc_no)->sum('est_voters');
                    $fper = (($tvoter > 0) && ($telec > 0)) ? number_format(($tvoter * 100) / $telec, 2, ".", "") :  0;

                    $tempar['final_per'] = (float) $fper;
                    $statids[] = $tempar;
                }

                $summary['data'] = $statids;
                $oall = array();
                if (empty($statecode) || $statecode == "S00") {
                    $telec = DB::table('pd_scheduledetail_publish')->sum('electors_total');
                    $tvoter = DB::table('pd_scheduledetail_publish')->sum('est_voters');
                    if (($tvoter > 0) && ($telec > 0))
                        $fper = number_format(($tvoter * 100) / $telec, 2, ".", "");
                    else
                        $fper = 0;
                } else {
                    $telec = DB::table('pd_scheduledetail_publish')->where('st_code', $statecode)->sum('electors_total');
                    $tvoter = DB::table('pd_scheduledetail_publish')->where('st_code', $statecode)->sum('est_voters');
                    if (($tvoter > 0) && ($telec > 0))
                        $fper = number_format(($tvoter * 100) / $telec, 2, ".", "");
                    else
                        $fper = 0;
                }
                $oall['voters'] = $tvoter;
                $oall['total'] = $telec;
                $oall['percentage'] = $fper;
                $summary['overall'] = $oall;
                $summary['total_ac'] = $acount;
                $tempdata = DB::table('pd_scheduledetail_publish')->orderBy('updated_at', 'DESC')->first();
                $summary['last_update_time'] = @$tempdata->updated_at;
            }

            if ($electiontypeid == 4) /*-----------------for AC-----------------*/ {
                $this->setActualPhaseForAcBye($scheduleid);
                if (!empty($statecode)) {
                    $totatlPhaseCount = $this->getTotalPhasesCount($statecode);
                    $pddata = DB::table('pd_schedulemaster')->where('st_code', $statecode)
                        ->where(function ($q) use ($totatlPhaseCount) {
                            if ($totatlPhaseCount > 1) {
                                $q->whereIn('schedule_id', $this->actualScheduleIds);
                            }
                        })
                        ->where('election_type_id', $electiontypeid)->get();

                    $sdse = DB::table('pd_schedule_estimated')->where('st_code', $statecode)->where(function ($q) use ($totatlPhaseCount, $scheduleid) {
                        if ($totatlPhaseCount > 1) {
                            $q->whereIn('schedule_id', $this->actualScheduleIds);
                        }
                    })->first();
                    $schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
                        ->where('SCHEDULEID', $sdse->sechudle_id)
                        ->first();
                    $scheduleid = @$schedule->SCHEDULEID;
                    @Carbon::parse(@$schedule->DATE_POLL)->format('d-m-Y');
                } else {
                    $pddata = DB::table('pd_schedulemaster')->where('election_type_id', $electiontypeid)->whereIn('schedule_id', $this->actualScheduleIds)->get();
                    $schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
                        ->where('DATE_POLL', '<=', $date)
                        ->orderBy('DATE_POLL', 'DESC')
                        ->first();
                    // dd($pddata);
                    if (@$schedule->SCHEDULEID) {
                        $scheduleid = @$schedule->SCHEDULEID;
                    } else {
                        $schedule = DB::table('m_schedule')->select('SCHEDULEID', 'DATE_POLL')
                            ->where('DATE_POLL', '>=', $date)
                            ->first();
                        $scheduleid = @$schedule->SCHEDULEID;
                        @Carbon::parse(@$schedule->DATE_POLL)->format('d-m-Y');
                    }
                }


                $pdilist = array();
                if (count($pddata)) {
                    foreach ($pddata as $pditem) {
                        $pdilist[] = $pditem->pd_scheduleid;
                    }
                }

                $summary = array();
                $eldata = CommonModel::getecctionBYid($electiontypeid);
                $summary['success'] = true;
                $summary['message'] = "Poll TurnOut Home [State 2 ACWise]";
                $summary['phase'] = $scheduleid;
                $summary['poll_date'] = @Carbon::parse(@$schedule->DATE_POLL)->format('d-m-Y');
                $stlist = DB::table('pd_scheduledetail_publish as a')->join('m_state as b', 'b.ST_CODE', 'a.st_code')->whereIn('a.pd_scheduleid', $pdilist)->orderBy('b.ST_NAME')->get();
                $acount = count($stlist);

                $statids = array();
                $tcnt = count($stlist);
                foreach ($stlist as $sttid) {
                    $tempar = array();
                    $tempar['acno'] = $sttid->ac_no;

                    if (isset($userInputs['language']) && ($userInputs['language'] == 'hi')) {
                        $tempar['ac_name'] = ucwords(strtolower(CommonModel::getacbyacno($sttid->st_code, $sttid->ac_no)->AC_NAME_HI));
                        $tempar['dist_name'] = ucwords(strtolower(CommonModel::getdistrictbydistrictno($sttid->st_code, $sttid->dist_no)->DIST_NAME_HI));
                        $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME_HI));
                    } else {
                        $tempar['ac_name'] = ucwords(strtolower(CommonModel::getacbyacno($sttid->st_code, $sttid->ac_no)->AC_NAME));
                        $tempar['district_name'] = ucwords(strtolower(CommonModel::getdistrictbydistrictno($sttid->st_code, $sttid->dist_no)->DIST_NAME));
                        $tempar['st_name'] = ucwords(strtolower(CommonModel::getstatebystatecode($sttid->st_code)->ST_NAME));
                    }

                    $tempar['dist_no'] = $sttid->dist_no;
                    $tempar['st_code'] = $sttid->st_code;

                    $tempar['voters'] = 0;

                    $tempar['final_total'] = 0;
                    $tempar['final_per'] = number_format($sttid->est_turnout_total, 2, ".", "");
                    $statids[] = $tempar;
                }

                $summary['data'] = $statids;
                $oall = array();
                if (empty($distno) || $distno == 0 || empty($statecode) || $statecode == "S00") {
                    $telec = DB::table('pd_scheduledetail_publish')->where('ELECTION_TYPEID', $electiontypeid)->sum('electors_total');
                    $tvoter = DB::table('pd_scheduledetail_publish')->where('ELECTION_TYPEID', $electiontypeid)->sum('est_voters');
                    if (($tvoter > 0) && ($telec > 0))
                        $fper = number_format(($tvoter * 100) / $telec, 2, ".", "");
                    else
                        $fper = 0;
                } else {
                    $telec = DB::table('pd_scheduledetail_publish')->where('ELECTION_TYPEID', $electiontypeid)->where('st_code', $statecode)->sum('electors_total');
                    $tvoter = DB::table('pd_scheduledetail_publish')->where('ELECTION_TYPEID', $electiontypeid)->where('st_code', $statecode)->sum('est_voters');
                    if (($tvoter > 0) && ($telec > 0))
                        $fper = number_format(($tvoter * 100) / $telec, 2, ".", "");
                    else
                        $fper = 0;
                }
                $oall['voters'] = $tvoter;
                $oall['total'] = $telec;
                $oall['percentage'] = $fper;
                //print_r("\n\n2. \n");
                //print_r($stsum);die;

                $summary['overall'] = $oall;
                $summary['total_ac'] = $acount;
                $tempdata = DB::table('pd_scheduledetail_publish')->orderBy('updated_at', 'DESC')->first();
                $summary['last_update_time'] = @$tempdata->updated_at;
            }

            return $summary;
        } catch (Exception $ex) {
            Log::error($ex);
            throw $ex;
        }
    }

    public function updateFireBaseKey(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firebase' => 'required',
                'token' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }
            if ($request->token == 'ff1d2811a32f1e3222cd4727e9c15f7fee4306f0') {
                $result = DB::table('app_vtr_message')->update(['firebase_key' => $request->firebase]);
                return response()->json(['success' => true, 'msg' => 'Firebase Key is Updated'], $this->successStatus);
            } else {
                throw new Exception('Invalid Token');
            }
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }

    public function updateMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'token' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), $this->notfoundStatus);
            }

            if ($request->token == '17aa71ef6a2c7200cb141e4d9aa47d143281ca42') {
                $result = DB::table('app_vtr_message')->update(['status' => 0]);
                $result = DB::table('app_vtr_message')->where('id', $request->id)->update(['status' => 1]);
                return response()->json(['success' => true, 'msg' => 'Message is active'], $this->successStatus);
            } else {
                throw new Exception('Invalid Token');
            }
        } catch (Exception $ex) {
            Log::error($ex);

            throw $ex;
        }
    }

    public function getTotalPhasesCount($st_code)
    {
        return DB::table('pd_schedule_estimated')->where('st_code', $st_code)->select('sechudle_id')->distinct()->count();
    }
}
