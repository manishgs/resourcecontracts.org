@extends('layout.app')

@section('css')
	<style>
		.select2 {
			width: 14% !important;
			float: left;
			margin-right: 20px !important;
			margin-top: 4px !important;
		}

		.index-lang {
			float: left;
			margin-left: 20px;
		}

		.index-lang .translation-lang-wrapper {
			padding: 0px !important;
		}

		.index-lang .btn-group {
			width: auto;
		}

		.index-lang .btn {
			padding: 2px 5px;
			font-size: 12px;
		}

		table tr:nth-child(odd) {
			background-color: #F5F5F5;
		}

		table tr:hover {
			background-color: #ECECEC;
		}

		table tr td {
			padding: 20px 20px 0px !important;

		}
	</style>
@stop
@section('content')
	<div class="panel panel-default">
		<div class="panel-heading">@lang('contract.all_contract')

			<div class="pull-right" role="group" aria-label="...">
				<?php
				$url = Request::all();
				$url['download'] = 1;
				?>
				<a href="{{route('bulk.text.download')}}" class="btn btn-info">@lang('global.text_download')</a>
				<a href="{{route("contract.index",$url)}}" class="btn btn-info">@lang('contract.download')</a>
				<a href="{{route('contract.import')}}" class="btn btn-default">@lang('contract.import.name')</a>
				<a href="{{route('contract.select.type')}}" class="btn btn-primary btn-import">@lang('contract.add')</a>
			</div>
		</div>

		<div class="panel-body">
			{!! Form::open(['route' => 'contract.index', 'method' => 'get', 'class'=>'form-inline']) !!}
			{!! Form::select('year', ['all'=>trans('contract.year')] + $years , Input::get('year') , ['class' =>
			'form-control']) !!}

			{!! Form::select('country', ['all'=>trans('contract.country')] + $countries , Input::get('country') ,
			['class' =>'form-control']) !!}

			{!! Form::select('category', ['all'=>trans('contract.category')] + config('metadata.category'),
			Input::get('category') ,
			['class' =>'form-control']) !!}

			{!! Form::select('resource', ['all'=>trans('contract.resource')] + trans_array($resources,
			'codelist/resource') ,
			Input::get
			('resource') ,
			['class' =>'form-control']) !!}
			{!! Form::text('q', Input::get('q') , ['class' =>'form-control','placeholder'=>trans('contract.search_contract')]) !!}

			{!! Form::submit(trans('contract.search'), ['class' => 'btn btn-primary']) !!}
			{!! Form::close() !!}
			<br/>
			<br/>
			<table class="table table-contract table-responsive">
				@forelse($contracts as $contract)
					<tr>
						<td width="70%">
							<i class="glyphicon glyphicon-file"></i>
							<a href="{{route('contract.show', $contract->id)}}">{{$contract->metadata->contract_name or $contract->metadata->project_title}}</a>
							<span class="label label-default"><?php echo strtoupper(
										$contract->metadata->language
								);?></span>
							<div style="margin-top: 10px;">
								<div style="float: left">
									<span style="margin-right: 20px">
										<i class="glyphicon glyphicon-time"></i>
										{{$contract->metadata->signature_year}}
									</span>
									<span style="margin-right: 20px">
									<i class="glyphicon glyphicon glyphicon-map-marker"></i>
										{{$contract->metadata->country->name}}
									</span>
									<i class="glyphicon glyphicon-comment"></i>
									{{$contract->annotations->count()}}
								</div>
								<div class="index-lang">
									@include('contract.partials.form.language', ['view' => 'show', 'page'=>'index'] )
								</div>
							</div>
						</td>
						<td align="right">{{getFileSize($contract->metadata->file_size)}}</td>
						<td align="right"><?php echo $contract->createdDate('M d, Y');?></td>
					</tr>
				@empty
					<tr>
						<td colspan="2">@lang('contract.contract_not_found')</td>
					</tr>
				@endforelse

			</table>
			@if ($contracts->lastPage()>1)
				<div class="text-center paginate-wrapper">
					<div class="pagination-text">@lang('contract.showing') {{($contracts->currentPage()==1)?"1":($contracts->currentPage()-1)*$contracts->perPage()}} @lang('contract.to') {{($contracts->currentPage()== $contracts->lastPage())?$contracts->total():($contracts->currentPage())*$contracts->perPage()}} @lang('contract.of') {{$contracts->total()}} @lang('contract.contract')</div>
					{!! $contracts->appends($app->request->all())->render() !!}
				</div>
			@endif
		</div>
	</div>
@endsection
@section('script')
	<link href="{{asset('css/select2.min.css')}}" rel="stylesheet"/>
	<script src="{{asset('js/select2.min.js')}}"></script>
	<script type="text/javascript">
		var lang_select = '@lang('global.select')';
		$('select').select2({placeholder: lang_select, allowClear: true, theme: "classic"});
	</script>
@stop