@extends('layout.app')

@section('menu_active')
	@php($active = 'Operational')
@endsection

@section('style')
<link href="{{ asset('plugins/DataTables/css/data-table.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/bootstrap-datepicker/css/datepicker.css') }}" rel="stylesheet" />
{{-- <link href="{{ asset('plugins/bootstrap-datepicker_old/css/datepicker3.css') }}" rel="stylesheet" /> --}}

<style type="text/css">
	
</style>

@endsection

@section('script')
<script type="text/javascript" src="{{ url('plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
<script type="text/javascript" src="{{ url('plugins/fusioncharts/js/fusioncharts.js') }}"></script>
<script type="text/javascript" src="{{ url('plugins/fusioncharts/js/fusioncharts.charts.js') }}"></script>
<script type="text/javascript" src="{{ url('plugins/fusioncharts/js/themes/fusioncharts.theme.carbon.js') }}"></script>
<script type="text/javascript" src="{{ url('plugins/DataTables/js/jquery.dataTables.js') }}"></script>
<script type="text/javascript" src="{{ url('plugins/DataTables/js/dataTables.responsive.js') }}"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$('.input-daterange input').each(function() {
		    $(this).datepicker({
		    	format : 'dd-MM-yyyy',
		    	autoclose : true,
		    	todayBtn : true

		    });
		});

		$('#start').datepicker().on('changeDate', function(e) {
			$('#end').datepicker('setStartDate', $('#start').val() );
			$('#end').val('');
	    });

	    $('#h4DateRange').hide();

	});
	

	getData('TTL',{{ $now_bln }},{{ $now_thn }});
	$('#bln option[value='+{{ $now_bln }}+']').attr('selected','selected');
	$('#thn option[value='+{{ $now_thn }}+']').attr('selected','selected');
	$('#ks_produk option[value=TTL]').attr('selected','selected');

	setInterval(blink_text, 1500);

	function blink_text() {
	    $('.blink').fadeOut(500);
	    $('.blink').fadeIn(500);
	}
	function drawTable(datasrc){
		$('#accumulated').DataTable( {
			data: datasrc.atable,
			"searching": false,
			"lengthChange": false,
			"destroy" : true,
			"columns": [
				{ title: "Date" },
				{ title: "Shipment", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
				{ title: "Target"  , render: $.fn.dataTable.render.number( ',', '.', 2 ) }
			]
		} );
		$('#daily').DataTable( {
			data: datasrc.dtable,
			"searching": false,
			"lengthChange": false,
			"destroy" : true,
			"columns": [
				{ title: "Date" },
				{ title: "Shipment", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
				{ title: "Target"  , render: $.fn.dataTable.render.number( ',', '.', 2 ) }
			]
		} );
	}
	function getData(ks_produk,bln,thn) {
        $.get('{{ url('shipprod') }}', {produk: ks_produk, bulan: bln, tahun: thn}, function(data, textStatus, xhr) {
        	$('.prognosa').html(data.prognosa);
			var data_produk = data.produk;
			drawTable(data);
            FusionCharts.ready(function() {
				chartObj = new FusionCharts({
					swfUrl		: "msline",
					width		: "100%",
					height		: "400",
					// id			: 'shipmenthari',
					dataFormat	: 'json',
					renderAt	: 'chart-container',
					dataSource: {
						"chart": {
							"caption": "Daily Shipment",
							"numberprefix": "",
							"plotgradientcolor": "",
							"bgcolor": "FFFFFF",
							"showalternatehgridcolor": "0",
							"divlinecolor": "CCCCCC",
							"showvalues": "0",
							"showcanvasborder": "0",
							"canvasborderalpha": "0",
							"canvasbordercolor": "CCCCCC",
							"canvasborderthickness": "2",
							//"yaxismaxvalue": "30000",
							"captionpadding": "30",
							"linethickness": "3",
							//"yaxisvaluespadding": "15",
							"legendshadow": "0",
							"legendborderalpha": "0",
							"palettecolors": "#f8bd19,#008ee4,#33bdda,#e44a00,#6baa01,#583e78",
							"showborder": "0"
						},

						"categories": data.categories,
						"dataset": data.dataset
					}
				}).render();

				chartObj = new FusionCharts({
					swfUrl		: "msline",
					width		: "100%", 
					height		: "400",
					// id			: 'shipmentbln',	
					dataFormat	: 'json',
					renderAt	: 'chart-accumulate',
					dataSource: {
						"chart": {
							"caption": "Daily Shipment",
							"numberprefix": "",
							"plotgradientcolor": "",
							"bgcolor": "FFFFFF",
							"showalternatehgridcolor": "0",
							"divlinecolor": "CCCCCC",
							"showvalues": "0",
							"showcanvasborder": "0",
							"canvasborderalpha": "0",
							"canvasbordercolor": "CCCCCC",
							"canvasborderthickness": "2",
							//"yaxismaxvalue": "30000",
							"captionpadding": "30",
							"linethickness": "3",
							//"yaxisvaluespadding": "15",
							"legendshadow": "0",
							"legendborderalpha": "0",
							"palettecolors": "#f8bd19,#008ee4,#33bdda,#e44a00,#6baa01,#583e78",
							"showborder": "0"
						},

						"categories": data.categoriesa,
						"dataset": data.dataseta
					}
				}).render();

            });


        });
    }
    function selectDate() {
    	var dateRange = $('#form_dateRange').serializeArray();
    	$.getJSON('{{ url('shipprodrange') }}',dateRange,function(data) {
    		if(!data.error){
	    		drawTable(data);
	            FusionCharts.ready(function() {
					chartObj = new FusionCharts({
						swfUrl		: "msline",
						width		: "100%",
						height		: "400",
						// id			: 'shipmenthari',
						dataFormat	: 'json',
						renderAt	: 'chart-container',
						dataSource: {
							"chart": {
								"caption": "Daily Shipment",
								"numberprefix": "",
								"plotgradientcolor": "",
								"bgcolor": "FFFFFF",
								"showalternatehgridcolor": "0",
								"divlinecolor": "CCCCCC",
								"showvalues": "0",
								"showcanvasborder": "0",
								"canvasborderalpha": "0",
								"canvasbordercolor": "CCCCCC",
								"canvasborderthickness": "2",
								//"yaxismaxvalue": "30000",
								"captionpadding": "30",
								"linethickness": "3",
								//"yaxisvaluespadding": "15",
								"legendshadow": "0",
								"legendborderalpha": "0",
								"palettecolors": "#f8bd19,#008ee4,#33bdda,#e44a00,#6baa01,#583e78",
								"showborder": "0"
							},

							"categories": data.categories,
							"dataset": data.dataset
						}
					}).render();

					chartObj = new FusionCharts({
						swfUrl		: "msline",
						width		: "100%", 
						height		: "400",
						// id			: 'shipmentbln',	
						dataFormat	: 'json',
						renderAt	: 'chart-accumulate',
						dataSource: {
							"chart": {
								"caption": "Daily Shipment",
								"numberprefix": "",
								"plotgradientcolor": "",
								"bgcolor": "FFFFFF",
								"showalternatehgridcolor": "0",
								"divlinecolor": "CCCCCC",
								"showvalues": "0",
								"showcanvasborder": "0",
								"canvasborderalpha": "0",
								"canvasbordercolor": "CCCCCC",
								"canvasborderthickness": "2",
								//"yaxismaxvalue": "30000",
								"captionpadding": "30",
								"linethickness": "3",
								//"yaxisvaluespadding": "15",
								"legendshadow": "0",
								"legendborderalpha": "0",
								"palettecolors": "#f8bd19,#008ee4,#33bdda,#e44a00,#6baa01,#583e78",
								"showborder": "0"
							},

							"categories": data.categoriesa,
							"dataset": data.dataseta
						}
					}).render();
					$('#modal-id').modal('hide');
					$('#form').hide();
					$('#title_dateRange').html('KS PRODUCT : '+dateRange[0].value+' | '+dateRange[1].value+' to '+dateRange[2].value);
					$('#h4DateRange').show();
	            });
	        }else{
	        	alert(data.error);
	        }
    	});

    }
    function reset() {
    	$('#form').show();
    	$('#h4DateRange').hide();
    	getData('TTL',{{ $now_bln }},{{ $now_thn }});

    }
</script>
@endsection

@section('content')
<!-- begin page-header -->
<h1 class="page-header">Dashboard Operation Excellence</h1>
<!-- end page-header -->
<div class="panel panel-inverse" >
	<div class="panel-heading">
		<div class="panel-heading-btn">
			<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
		</div>
		<h4 class="panel-title"><span class="fa fa-dashboard"></span> Daily Shipment</h4>
	</div>
	<div class="panel-body">
		<h4 class="text-center" id="h4DateRange" style="margin-bottom: 30px">
			<span id="title_dateRange"></span><br><br>
			<a class="btn btn-md btn-primary" href="javascript:void(0)" onclick="reset()">Reset</a>
		</h4>
		<form class="form-horizontal" id="form">
            <div class="form-group">
                <label class="col-md-2 control-label">Product Type : </label>
                <div class="col-md-4" style="margin-bottom: 10px">
                    <select name="ks_produk" id="ks_produk" class="form-control" onchange="getData( $(this).val(),$('#bln').val(),$('#thn').val() )">
                        <option value="" selected>Pilih Produk</option>
						<option value="TTL" >Total</option>
						<option value="DMS" >Domestik</option>
						<option value="EKS" >Eskport</option>
                        @foreach($produk as $value)
                            <option value="{{ $value->produk }}">{{ $value->ket }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2" style="margin-bottom: 10px">
                	<select name="bulan" id="bln" class="form-control" 
                	onchange="getData( $('#ks_produk').val(),$(this).val(),$('#thn').val() )">
                    	<option value="1" >Januari</option>
                    	<option value="2" >Februari</option>
                    	<option value="3" >Maret</option>
                    	<option value="4" >April</option>
                    	<option value="5" >Mei</option>
                    	<option value="6" >Juni</option>
                    	<option value="7" >Juli</option>
                    	<option value="8" >Agustus</option>
                    	<option value="9" >September</option>
                    	<option value="10" >Oktober</option>
                    	<option value="11" >November</option>
                    	<option value="12" >Desember</option>
                    </select>
                </div>
                <div class="col-md-2" style="margin-bottom: 10px">
                	<select name="tahun" id="thn" class="form-control" 
                	onchange="getData( $('#ks_produk').val(),$('#bln').val(),$(this).val() )">
                    	@foreach($thn as $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2" style="margin-bottom: 10px">
                	<a class="btn btn-md btn-primary" data-toggle="modal" href='#modal-id'>Select date range</a>
                </div>
            </div>
        </form>
        
		<div class="col-md-8">
			<div id="chart-container">FusionCharts will render here</div>
		</div>
		<div class="col-md-4">
			<div>
				<div class="blink">
                	<h5>LAST UPDATE : {{$last_update}}</h5>
                </div>
				<strong style="font-size: 12pt">Prognosa : <span class="prognosa"></span> </strong>
			</div>
			<table id="daily" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Date</th>
						<th>Shipment</th>
						<th>Target</th>
					</tr>
				</thead>
				{{-- <tfoot>
					<tr>
						<th>Date</th>
						<th>Shipment</th>
						<th>Target</th>
					</tr>
				</tfoot> --}}
			</table>
		</div>
	</div>
</div>

<div class="panel panel-inverse" >
	<div class="panel-heading">
		<div class="panel-heading-btn">
			<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
		</div>

		<h4 class="panel-title"><span class="fa fa-dashboard"></span> Accumulated Shipment</h4>
	</div>
	<div class="panel-body">
		<div class="col-md-8">
			<div id="chart-accumulate">FusionCharts will render here</div>
		</div>
		<div class="col-md-4">
			<div>
				<div class="blink">
                	<h5>LAST UPDATE : {{$last_update}}</h5>
                </div>
				<strong style="font-size: 12pt">Prognosa : <span class="prognosa"></span> </strong>
			</div>
			<table id="accumulated" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Date</th>
						<th>Shipment</th>
						<th>Target</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Date</th>
						<th>Shipment</th>
						<th>Target</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-id">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Select date</h4>
			</div>
			<div class="modal-body">

				<form class="form-horizontal" id="form_dateRange">
					<div class="form-group">
                        <div class="col-md-12">
                        	<p class="text-center"> Product Type </p>
                        	<select name="ks_produk" class="form-control" style="margin-bottom: 20px">
		                        <option value="" >Pilih Produk</option>
								<option value="TTL" selected>Total</option>
								<option value="DMS" >Domestik</option>
								<option value="EKS" >Eskport</option>
		                        @foreach($produk as $value)
		                            <option value="{{ $value->produk }}">{{ $value->ket }}</option>
		                        @endforeach
		                    </select>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group input-daterange">
							    <input type="text" class="form-control" name="start" id="start" autocomplete="off">
							    <div class="input-group-addon">to</div>
							    <input type="text" class="form-control" name="end" id="end" autocomplete="off">
							</div>
                        </div>
                    </div>
				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="selectDate();">Apply</button>
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
@endsection
