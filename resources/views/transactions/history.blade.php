@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        .table>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        table.table-bordered {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
            padding: 2px 5px 2px 5px;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        #loading {
            display: none;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
        </h1>
    </section>
@endsection

@section('content')
    <section class="content" style="font-size: 0.9vw;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <input type="hidden" id="materials" value="{{ $materials }}">
        <div class="row">
            <div class="col-xs-12" style="">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body">
                        {{-- <form id="formFilter" method="get" action="{{ url("fetch/ymes/history")}}"> --}}
                        <form id="formFilter">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Entry Date</label>
                                            <input type="text" class="form-control datepicker" id="filterEntryDate"
                                                name="filterEntryDate" placeholder="Select Date">
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Issue Location</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        id="filterIssue" name="filterIssue[]"
                                                        data-placeholder="Select Location" style="width: 100%;">
                                                        <option></option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ $location->storage_location }}">
                                                                {{ $location->storage_location }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Receive Location</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        id="filterReceive" name="filterReceive[]"
                                                        data-placeholder="Select Location" style="width: 100%;">
                                                        <option></option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ $location->storage_location }}">
                                                                {{ $location->storage_location }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Product</label>
                                                    <select class="form-control select2" id="filterProduct"
                                                        name="filterProduct" data-placeholder="Select Product"
                                                        style="width: 100%;">
                                                        <option></option>
                                                        <option value="FG">Finished Goods/KD (9010)</option>
                                                        <option value="WIP">Semi Finished (9030)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Posting Date</label>
                                            <input type="text" class="form-control datepicker" id="filterPostingDate"
                                                name="filterPostingDate" placeholder="Select Date">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Material Number</label>
                                            <select class="form-control select2" multiple="multiple" id="filterMaterial"
                                                name="filterMaterial[]" data-placeholder="Select Material"
                                                style="width: 100%;">
                                                <option></option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->item_code }}">{{ $material->item_code }} -
                                                        {{ $material->item_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-7">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Category</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        id="filterCategory" name="filterCategory[]"
                                                        data-placeholder="Select Category" style="width: 100%;">
                                                        <option></option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category }}">{{ $category }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-5">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Sync Status</label>
                                                    <select class="form-control select2" id="filterSync" name="filterSync"
                                                        data-placeholder="Select Status" style="width: 100%;">
                                                        <option></option>
                                                        <option value="0">Not Synced</option>
                                                        <option value="1">Synced</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <button type="submit" class="btn btn-primary pull-right" style="width: 10%; margin: 5px;">Search</button> --}}
                        </form>
                        <button class="btn btn-primary pull-right" style="width: 10%; margin: 5px;"
                            onclick="fetchSearch()">Search</button>
                        <button class="btn btn-danger pull-right" style="width: 10%; margin: 5px;"
                            onclick="clearAll()">Clear</button>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body">
                        <table id="tableResult" class="table table-bordered table-striped table-hover"
                            style="width: 100%;">
                            <thead style="color: black; background-color: grey;">
                                <tr>
                                    <th style="width: 0.1%; text-align: left;">Category</th>
                                    <th style="width: 0.1%; text-align: right;">Posting</th>
                                    <th style="width: 0.1%; text-align: right;">Entry</th>
                                    <th style="width: 0.1%; text-align: left;">Slip No.</th>
                                    <th style="width: 0.1%; text-align: left;">Serial No.</th>
                                    <th style="width: 1%; text-align: left;">Material</th>
                                    <th style="width: 10%; text-align: left;">Description</th>
                                    <th style="width: 1%; text-align: left;">Issue</th>
                                    <th style="width: 1%; text-align: left;">Receive</th>
                                    <th style="width: 1%; text-align: right;">Quantity</th>
                                    <th style="width: 0.1%; text-align: left;">Created By</th>
                                    <th style="width: 1%; text-align: center;">Synced</th>
                                    <th style="width: 1%; text-align: left;">Sync</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('.select2').select2();
            $('#filterEntryDate').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 10,
                locale: {
                    format: 'YYYY-MM-DD H:mm'
                }
            });
            $('#filterPostingDate').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
            $('#syncDate').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: "yyyy-mm-dd"
            });
            $('#filterEntryDate').val("");
            $('#filterPostingDate').val("");
            $('body').toggleClass("sidebar-collapse");
            // fetchDataTable();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        function clearAll() {
            location.reload(true);
        }

        (function() {
            document.getElementById('formFilter').addEventListener('submit', function(event) {
                var phone = document.getElementById('filterPostingDate').value.length,
                    email = document.getElementById('filterEntryDate').value.length;

                if (phone === 0 && email === 0) {
                    event.preventDefault();
                    alert('Harus mengisi salah satu Result Date atau Posting Date');
                }
            }, false);
        })();

        // function fetchDataTable(){
        // 	$('#tableResult').DataTable({
        // 		'dom': 'Bfrtip',
        // 		'responsive':true,
        // 		'lengthMenu': [
        // 		[ 25, 50, -1 ],
        // 		[ '25 rows', '50 rows', 'Show all' ]
        // 		],
        // 		'buttons': {
        // 			buttons:[
        // 			{
        // 				extend: 'pageLength',
        // 				className: 'btn btn-default',
        // 			},
        // 			{
        // 				extend: 'copy',
        // 				className: 'btn btn-success',
        // 				text: '<i class="fa fa-copy"></i> Copy',
        // 				exportOptions: {
        // 					columns: ':not(.notexport)'
        // 				}
        // 			},
        // 			{
        // 				extend: 'excel',
        // 				className: 'btn btn-info',
        // 				text: '<i class="fa fa-file-excel-o"></i> Excel',
        // 				exportOptions: {
        // 					columns: ':not(.notexport)'
        // 				}
        // 			},
        // 			{
        // 				extend: 'print',
        // 				className: 'btn btn-warning',
        // 				text: '<i class="fa fa-print"></i> Print',
        // 				exportOptions: {
        // 					columns: ':not(.notexport)'
        // 				}
        // 			},
        // 			]
        // 		},
        // 		'paging': true,
        // 		'lengthChange': true,
        // 		'searching': true,
        // 		'ordering': false,
        // 		'order': [],
        // 		'info': true,
        // 		'autoWidth': true,
        // 		"sPaginationType": "full_numbers",
        // 		"bJQueryUI": true,
        // 		"bAutoWidth": false,
        // 		"processing": true
        // 	});
        // }


        function fetchSearch() {
            var filterCategory = $('#filterCategory').val();
            var filterPostingDate = $('#filterPostingDate').val();
            var filterEntryDate = $('#filterEntryDate').val();
            var filterMaterial = $('#filterMaterial').val();
            // var material_description = $('#filterCategory').val();
            var filterIssue = $('#filterIssue').val();
            var filterReceive = $('#filterReceive').val();
            var filterSync = $('#filterSync').val();
            var filterProduct = $('#filterProduct').val();
            // var quantity = $('#filterCategory').val();
            // var created_by = $('#filterCategory').val();
            // var created_by_name = $('#filterCategory').val();
            // var synced = $('#filterCategory').val();

            if (filterEntryDate == "" && filterPostingDate == "") {
                $('#loading').hide();
                alert('Please select posting date or entry date.');
                return false;
            }

            $('#tableResult').DataTable().clear();
            $('#tableResult').DataTable().destroy();

            var data = {
                filterCategory: filterCategory,
                filterPostingDate: filterPostingDate,
                filterEntryDate: filterEntryDate,
                filterMaterial: filterMaterial,
                // material_description:material_description,
                filterIssue: filterIssue,
                filterSync: filterSync,
                filterReceive: filterReceive,
                filterProduct: filterProduct,
                // quantity:quantity,
                // created_by:created_by,
                // created_by_name:created_by_name,
                // synced:synced,
            }

            var table = $('#tableResult').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'buttons': {
                    buttons: [{
                            extend: 'pageLength',
                            className: 'btn btn-default'
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
                        },
                    ]
                },
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/ymes/history') }}",
                    "data": data
                },
                "columns": [{
                        "data": "category"
                    },
                    {
                        "data": "posting_date"
                    },
                    {
                        "data": "entry_date"
                    },
                    {
                        "data": "slip_number"
                    },
                    {
                        "data": "serial_number"
                    },
                    {
                        "data": "material_number"
                    },
                    {
                        "data": "material_description"
                    },
                    {
                        "data": "issue_location"
                    },
                    {
                        "data": "receive_location"
                    },
                    {
                        "data": "quantity"
                    },
                    {
                        "data": "created_by"
                    },
                    {
                        "data": "synced"
                    },
                    {
                        "data": "sync"
                    }
                ],
                "columnDefs": [{
                        "className": 'text-left',
                        "targets": [0, 3, 4, 5, 6, 7, 8, 10]
                    },
                    {
                        "className": 'text-center',
                        "targets": [12]
                    },
                    {
                        "className": 'text-right',
                        "targets": [1, 2, 9, 11]
                    },
                    {
                        "width": "40%",
                        "targets": 6
                    },
                ]
            });
        }

        // function syncAll(){
        // 	if(confirm("Do you want to sync this transaction data?")){
        // 		$('#loading').show();
        // 		var category = $('#filterCategory').val();
        // 		var posting_date = $('#filterPostingDate').val();
        // 		var entry_date = $('#filterEntryDate').val();
        // 		var material_number = $('#filterMaterial').val();
        // 		var issue_location = $('#filterIssue').val();
        // 		var receive_location = $('#filterReceive').val();
        // 		var sync = $('#filterSync').val();
        // 		var product = $('#filterProduct').val();

        // 		if(entry_date == "" && posting_date == ""){
        // 			$('#loading').hide();
        // 			alert('Please select posting date or entry date.');
        // 			return false;
        // 		}

        // 		$('#tableResult').DataTable().clear();
        // 		$('#tableResult').DataTable().destroy();

        // 		var data = {
        // 			category:category,
        // 			posting_date:posting_date,
        // 			entry_date:entry_date,
        // 			material_number:material_number,
        // 			issue_location:issue_location,
        // 			sync:sync,
        // 			receive_location:receive_location,
        // 			product:product,
        // 		}

        // 		$.post('{{ url('sync/ymes/transaction_all') }}', data, function(result, status, xhr){
        // 			if(result.status){
        // 				$('#tableResult').DataTable().ajax.reload(null, false);
        // 				audio_ok.play();
        // 				openSuccessGritter('Success!', result.message);
        // 				$('#loading').hide();
        // 			}
        // 			else{
        // 				audio_error.play();
        // 				openErrorGritter('Error!', result.message);
        // 				$('#loading').hide();
        // 			}
        // 		});
        // 	}
        // 	else{
        // 		return false
        // 	}

        // }

        function sync(id, action) {
            if (confirm("Do you want to sync this transaction data?")) {
                $('#loading').show();

                var data = {
                    id: id,
                    action: action
                }

                $.post('{{ url('sync/ymes/transaction') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        $('#tableResult').DataTable().ajax.reload(null, false);
                        audio_ok.play();
                        openSuccessGritter('Success!', result.message);
                        $('#loading').hide();
                    } else {
                        audio_error.play();
                        openErrorGritter('Error!', result.message);
                        $('#loading').hide();
                    }
                });
            } else {
                return false;
            }
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '3000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '3000'
            });
        }
    </script>
@endsection
