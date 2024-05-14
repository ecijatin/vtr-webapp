@extends('theme')
@section('content')
<div class="card custom-card water-mark">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>State/UT</th>
                        <th>Poll Date</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $key => $result)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td><a href="{{route('acState',[
                                Crypt::encryptString($result['statecode']),
                                Crypt::encryptString($result['schedule_id']),
                                Crypt::encryptString($filters['election_id']),
                                Crypt::encryptString($filters['electiontype'])
                                ])}}">{{$result['statename']}}</a></td>
                        <td>{{$self->formatDate($result['poll_date'])}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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