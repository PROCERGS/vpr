<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Acompanhamento VPR</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/jquery.countdown.css">
        <style>
            .fixed {
                z-index: 1000;
            }
            #chart_div, #chart_div {
                z-index: 500;
            }

            .countdown {
                width: 550px;
                font-size: 3em;
                height: 140px;
                position: absolute;
                right: 10px;
                top: 10px;
            }

            .current, .votesPerMinute {
                font-weight: bold;
            }
        </style>

        <!--[if lt IE 9]>
                <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
                <script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
        <![endif]-->
    </head>
    <body>
        <div data-spyx="affix" data-offset-top="10" class="fixed">
            <h1>Votação de Prioridades - Orçamento 2014</h1>
            <h2>Último Voto: <span class="lastUpdate">não disponível</span> - Votos/minuto: <span class="votesPerMinute">0</span></h2>
            <h2>Total de Eleitores Votantes: <span class="current">0</span></h2>

            <div class="countdown"></div>
        </div>
        <div id="chart_div" style="width: 100%; height: 700px;"></div>

        <script src="<?= $dataUrl ?>"></script>
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script src="/js/jquery.countdown.js"></script>
        <script src="/js/jquery.countdown-pt-BR.js"></script>
        <script src="https://www.google.com/jsapi"></script>
        <script>
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));

                var options = {
                    title: 'Evolução da Votação de Prioridades do RS',
                    vAxis: {title: "Votos/minuto"},
                    hAxis: {
                        title: "Tempo",
                        format: "dd/MM HH:mm",
                        slantedText: true,
                        minTextSpacing: 1,
                        viewWindowMode: 'maximized',
                        gridlines: {
                            count: 10
                        }
                    },
                    seriesType: "bars",
                    animation: { duration: 1500, easing: 'in' },
                };

                var data = new google.visualization.DataTable();
                data.addColumn('datetime', 'Data');
                data.addColumn('number', 'Delta');
                data.addRows(points);

                var formatter = new google.visualization.DateFormat({pattern: 'dd/MM/yyyy HH:mm'});
                formatter.format(data, 1);

                chart.draw(data, options);

                $(".current").html(current);
                $(".lastUpdate").html(lastUpdate);
                $(".votesPerMinute").html(lastDelta);
            }
            $(document).ready(function() {
                setInterval(function() {
                    $.getScript('chart_data.php', function() {
                        drawChart();
                    });
                }, 30000);

                $('.countdown').countdown({until: new Date(2013, 07, 07, 23,59,59), format: 'HMS'});
            });
        </script>
        <script src="/js/bootstrap.min.js"></script>
    </body>
</html>