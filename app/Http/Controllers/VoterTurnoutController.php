<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\CommonModel;
use App\Models\VtMessage;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class VoterTurnoutController extends Controller
{
    public $phase = 4;
    public $currentPhase = 4;
    public $selected_election_type = null;

    public function index(Request $request)
    {
        $data = [];
        $api = new ApiController();
        // $this->phase = 4;
        $data['selected_election_type'] = (!$request->has('election_type')) ? Config::get('constant.elections_type.PC') :  Crypt::decryptString($request->input('election_type'));
        $this->phase = (!$request->has('phase')) ? $this->phase : Crypt::decryptString($request->input('phase'));
        $data['phase'] = $this->phase;
        $data['elections'] = Config::get('constant.elections');
        $req = new Request();
        $data['filters'] = [
            'electiontype' => $data['selected_election_type'], 'electionphase' => $this->phase, 'election_id' => Config::get('constant.elections_ids')[$data['selected_election_type']]
        ];
        $req->merge($data['filters']);
        $data['results'] = $api->HomePt($req);
        $filters = ['electiontype' => $data['selected_election_type'], 'election_id' => Config::get('constant.elections_ids')[$data['selected_election_type']]];
        $req1 = new Request();
        $req1->merge($filters);
        $data['phases'] = $api->PhaseListPt($req1);
        $data['time'] = $this->getTimeSlot();
        $data['currentPhase'] = $this->currentPhase;
        $data['self'] = $this;
        return view('home', $data);
    }

    public function pcElelction(Request $request, $election_type, $phase)
    {
        $request->merge(['election_type' => $election_type, 'phase' => $phase]);
        return $this->index($request);
    }


    public function pcState(Request $request)
    {
        try {

            $api = new ApiController();
            $data = [];
            $data['elections'] = Config::get('constant.elections');
            $req = new Request();
            $data['filters'] = [
                'electiontype' => Crypt::decryptString($request->electiontype),
                'electionphase' => Crypt::decryptString($request->electionphase),
                'election_id' => Crypt::decryptString($request->electionid),
                'statecode' => Crypt::decryptString($request->statecode),
                'district_no' => 0
            ];
            $req->merge($data['filters']);
            $data['results'] = $api->DISTwisePt($req);
            $data['selected_election_type'] = $data['filters']['electiontype'];
            $this->phase = $data['filters']['electionphase'];
            $data['phase'] = $this->phase;
            $filters = ['electiontype' => $data['selected_election_type'], 'election_id' => $data['filters']['election_id']];
            $req1 = new Request();
            $req1->merge($filters);
            $data['phases'] = $api->PhaseListPt($req1);
            $data['time'] = $this->getTimeSlot();
            $data['currentPhase'] = $this->currentPhase;
            $data['self'] = $this;
            return view('state', $data);
        } catch (DecryptException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function pcAc(Request $request)
    {
        try {
            $data = [];
            $api = new ApiController();
            $data['elections'] = Config::get('constant.elections');
            $req = new Request();
            $data['filters'] = [
                'electiontype' => Crypt::decryptString($request->electiontype),
                'electionphase' => Crypt::decryptString($request->electionphase),
                'election_id' => Crypt::decryptString($request->electionid),
                'statecode' => Crypt::decryptString($request->statecode),
                'district_no' => Crypt::decryptString($request->distict)
            ];
            $req->merge($data['filters']);
            $data['results'] = $api->Dist2AcwisePt($req);
            $data['selected_election_type'] = $data['filters']['electiontype'];
            $this->phase = $data['filters']['electionphase'];
            $data['phase'] = $this->phase;
            $filters = ['electiontype' => $data['selected_election_type'], 'election_id' => $data['filters']['election_id']];
            $req1 = new Request();
            $req1->merge($filters);
            $data['phases'] = $api->PhaseListPt($req1);
            $data['time'] = $this->getTimeSlot();
            $data['currentPhase'] = $this->currentPhase;
            $data['self'] = $this;
            return view('ac', $data);
        } catch (DecryptException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function acElection(Request $request)
    {
        try {
            $data = [];
            $api = new ApiController();
            $data['selected_election_type'] = Config::get('constant.elections_type.AC');
            $data['phase'] = $this->phase;
            $data['elections'] = Config::get('constant.elections');
            $req = new Request();
            $data['filters'] = [
                'electiontype' => $data['selected_election_type'],
                'electionphase' => 0,
                'election_id' => Config::get('constant.elections_ids')[Config::get('constant.elections_type.AC')],
            ];
            $req->merge($data['filters']);
            $data['results'] = $api->StateListingPT($req);
            $data['time'] = $this->getTimeSlot();
            $data['currentPhase'] = $this->currentPhase;
            $data['self'] = $this;
            return view('acState', $data);
        } catch (DecryptException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function acState(Request $request)
    {
        try {
            $data = [];
            $api = new ApiController();
            $data['elections'] = Config::get('constant.elections');
            $req = new Request();

            $data['filters'] = [
                'electiontype' => Crypt::decryptString($request->electiontype),
                'electionphase' => Crypt::decryptString($request->electionphase),
                'election_id' => Crypt::decryptString($request->electionid),
                'statecode' => Crypt::decryptString($request->statecode),
                'district_no' => 0
            ];
            $req->merge($data['filters']);
            $data['results'] = $api->Dist2AcwisePt($req);
            $data['selected_election_type'] = $data['filters']['electiontype'];
            $this->phase = $data['filters']['electionphase'];
            $data['phase'] = $this->phase;
            $filters = ['electiontype' => Crypt::decryptString($request->electiontype), 'election_id' => Crypt::decryptString($request->electionid), 'statecode' => Crypt::decryptString($request->statecode)];
            $req1 = new Request();
            $req1->merge($filters);
            $data['phases'] = $api->PhaseListPt($req1);
            $data['time'] = $this->getTimeSlot();
            $data['currentPhase'] = $this->currentPhase;
            $data['self'] = $this;
            // dd($data);
            return view('acAc', $data);
        } catch (DecryptException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function acByeState(Request $request)
    {
        try {
            $data = [];
            $api = new ApiController();
            $data['selected_election_type'] = Config::get('constant.elections_type.ACBYE');
            $data['phase'] = $this->phase;
            $data['elections'] = Config::get('constant.elections');
            $req = new Request();
            $data['filters'] = [
                'electiontype' => $data['selected_election_type'],
                'electionphase' => 0,
                'election_id' => Config::get('constant.elections_ids')[Crypt::decryptString($request->electionid)],
            ];
            $req->merge($data['filters']);
            $data['results'] = $api->StateListingPT($req);
            $data['time'] = $this->getTimeSlot();
            $data['currentPhase'] = $this->currentPhase;
            $data['self'] = $this;
            return view('acByeState', $data);
        } catch (DecryptException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function acByeAc(Request $request)
    {
        try {
            $data = [];
            $api = new ApiController();
            $data['elections'] = Config::get('constant.elections');
            $req = new Request();
            $data['filters'] = [
                'electiontype' => Crypt::decryptString($request->electiontype),
                'electionphase' => Crypt::decryptString($request->electionphase),
                'election_id' => Crypt::decryptString($request->electionid),
                'statecode' => Crypt::decryptString($request->statecode),
                'district_no' => 0
            ];
            $req->merge($data['filters']);
            $data['results'] = $api->Dist2AcwisePt($req);
            $data['selected_election_type'] = $data['filters']['electiontype'];
            $this->phase = $data['filters']['electionphase'];
            $data['phase'] = $this->phase;
            $data['time'] = $this->getTimeSlot();
            $data['currentPhase'] = $this->currentPhase;
            $data['self'] = $this;
            return view('acByeAc', $data);
        } catch (DecryptException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    function getTimeSlot()
    {
        CommonModel::setDataBase(47);
        $message = VtMessage::where('status', 1)->first();
        if ($message) {
            return $message->start_alt_msg_en;
        }
        return '';
    }

    function formatDate($date)
    {
        return Carbon::parse($date)->format('d-m-Y');
    }
}
