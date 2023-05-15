@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #main_table {
            color: black;
            border: 1px solid black;
        }

        #main_table>thead>tr>th {
            color: white;
            text-align: center;
            padding: 2px;
            border: #000 1px solid;
        }

        #main_table>tbody>tr>td {
            padding: 2px;
            height: 40px !important;
            border: #000 1px solid;
        }

        #main_table>tfoot>tr>th {
            padding: 2px;
            border: #000 1px solid;
        }

        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            text-align: center;
        }

        tfoot>tr>th {
            text-align: center;
        }

        td:hover {
            overflow: visible;
        }

        .table-hover>tbody>tr:hover>td,
        .table-hover>tbody>tr:hover>th {
            background-color: #7dfa8c;
        }

        table>thead>tr>th {
            border: 2px solid black;
            color: black;
        }

        #loading,
        #error {
            display: none;
        }

        .top-chart {
            margin: 2% 0%;
            padding: 1% 1%;            
        }

        table {
            color: #000;
        }

        .table-black td {
            border: 1px solid black;
            color: black;
        }        
    </style>
@endsection

@section('content')
    <div id="loading"
        style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
        <p style="position: absolute; color: White; top: 45%; left: 35%;">
            <span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
        </p>
    </div>
    <div class="row" style="padding: 0 1%;">
        <div class="col-xs-2">
            <div class="input-group select_location_container">
                <div class="input-group-addon bg-green">
                    <i class="fa fa-industry"></i>
                </div>
                <select class="form-control select2" id="select_location" data-placeholder="Select Location" style="width: 100%;">
                    <option value="all" selected> All Location</option>
                    @foreach ($storage_location as $sloc)
                        <option value="{{ $sloc->storage_location }}">{{ $sloc->storage_location }} - {{ $sloc->location }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-xs-2 pull_right" style="margin-right: 2%">
            <div class="input-group">
                <div class="input-group-addon bg-green">
                    <i class="fa fa-calendar"></i> Date from
                </div>
                <input type="date" class="form-control" id="date_from" placeholder="Month From" style="width: 100%;">
            </div>
        </div>

        <div class="col-xs-2">
            <div class="input-group">
                <div class="input-group-addon bg-green">
                    <i class="fa fa-calendar"></i> Date to
                </div>
                <input type="date" class="form-control" id="date_to" placeholder="Month To" style="width: 100%;">
            </div>
        </div>

        {{-- <div class="col-xs-2">
            <div class="btn-group pull-right">                     
                <button class="btn btn-danger btn-outline btn-sm" onclick="clearDate()"><i class="fa fa-close"></i> Clear Date</button>
            </div>
        </div> --}}

        <div class="col-xs-3 pull-right">
            <div class="btn-group pull-right">
                {{-- <button class="btn btn-success btn-outline btn-sm" onclick="getData()"><i class="fa fa-search"></i> Filter</button> --}}
                <a href="{{ url('index/return') }}" class="btn btn-primary btn-outline btn-sm"><i class="fa fa-arrow-left"></i> Return Page</a>
                <button class="btn btn-danger btn-outline btn-sm" onclick="clearDate()"><i class="fa fa-close"></i> Clear Date</button>
                <button class="btn btn-primary btn-outline btn-sm" onclick="render_data('all')"><i class="fa fa-refresh"></i> Refresh</button>
            </div>
        </div>
    </div>
    <div class="row" style="padding: 0 1%;" id="data_container">
        
    </div>
    <br>
    <div id="data_dynamic" class="row" style="padding: 0 1%;">
    </div>
@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/exporting.js') }}"></script>
    <script src="{{ url('js/export-data.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Setup Data
        const global_return_logs = [];
        var sloc = [];
        @foreach ($storage_location as $sloc )
            sloc.push('{{ $sloc->storage_location }}');
        @endforeach

        $(document).ready(function() {                    

            $('#select_location').val('all').trigger('change');
            render_data('all');
            
            $('#select_location').change(function() {
                if($('#select_location').val() == 'all'){
                    render_data('all');
                    $('.sloc_description').html('');
                }else{
                    render_data($('#select_location').val());
                    @foreach ($storage_location as $sloc )
                        if($('#select_location').val() == '{{ $sloc->storage_location }}'){
                            $('.sloc_description').html('{{ $sloc->storage_location }} - {{ $sloc->location }}');
                        }
                    @endforeach
                }
            });

            $('#date_from').change(function() {
                if($('#select_location').val() == 'all'){
                    render_data('all', $('#date_from').val(), $('#date_to').val());
                }else{
                    render_data($('#select_location').val(), $('#date_from').val(), $('#date_to').val());
                }
            });

            $('#date_to').change(function() {
                if($('#select_location').val() == 'all'){
                    render_data('all', $('#date_from').val(), $('#date_to').val());
                }else{
                    render_data($('#select_location').val(), $('#date_from').val(), $('#date_to').val());
                }
            });

            $('.select2').select2({
                dropdownParent: $('.select_location_container')
            });

        });


        function render_data(location, date_from, date_to){     
            $('#loading').show();                   
            $('#data_container').html("");                        

            let html = ""; 
            // BODY
                html += '<div class="col-xs-6 top-chart">';
                html += '<div class="box" style="padding: 1%">';
                    html += '<h3 style="text-align: center;">5 Highest Return Process - Body Products<br><span class="sloc_description"></span></h3>';
                html += '<table class="table table-bordered table-striped table-hover" id="table_body_'+location+'">';
                html += '<thead style="background-color: rgba(126,86,134,.7);">';
                html += '<tr>';
                html += '<th style="width: 1%;">Material</th>';
                html += '<th style="width: 5%;">Description</th>';
                html += '<th style="width: 1%; text-align:center;">Return Pcs</th>';
                html += '<th style="width: 1%;">Amount</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody id="body_table_body_'+location+'">';
                html += '</tbody>';                
                html += '</table>';
                html += '<br>';
                html += '<div id="chart_main_body_'+location+'" class="chart_style" style="width: 99%; border-radius:10px;"></div>';
                html += '</div>';
                html += '</div>';
                
            // KEY
                html += '<div class="col-xs-6 top-chart">';
                html += '<div class="box" style="padding: 1%">';
                html += '<h3 style="text-align: center;">5 Highest Return Process - Key Products<br><span class="sloc_description"></span></h3>';
                html += '<table class="table table-bordered table-striped table-hover" id="table_key_'+location+'">';
                html += '<thead style="background-color: rgba(126,86,134,.7);">';
                html += '<tr>';
                html += '<th style="width: 1%;">Material</th>';
                html += '<th style="width: 5%;">Description</th>';
                html += '<th style="width: 1%; text-align:center;">Return Pcs</th>';
                html += '<th style="width: 1%;">Amount</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody id="body_table_key_'+location+'">';
                html += '</tbody>';                
                html += '</table>';
                html += '<br>';
                html += '<div id="chart_main_key_'+location+'" class="chart_style" style="width: 99%; border-radius:10px;"></div>';
                html += '</div>';
                html += '</div>';                            

            $('#data_container').append(html);
            
            if(location == 'all'){
                fetchReturnLogs('body', 'all', date_from, date_to);
                fetchReturnLogs('key', 'all' , date_from, date_to);
                
            }else{
                fetchReturnLogs('body', location);
                fetchReturnLogs('key', location);
            }                        
        }

        function fetchReturnLogs(material_category, storage_location, date_from, date_to) {

            let category = material_category;
            let location = storage_location;

            var data = {
                material_category: category,
                storage_location: location,
                date_from: date_from,
                date_to: date_to
            }

            $.get('{{ url('fetch/return/logs') }}', data, function(data, status) {
                if (status == 'success') {
                    $.each(data.return_logs, function(index, value) {
                        global_return_logs.push(value);
                    });                    

                    renderMainChart(category, location);
                }
            })
        }
        
        function renderMainChart(material_category, storage_location) {
            const ng_name = [];
            const std_price = [];
            const counts = {};
            const return_logs = []                                     


            if(storage_location == 'all'){
                $.each(global_return_logs, function(index, value) {
                    if (value.material_category == material_category.toUpperCase()) {
                        return_logs.push(value);                    
                    }
                });
            } else {
                $.each(global_return_logs, function(index, value) {
                    if (value.issue_location == storage_location.toUpperCase()) {
                        if (value.material_category == material_category.toUpperCase()) {
                            return_logs.push(value);                    
                        }
                    }                
                });
            }                    

            const material_numbers = return_logs.map(log => log.material_number);

            var tableData = "";
            var total = 0;
            var total_return = 0;
            for (var i = 0; i < return_logs.length; i++) {
                tableData += '<tr>';
                tableData += '<td>' + return_logs[i].material_number + '</td>';
                tableData += '<td>' + return_logs[i].material_description + '</td>';
                tableData += '<td style="text-align:center;">' + return_logs[i].quantity + '</td>';                
                tableData += '<td> $' + return_logs[i].amount.toFixed(2) + '</td>';
                tableData += '</tr>';
                total_return = total_return + parseInt(return_logs[i].quantity);
                total = total + parseFloat(return_logs[i].amount);
            }

            $('#body_table_' + material_category + '_'+ storage_location).append(tableData);            

            return_logs.forEach(log => {
                const ngItems = log.ng.split(",");
                ngItems.forEach(item => {
                    const name = item.split("_")[0];
                    if (!ng_name.includes(name)) {
                        ng_name.push(name);
                    }                    
                    const count = parseInt(item.split("_")[1]);

                    if (!counts[name]) {
                        counts[name] = new Array(return_logs.length).fill(0);
                    }
                    const index = material_numbers.indexOf(log.material_number);
                    counts[name][index] += count;
                });
            });

            const series = ng_name.map((name, index) => ({
                name,
                data: counts[name],
            }));            

            var gmcs = [];
            for (var i = 0; i < return_logs.length; i++) {
                var ng = [];                
                for (var j = 0; j < series.length; j++) {
                    ng.push(parseInt(series[j].data[i]) * return_logs[i].standard_price/1000);                    
                }
                gmcs.push({
                    gmc: return_logs[i].material_number,
                    std_price: return_logs[i].standard_price,
                    ng: ng,                    
                });
            }            

            var stdseries = [];
            for (var j = 0; j < ng_name.length; j++) {
                var data = [];
                for (var i = 0; i < gmcs.length; i++) {
                    data.push(gmcs[i].ng[j]);
                }
                stdseries.push({
                    name: ng_name[j],
                    data: data
                });
            }

            Highcharts.chart('chart_main_'+material_category + '_'+ storage_location, {
                chart: {
                    type: 'column',
                },

                title: {
                    text: '',
                },

                xAxis: {
                    categories: material_numbers,
                    // categories: gmcs.map(gmc => gmc.gmc),
                    crosshair: true,
                    labels: {
                        formatter: function() {
                            var material_number = this.value;
                            var material_description = return_logs.find(log => log.material_number == material_number).material_description;
                            return material_number + ' - ' + material_description;
                        },
                    },
                },

                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: 'Amount',
                    },
                    labels: {
                        formatter: function() {
                            return '$' + this.axis.defaultLabelFormatter.call(this);
                        },
                    },
                },

                tooltip: {
                    // headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    // pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td><td style="padding:0"><b>${point.y:.2f}</b></td>',
                    // footerFormat: '<tr><td>Total: </td><td><b>${point.total:.2f}</b></td></tr></table>',
                    // shared: false,
                    // useHTML: true

                    formatter:function () {
                        var tooltip = '<span style="font-size:10px"><b>' + this.x + '</b></span><br/>';                        
                        tooltip += '<span style="font-size:10px color:'+ this.series.color +'"><b>' + this.series.name + '</b></span><br/>';

                        quantity = return_logs.find(log => log.material_number == this.x).quantity;
                        quantity = this.point.y / return_logs.find(log => log.material_number == this.x).standard_price * 1000;
                        tooltip += '<span style="font-size:10px">Quantity: </span><span style="font-size:10px"><b>' + quantity.toFixed(0) + '</b></span><br/>';
                        total_quantity = return_logs.find(log => log.material_number == this.x).quantity;
                        tooltip += '<span style="font-size:10px">Amount: </span><span style="font-size:10px"><b>$' + this.y.toFixed(2) + '</b></span><br/>';
                        tooltip += '<span style="font-size:10px">Total Qty: </span><span style="font-size:10px"><b>' + total_quantity + '</b></span><br/>';
                        tooltip += '<span style="font-size:10px">Total: </span><span style="font-size:10px"><b>$' + this.point.total.toFixed(2) + '</b></span><br/>';

                        return tooltip;
                    }
                },

                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },

                series: stdseries,

            });        
            
            $('#loading').hide();
            clearAll();
        }        

        function clearAll(){
            global_return_logs.length = 0;
        }

        function clearDate() {
            $('#date_from').val('');
            $('#date_to').val('').change();
        }

        function sortArray(array, property, direction) {
            direction = direction || 1;
            array.sort(function compare(a, b) {
                let comparison = 0;
                if (a[property] > b[property]) {
                    comparison = 1 * direction;
                } else if (a[property] < b[property]) {
                    comparison = -1 * direction;
                }
                return comparison;
            });
            return array; // Chainable
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '<?php echo e(url('images/image-screen.png')); ?>',
                sticky: false,
                time: '2000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '<?php echo e(url('images/image-stop.png')); ?>',
                sticky: false,
                time: '2000'
            });
        }
    </script>
@endsection

