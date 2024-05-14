@extends('theme')
@section('content')
<div class="card custom-card water-mark">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6 col-12 text-start">Phase - {{$results['phase']}}</div>
            <div class="col-md-6 col-12 text-end">Approx. Voter Turnout <strong><i>{{$results['overall']['st_name']}}</i></strong> - {{$results['overall']['percentage']}} %</div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12 text-start">{{($currentPhase == $phase && $time) ? 'Time -'.$time : ''}}</div>
            <div class="col-md-6 col-12 text-end">Poll Date - {{$self->formatDate($results['poll_date'])}}</div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>{{($filters['electiontype'] == '1' || $filters['electiontype'] == '2') ? 'Parliamentary Constituency' : 'Districts'}}</th>
                        <th class="text-end">Approximate Voter Turnout Trends (in %)</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($results['data'] as $key => $pc)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td><a href="{{route('pcAc',[
                                Crypt::encryptString($pc['dist_no']),
                                Crypt::encryptString($pc['st_code']),
                                Crypt::encryptString($filters['electionphase']),
                                Crypt::encryptString($filters['election_id']),
                                Crypt::encryptString($filters['electiontype'])
                                ])}}">{{$pc['dist_name']}}</a></td>
                        <td class="text-end">{{$pc['final_per']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-header">
        <div class="row">
            <div class="col-md-6 col-12 text-start">Phase - {{$results['phase']}}</div>
            <div class="col-md-6 col-12 text-end">Approx. Voter Turnout <strong><i>{{$results['overall']['st_name']}}</i></strong> - {{$results['overall']['percentage']}} %</div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12 text-start">{{($currentPhase == $phase && $time) ? 'Time -'.$time : ''}}</div>
            <div class="col-md-6 col-12 text-end">Poll Date - {{$self->formatDate($results['poll_date'])}}</div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('.statelist-row').on('click', function() {

        })
    })
</script>
@endsection