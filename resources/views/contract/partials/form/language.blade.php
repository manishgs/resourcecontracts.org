<div class="block translation-lang-wrapper" id="language" style="padding: 20px;">

	<div class="row">
		<div class="col-md-8 btn-group">
			@if(!isset($page) || $page != 'index')
				<p class="pull-left" style="margin-right: 10px; margin-top: 7px">Contract information translations:</p>
			@endif
			@foreach($lang->translation_lang() as $l)
				<?php
				$hash = ($view == 'show') ? '#language' : null;

				if ($lang->defaultLang() == $l['code']):
					$route      = route(sprintf('contract.%s', $view), ['id' => $contract->id]);
					$edit_route = route(
							sprintf('contract.%s.trans', 'edit'),
							['id' => $contract->id]
					);
				else:
					$route      = route(
							sprintf('contract.%s.trans', $view),
							['id' => $contract->id, 'lang' => $l['code']]
					);
					$edit_route = route(
							sprintf('contract.%s.trans', 'edit'),
							['id' => $contract->id, 'lang' => $l['code']]
					);
				endif;
				?>
				@if($lang->current_translation() == $l['code'] && (!isset($page) || $page != 'index'))
					<a class="btn btn-primary">{{$l['name']}}</a>
				@else
					@if($contract->hasTranslation($l['code']))
						<a class="btn btn-default" href="{{$route}}{{$hash}}">{{$l['name']}}</a>
					@else
						<a href="#"
						   data-placement="bottom"
						   class="btn btn-warning"
						   data-toggle="popover"
						   data-trigger="focus"
						   data-content="This contract hasn’t been translated into {{$l['name']}} yet.
						   <hr><center><a class='btn btn-primary' href='{{$edit_route}}'>+ Add {{$l['name']}}
								   translation</a></center>">
							{{$l['name']}}
						</a>
					@endif

				@endif
			@endforeach
		</div>
		@if($view == 'show' && (!isset($page) || $page != 'index' ))
			<div class="col-md-4">
				<a href="{{$edit_route}}" class="pull-right">
					<i class="glyphicon glyphicon-edit"></i>
					Edit contract information
				</a>
			</div>
		@endif
	</div>
</div>