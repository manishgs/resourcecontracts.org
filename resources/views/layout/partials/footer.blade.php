<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/script.js')}}"></script>
@yield('script')
@yield('script2')
<script>
	$(document).ready(function () {
		$('[data-toggle="popover"]').click(function (e) {
			e.preventDefault();
		});

		$('[data-toggle="popover"]').popover({html:true});
	});
</script>
</body>
</html>