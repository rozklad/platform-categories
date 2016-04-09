<li>

	<label class="control-label" for="category-{{ $category->id }}">

		<input type="checkbox" name="categories[]" value="{{ $category->id }}" id="category-{{ $category->id }}" {{ (in_array($category->id, $active_categories) ? 'checked' : '' ) }}>

		{{ $category->category_title }}

		@if ($category->children)

			<ul>

			@foreach($category->children as $category)

				@include('sanatorium/categories::widgets/child')

			@endforeach

			</ul>

		@endif

	</label>

</li>