<div class="filter-wrap">
    <button class="btn btn-success float-btn" onclick="window.location.reload();"><svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 489.698 489.698" xml:space="preserve" stroke="#ffffff">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
                <g>
                    <g>
                        <path d="M468.999,227.774c-11.4,0-20.8,8.3-20.8,19.8c-1,74.9-44.2,142.6-110.3,178.9c-99.6,54.7-216,5.6-260.6-61l62.9,13.1 c10.4,2.1,21.8-4.2,23.9-15.6c2.1-10.4-4.2-21.8-15.6-23.9l-123.7-26c-7.2-1.7-26.1,3.5-23.9,22.9l15.6,124.8 c1,10.4,9.4,17.7,19.8,17.7c15.5,0,21.8-11.4,20.8-22.9l-7.3-60.9c101.1,121.3,229.4,104.4,306.8,69.3 c80.1-42.7,131.1-124.8,132.1-215.4C488.799,237.174,480.399,227.774,468.999,227.774z"></path>
                        <path d="M20.599,261.874c11.4,0,20.8-8.3,20.8-19.8c1-74.9,44.2-142.6,110.3-178.9c99.6-54.7,216-5.6,260.6,61l-62.9-13.1 c-10.4-2.1-21.8,4.2-23.9,15.6c-2.1,10.4,4.2,21.8,15.6,23.9l123.8,26c7.2,1.7,26.1-3.5,23.9-22.9l-15.6-124.8 c-1-10.4-9.4-17.7-19.8-17.7c-15.5,0-21.8,11.4-20.8,22.9l7.2,60.9c-101.1-121.2-229.4-104.4-306.8-69.2 c-80.1,42.6-131.1,124.8-132.2,215.3C0.799,252.574,9.199,261.874,20.599,261.874z"></path>
                    </g>
                </g>
            </g>
        </svg></button>
    <div class="row align-items-center white-bg">
        <div class="col-md-12 btn-group parent-btn">
            @foreach($elections as $id => $type)
            @if($id == 4)
            <a href="{{route('acByeState',[Crypt::encryptString($id)])}}" class="btn btn-outline-danger {{($id == $selected_election_type) ? 'active' : ''}}">{{$type}}</a>
            @elseif($id == 3)
            <a href="{{route('acElection')}}" class="btn btn-outline-danger {{($id == $selected_election_type) ? 'active' : ''}}">{{$type}}</a>
            @else
            <a href="{{route('pcElelction',[Crypt::encryptString($id), ($selected_election_type == $id) ? Crypt::encryptString($phase) : Crypt::encryptString(1)])}}" class="btn btn-outline-danger {{($id == $selected_election_type) ? 'active' : ''}}">{{$type}}</a>
            @endif
            @endforeach
        </div>
    </div>
    @if($selected_election_type == 1)
    <div class="row align-items-center mt-2 white-bg">
        <div class="col-md-12 btn-group">
            @foreach($phases as $key => $result)
            <a href="{{route('pcElelction',[Crypt::encryptString($selected_election_type), Crypt::encryptString($result['schedule_id'])])}}" class="btn btn-outline-primary {{($result['schedule_id'] == $phase) ? 'active' : ''}}">Phase {{$result['schedule_id']}}</a>
            @endforeach
        </div>
    </div>
    @endif
    @if($selected_election_type == 3 && isset($results['total_phase']))
    @if($results['total_phase'] > 1)
    <div class="row align-items-center mt-2 white-bg">
        <div class="col-md-12 btn-group">
            @foreach($phases as $key => $result)
            <a href="{{route('acState',[
                                Crypt::encryptString($filters['statecode']),
                                Crypt::encryptString($result['schedule_id']),
                                Crypt::encryptString($filters['election_id']),
                                Crypt::encryptString($filters['electiontype'])
                                ])}}" class="btn btn-outline-primary {{($result['schedule_id'] == $phase) ? 'active' : ''}}">Phase {{$result['name']}}</a>
            @endforeach
        </div>
    </div>
    @endif
    @endif
</div>

@section('script')
<script type=" text/javascript">

</script>
@endsection