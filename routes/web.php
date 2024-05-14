<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\VoterTurnoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [VoterTurnoutController::class, 'index'])->name('home');
Route::get('/election/pc/{election_type}/{phase?}', [VoterTurnoutController::class, 'pcElelction'])->name('pcElelction');
Route::get('/election/pc/state/{statecode}/{electionphase}/{electionid}/{electiontype}', [VoterTurnoutController::class, 'pcState'])->name('pcState');
Route::get('/election/pc/ac/{distict}/{statecode}/{electionphase}/{electionid}/{electiontype}', [VoterTurnoutController::class, 'pcAc'])->name('pcAc');
Route::get('/election/ac', [VoterTurnoutController::class, 'acElection'])->name('acElection');
Route::get('/election/ac/{statecode}/{electionphase}/{electionid}/{electiontype}', [VoterTurnoutController::class, 'acState'])->name('acState');
Route::get('/ac/bye/state/{electionid}', [VoterTurnoutController::class, 'acByeState'])->name('acByeState');
Route::get('/ac/bye/{statecode}/{electionphase}/{electionid}/{electiontype}', [VoterTurnoutController::class, 'acByeAc'])->name('acByeAc');


Route::prefix('v1')->group(function () {
    Route::post('Home_PT', 'API\VtOneController@HomePt'); /// For Home Page
    Route::post('Home_Dashboard', 'API\VtOneController@HomeDashboard'); /// For Home Dashboard Page
    Route::post('DistrictWise_PT', 'API\VtOneController@DistwisePt'); /// For Summary Report of All PC or PC of selected State
    Route::post('PC2ACwise_PT', 'API\VtOneController@PC2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
    Route::post('DIST2ACwise_PT', 'API\VtOneController@Dist2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
    Route::post('AC_PT', 'API\VtOneController@AcPt'); /// For Current Poll turnout status of selected AC
    Route::post('State_PhaseWise', 'API\VtOneController@PhaseWiseState'); /// For Poll turnout status of all States
    Route::post('ElectionTypeState_Pt', 'API\VtOneController@ElectionTypeState_Pt');
    Route::post('app_vtr_message', 'API\VtOneController@app_vtr_message'); /// For Msg All Page
    Route::post('update-firebase-key', 'API\VtOneController@updateFireBaseKey'); /// For Home Page
    Route::post('update-msg', 'API\VtOneController@updateMessage'); /// For Home Page

    ###### FILTERS CALLS
    Route::post('ElectionType_PT', 'API\VtOneController@ElectionTypePt'); /// For List of available election types
    Route::post('PhaseList_PT', 'API\VtOneController@PhaseListPt'); /// For List of phases in selected election type
    Route::post('PhaseList_PTNew', 'API\VtOneController@PhaseListPtNew'); /// For List of phases in selected election type
    Route::post('StateList_PT', 'API\VtOneController@StateListingPT'); /// For List of All States in selected Phase and Election
    Route::post('PcList_PT', 'API\VtOneController@PcListingPT'); /// For List of All PC in selected State
    Route::post('PCWiseAcList_PT', 'API\VtOneController@PC2AcListingPT'); /// For List of All Polling AC in selected PC
    Route::post('DistWiseAcList_PT', 'API\VtOneController@Dist2AcListingPT'); /// For List of All Polling AC in selected District
    Route::post('DistrictList_PT', 'API\VtOneController@DistListingPT'); /// For List of All Polling District in selected State
    Route::post('GetPollDate', 'API\VtOneController@PollDate'); /// For List of All Polling District in selected State
});
