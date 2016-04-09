<?php
    $childId   = ! empty($child) ? "{$child->id}_%s" : 'new-child_%s';
    $childName = ! empty($child) ? "children[{$child->id}]%s" : 'new-child_%s';
?>

<div class="form-group{{ (empty($child) || ( ! empty($child) and $child->type != 'category')) ? ' hide' : null }}" data-item-type="category">
	<label class="control-label" for="{{ sprintf($childId, 'page_id') }}">Select a category</label>

	<select class="form-control input-sm" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, '[category][page_id]') }}" id="{{ sprintf($childId, 'category_uri') }}">
		@foreach ($categories as $category)
		<option value="{{ $category->id }}"{{ Input::old('category.page_id', ! empty($child) ? $child->page_id : null) == $category->id ? ' selected="selected"' : null }}>{{ $category->uri ?: '/' }}</option>
		@endforeach
	</select>
</div>
