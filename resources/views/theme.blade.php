<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Bootstrap CSS -->
    <link href="{{asset('css/bootstrap.min.css?v='.rand(1,999))}}" rel="stylesheet">
    <link href="{{asset('css/custom.css?v='.rand(1,999))}}" rel="stylesheet">
    <link href="{{asset('css/responsive.css?v='.rand(1,999))}}" rel="stylesheet">
    <link href="{{asset('css/font.css?v='.rand(1,999))}}" rel="stylesheet">
    <link rel='shortcut icon' href="{{asset('img/icons/favicon.ico')}}" type="image/x-icon">
    <title>Voter Turnout | Election Commission of India</title>
    <style>
        .water-mark {
            background: url('<?= asset("/img/approximate-trend.png?v=" . rand(1, 999)) ?>') repeat;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo-search">
            <div class="logo"><img src="{{asset('img/eci-logo.png')}}" alt=""></div>
            <div class="txt-logo">Voter Turnout</div>
            <div class="vtn-logo"><img src="{{asset('img/voter-turnout-logo.png')}}" alt=""></div>
        </div>
        <div class="dis-info">
            <p class="mb-0 text-center"> <strong>Disclaimer:</strong> This is approximate trend, as data from some Polling Stations(PS) takes time and this trend does
                not include data of postal ballot voting. Final data for each PS is shared in Form 17C with all Polling Agents.
            </p>
        </div>
    </header>


    <main class="inner-content">
        <div class="container-md container-fluid">

            @include('filter')
            @yield('content')

        </div><!-- End of container-fluid Div -->

    </main>
    <footer class="footer">
        <div class="foot-info">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <h6 class="mb-1">Download Voter helpline app to see results on mobile</h6>
                    </div>
                    <div class="col-md-6 col-12">
                        <ul class="d-flex align-items-center items-end">
                            <li><a href="https://play.google.com/store/apps/details?id=in.gov.eci.pollturnout&pcampaignid=web_share"><img src="{{asset('img/icons/android.png')}}" alt=""></a></li>
                            <li><a href="https://apps.apple.com/in/app/voter-turnout-app/id1536366882"><img src="{{asset('img/icons/apple.png')}}" alt=""></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Optional JavaScript; choose one of the two! -->
    <script src="{{asset('js/jquery.min.js')}}" type="text/javascript"></script>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="{{asset('js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <!-- Option 3: Custom jQuery Lab -->
    <script src="{{asset('js/custom-jQuery.js')}}" type="text/javascript"></script>
    @yield('script')
</body>

</html>