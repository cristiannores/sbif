<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <script
            src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
        <!-- Compiled and minified JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>



    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
            <div class="top-right links">
                @auth
                <a href="{{ url('/home') }}">Home</a>
                @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                @endauth
            </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    <div class="section">
                        <div class="row">
                            <h1>Consultar valores UF </h1>
                        </div>
                        <div class="row">

                            <div class="input-field col s12">

                                <label for="birthdate" class="">Seleccione fecha</label>
                                <input type="text" class="datepicker" id="datepicker">
                            </div>

                            <!-- Switch -->
                            <div class="switch col s12">
                                <label>
                                    Obtener mes
                                    <input type="checkbox" id="selector">
                                    <span class="lever"></span>
                                    Obtener día 
                                </label>
                            </div>



                        </div>
                        <div class="row">
                            <a class="waves-effect waves-light  btn" id="btn-send"><i class="material-icons left">send</i> Generar resultados</a>
                            <a class="waves-effect waves-light  btn" id="btn-download"><i class="material-icons left">send</i> Descargar resultados</a>
                        </div>
                        <div class="row">
                            <div class="col s12" id="tabla">

                            </div>

                        </div>
                    </div>

                </div>


            </div>
        </div>

        <script>
            // A $( document ).ready() block.
            $(document).ready(function () {
                $('.datepicker').pickadate({
                    selectMonths: true, // Creates a dropdown to control month
                    selectYears: 15, // Creates a dropdown of 15 years to control year,
                    today: 'Today',
                    clear: 'Clear',
                    close: 'Ok',
                    closeOnSelect: false // Close upon selecting a date,
                });
                $("#btn-send").on("click", () => {


                    let sel = $("#selector").prop('checked');
                    let year = $('.datepicker').pickadate('picker').get('highlight', 'yyyy');
                    let day = $('.datepicker').pickadate('picker').get('highlight', 'dd');
                    let month = $('.datepicker').pickadate('picker').get('highlight', 'mm');
                    if (year === '' || day === '' || month === '') {
                        return;
                    }
                    console.log( sel , year, day , month);
                    sendRequest(year, month, day, sel);
                });
                $("#btn-download").on("click", () => {
                    let sel = $("#selector").prop('checked');
                    let year = $('.datepicker').pickadate('picker').get('highlight', 'yyyy');
                    let day = $('.datepicker').pickadate('picker').get('highlight', 'dd');
                    let month = $('.datepicker').pickadate('picker').get('highlight', 'mm');
                    if (year === '' || day === '' || month === '') {
                        return;
                    }
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var selector = (sel) ? "si" : "no";

                    document.location = "/download?sel=" + sel + "&year=" + year + "&day=" + day + "&selector=" + selector;


                });
            })

            function sendRequest(year, month, day, sel) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var selector = (sel) ? "si" : "no";
                $.ajax({
                    url: "/sbif",
                    type: 'POST',
                    data: {year, month, day, selector},
                    dataType: "json",
                }).done(function (e) {

                    $("#tabla").html("");
                    var table = document.createElement('table');
                    table.className = "";
                    var thead = document.createElement('thead');
                    var tr = document.createElement('tr');

                    var td1 = document.createElement('td');
                    var td2 = document.createElement('td');

                    var text1 = document.createTextNode('Valor');
                    var text2 = document.createTextNode('Fecha');

                    td1.appendChild(text1);
                    td2.appendChild(text2);
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    thead.appendChild(tr);
                    table.appendChild(thead);


                    if (typeof e.UFs !== 'undefined') {
                        var tbody = document.createElement('tbody');

                        for (var i in e.UFs) {
                            const uf = e.UFs[i];


                            var tr = document.createElement('tr');

                            var td1 = document.createElement('td');
                            var td2 = document.createElement('td');

                            var text1 = document.createTextNode(uf.Valor);
                            var text2 = document.createTextNode(uf.Fecha);
                            td1.appendChild(text1);
                            td2.appendChild(text2);
                            tr.appendChild(td1);
                            tr.appendChild(td2);
                            tbody.appendChild(tr);

                            document.getElementById("tabla").appendChild(table);

                        }
                        table.appendChild(tbody);
                    }
                });
            }

        </script>

    </body>
</html>
