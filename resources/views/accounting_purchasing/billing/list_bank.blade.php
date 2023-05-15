@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
    #bodytableBank > tr:hover {
        cursor: pointer;
        background-color: #7dfa8c;
    }
    table.table-bordered{
        border:1px solid black;
        vertical-align: middle;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        vertical-align: middle;
        text-align: left;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(150,150,150);
        vertical-align: middle;
        text-align: left;
    }

    table.table-bordered > tfoot > tr > th{
        text-align: right;
    }

    .container {
      display: block;
      position: relative;
      padding-left: 35px;
      margin-bottom: 12px;
      cursor: pointer;
      font-size: 16px;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }

    /* Hide the browser's default checkbox */
    .container input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
      position: absolute;
      top: 0;
      left: 0;
      height: 25px;
      width: 25px;
      background-color: #eee;
    }

    /* On mouse-over, add a grey background color */
    .container:hover input ~ .checkmark {
      background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .container input:checked ~ .checkmark {
      background-color: #2196F3;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }

    /* Show the checkmark when checked */
    .container input:checked ~ .checkmark:after {
      display: block;
    }

    /* Style the checkmark/indicator */
    .container .checkmark:after {
      left: 10px;
      top: 5px;
      width: 5px;
      height: 12px;
      border: solid white;
      border-width: 0 3px 3px 0;
      -webkit-transform: rotate(45deg);
      -ms-transform: rotate(45deg);
      transform: rotate(45deg);
    }
</style>
@stop
@section('header')
<section class="content-header">
    <h1>
        {{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
    </h1>
    <ol class="breadcrumb">
    </ol>
</section>
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid" style="padding: 7px;min-height: 100vh">
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>
        <div class="row" style="padding: 5px">
            <div class="col-sm-12 col-xs-12" style="text-align: center;">
                <div class="card" style="margin-bottom: 5px !important">
                    <div class="card-body" style="padding: 0px">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                        <div class="col-md-8" style="padding:0">
                            <form method="GET" action="{{ url("export/bank/list") }}">
                                <div class="col-md-6" style="padding-left: 5px;text-align: left;display: inline-block;">
                                    <label>Date From</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon bg-green" style="border: none; background-color: #469937; color: white;">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
                                    </div>
                                </div>
                                <div class="col-md-6" style="padding-left: 5px;text-align: left;display: inline-block;">
                                    <label>Date To</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon bg-green" style="border: none; background-color: #469937; color: white;">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding-left: 5px;text-align: left;display: inline-block;padding-top: 10px">
                                    <label>Currency</label>
                                    <div class="form-group">
                                        <select class="form-control select2" multiple="multiple" id='currencySelect' onchange="changeCurrency()" data-placeholder="Select Currency" style="width: 100%;color: black !important">
                                            <option value="IDR">IDR</option>
                                            <option value="EUR">EUR</option>
                                            <option value="USD">USD</option>
                                            <option value="JPY">JPY</option>
                                        </select>
                                        <input type="text" name="currency" id="currency" style="color: black !important" hidden>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary col-sm-14" type="button" onclick="fetchData()">Search</button>
                                        <button class="btn btn-success" type="submit"><i class="fa fa-download"></i> Export Excel</button>
                                        <!-- <input class="btn btn-success" type="submit" name="publish" value="Export Excel Without Merge">
                                        <input class="btn btn-warning" type="submit" name="save" value="Export Excel With Merge"> -->
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-4" style="padding:0">
                            <div class="col-md-4" style="padding-left: 5px;padding-top: 5px;text-align: left;display: inline-block;">
                                <label>Sub Total (IDR)</label>
                            </div>

                            <div class="col-md-8" style="padding-left: 5px;text-align: left;display: inline-block;">
                                <input type="text" class="form-control" id="total_idr" name="total_idr" placeholder="Total IDR">
                            </div>

                            <div class="col-md-4" style="padding-left: 5px;padding-top: 10px;text-align: left;display: inline-block;">
                                <label>Sub Total (USD)</label>
                            </div>

                            <div class="col-md-8" style="padding-left: 5px;margin-top:5px;text-align: left;display: inline-block;">
                                <input type="text" class="form-control" id="total_usd" name="total_usd" placeholder="Total USD">
                            </div>

                            <div class="col-md-4" style="padding-left: 5px;padding-top: 10px;text-align: left;display: inline-block;">
                                <label>Sub Total (JPY)</label>
                            </div>

                            <div class="col-md-8" style="padding-left: 5px;margin-top:5px;text-align: left;display: inline-block;">
                                <input type="text" class="form-control" id="total_jpy" name="total_jpy" placeholder="Total JPY">
                            </div>

                            <div class="col-md-4" style="padding-left: 5px;padding-top: 10px;text-align: left;display: inline-block;">
                                <label>Sub Total (EUR)</label>
                            </div>

                            <div class="col-md-8" style="padding-left: 5px;margin-top:5px;text-align: left;display: inline-block;">
                                <input type="text" class="form-control" id="total_eur" name="total_eur" placeholder="Total EUR">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-xs-12" style="text-align: center;">
                <div class="col-md-12" style="padding: 10px;overflow-x: scroll;">
                    <table class="table table-bordered table-striped table-hover" id="tableBank">
                        <thead style="background-color: rgba(126,86,134,.7);">
                            <tr>
                                <th>Date Payment</th>
                                <th>Vendor Name</th>
                                <th>Bank Name</th>
                                <th>Bank Beneficiary Name</th>
                                <th>Bank Beneficiary No</th>
                                <th>Currency</th>
                                <th>Amount</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody id="bodytableBank">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

@stop
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        $('.select2').select2();
        fetchData();
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true
        });
    });

    function changeCurrency() {
        $("#currency").val($("#currencySelect").val());
    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function fetchData() {
        $('#loading').show();
        var data = {
            currency:$('#currency').val(),
            date_from:$('#date_from').val(),
            date_to:$('#date_to').val(),
        }
        $.get('{{ url("fetch/list_bank") }}',data,  function(result, status, xhr){
            if(result.status){
                $('#tableBank').DataTable().clear();
                $('#tableBank').DataTable().destroy();
                $("#bodytableBank").html('');
                var bodyTable = '';

                var total_idr = 0;
                var total_usd = 0;
                var total_jpy = 0;
                var total_eur = 0;

                for (var i = 0; i < result.jurnal.length; i++) {
                    bodyTable += '<tr>';
                    bodyTable += '<td>'+getFormattedDateTime(new Date(result.jurnal[i].jurnal_date))+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].supplier_code+' - '+result.jurnal[i].supplier_name+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].bank_branch+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].bank_beneficiary_name+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].bank_beneficiary_no+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].currency+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].amount+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].remark+'</td>';
                    bodyTable += '</tr>';

                    // var amount = result.jurnal[i].amount.replace(",", "");
                    var amount = result.jurnal[i].amount.replace(/,/g, '');

                    if (result.jurnal[i].currency == "IDR") {
                        total_idr += parseFloat(amount);
                    }
                    else if (result.jurnal[i].currency == "USD") {
                        total_usd += parseFloat(amount);
                    }
                    else if (result.jurnal[i].currency == "JPY") {
                        total_jpy += parseFloat(amount);
                    }
                    else if (result.jurnal[i].currency == "EUR") {
                        total_eur += parseFloat(amount);
                    }
                }

                $('#total_idr').val(total_idr.toLocaleString());
                $('#total_usd').val(total_usd.toLocaleString());
                $('#total_jpy').val(total_jpy.toLocaleString());
                $('#total_eur').val(total_eur.toLocaleString());

                $('#bodytableBank').append(bodyTable);

                $('#tableBank tfoot th').each( function () {
                    var title = $(this).text();
                    $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
                } );

                var table = $('#tableBank').DataTable({
                    'dom': 'Bfrtip',
                    'responsive':true,
                    'lengthMenu': [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                    ],
                    'buttons': {
                        buttons:[
                        {
                            extend: 'pageLength',
                            className: 'btn btn-default',
                        },
                        {
                            extend: 'copy',
                            className: 'btn btn-success',
                            text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-info',
                            text: '<i class="fa fa-file-excel-o"></i> Excel',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-warning',
                            text: '<i class="fa fa-print"></i> Print',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        }
                        ]
                    },
                    'paging': true,
                    'lengthChange': true,
                    'pageLength': 10,
                    'searching': true   ,
                    'ordering': true,
                    'order': [],
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true,
                    initComplete: function() {
                    this.api()
                        .columns([1,2])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#tableBank th").eq([dd]).text();
                            var select = $(
                                    '<select style="width:100%"><option value="" style="font-size:11px;">All</option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
                                    var vals = d;
                                    if ($("#tableBank th").eq([dd]).text() == 'Category') {
                                        vals = d.split(' ')[0];
                                    }
                                    select.append('<option style="font-size:11px;" value="' +
                                        d + '">' + vals + '</option>');
                                });
                        });
                    },
                });

                table.columns().every( function () {
                    var that = this;

                    $( 'input', this.footer() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                            .search( this.value )
                            .draw();
                        }
                    } );
                } );

                $('#tableBank tfoot tr').appendTo('#tableBank thead');

                $('#loading').hide();
            }else{
                openErrorGritter('Error!',result.message);
            }
        });
    }

    function onlyUnique(value, index, self) {
      return self.indexOf(value) === index;
    }

    function dynamicSort(property) {
        var sortOrder = 1;
        if(property[0] === "-") {
            sortOrder = -1;
            property = property.substr(1);
        }
        return function (a,b) {
            var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
            return result * sortOrder;
        }
    }


    function uniqByKeepFirst(a, key) {
        let seen = new Set();
        return a.filter(item => {
            let k = key(item);
            return seen.has(k) ? false : seen.add(k);
        });
    }

    function openSuccessGritter(title, message){
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-success',
            image: '{{ url("images/image-screen.png") }}',
            sticky: false,
            time: '3000'
        });
    }

    function openErrorGritter(title, message) {
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-danger',
            image: '{{ url("images/image-stop.png") }}',
            sticky: false,
            time: '3000'
        });
    }

    function getFormattedDateTime(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = (date.getMonth()+1).toString();
        month = month.length > 1 ? month : '0' + month;

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }

        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        if (second < 10) {
            second = "0" + second;
        }
        
        return year+'-'+month+'-'+day;
    }
</script>
@endsection