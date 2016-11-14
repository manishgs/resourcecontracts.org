<div class="translation-lang-wrapper" id="language" style="padding: 20px;">
	<div class="btn-group">
		@foreach($lang->translation_lang() as $l)
			@if($lang->current_translation() == $l['code'])
				<a class="btn btn-default">{{$l['name']}}</a>
			@elseif($lang->defaultLang() == $l['code'])
				<a class="btn btn-primary" href="{{route(sprintf('contract.%s',$view), ['id'=>$contract->id])
				}}#lanugage">{{$l['name']}}</a>
			@else
				<a class="btn btn-primary" href="{{route(sprintf('contract.%s.trans',$view), ['id'=>$contract->id,
				'lang'=>$l['code']])}}#language">{{$l['name']}}</a>
			@endif
		@endforeach
	</div>
</div>