<div class="form-group<%= type != 'category' ? ' hide' : null %>" data-item-type="category">
	<label class="control-label" for="<%= slug %>_category_uri">Select a category</label>

	<select class="form-control input-sm" data-item-form="<%= slug %>" name="children[<%= slug %>][category][page_id]" id="<%= slug %>_category_uri" >
		@foreach ($categories as $category)
		<option value="{{ $category->id }}"<%= category_uri == '{{ $category->id }}' ? ' selected="selected"' : null %>>/{{ $category->uri }}</option>
		@endforeach
	</select>
</div>
