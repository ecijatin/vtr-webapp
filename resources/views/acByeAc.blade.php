@extends('theme')
@section('content')
<div class="card custom-card water-mark">
    <div class="card-header">
        <div class="row">
            <div class="col-md-12 col-12">Poll Date - {{$self->formatDate($results['poll_date'])}}</div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>Assembly Constituency</th>
                        <th class="text-end">Approximate Voter Turnout Trends (in %)</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($results['data'] as $key => $ac)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$ac['ac_name']}}</td>
                        <td class="text-end">{{$ac['final_per']}}</td>
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