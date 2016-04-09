

{{-- Inline scripts --}}
@section('scripts')
@parent

@stop

{{-- Inline styles --}}
@section('styles')
@parent
<style type="text/css">
.category-tree, .category-tree ul {
	list-style-type: none;
}
.category-tree li {
	
}
.category-tree li a {

}
.category-tree label {
	margin-bottom: 0;
	line-height: 34px;
}
.category-tree .parsley-help-block {
	display: none;
}
</style>
@stop
	
<ul class="category-tree" id="{{ $id }}">

	@foreach( $categories as $category )

		@include('sanatorium/categories::widgets/child')

	@endforeach

</ul>