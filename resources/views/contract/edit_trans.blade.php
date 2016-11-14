@extends('layout.app')

@section('content')
	<div class="panel panel-default">
		<div class="panel-heading"> Editing
			<span>{{$contract->metadata->contract_name or $contract->metadata->project_title}}</span></div>

		@include('contract.partials.form.language', ['view'=>'edit'])

		<div class="panel-body contract-wrapper">
			@if (count($errors) > 0)
				<div class="alert alert-danger">
					<strong>@lang('contract.whoops')</strong> @lang('contract.problem')<br><br>
					<ul>
						@foreach ($errors->all() as $error)
							<li>{!! $error !!}</li>
						@endforeach
					</ul>
				</div>
			@endif
			{!! Form::model($contract,['route' => array('contract.update', $contract->id) ,  'class'=>'form-horizontal contract-form', 'method'=>'PATCH','files' => true]) !!}
			{!! Form::hidden('contract_id', $contract->id)!!}
			{!! Form::hidden('trans', $lang->current_translation())!!}
			@include('contract.form', ['action'=>'edit', 'edit_trans'=>false, 'contact' => $contract])
			{!! Form::close() !!}
		</div>
	</div>
@stop

@section('script2')

	<script>
		$(function () {
			var arr = ['#contract_identifier', '#signature_date', '#signature_year', '#source_url', '#date_retrieval',
				'.el_government_identifier', '#deal_number', '#matrix_page'];
			$('select').prop('disabled', true);
			$("input:checkbox").prop('disabled', true);
			$("input:radio").prop('disabled', true);

			$('.col-sm-7').click(function () {
				var inp = $(this).find('input');
				if (inp.length > 0 && inp.prop('disabled') == true) {
					console.log('input is disabled');
				}
			});

			$('.add-new-btn').hide();
			$.each(arr, function (index, value) {
				$(value).prop('disabled', true);
			});
		});
	</script>


@stop