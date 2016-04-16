@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
{{{ trans("action.{$mode}") }}} {{ trans('sanatorium/categories::categories/common.title') }}
@stop

{{-- Queue assets --}}
{{ Asset::queue('validate', 'platform/js/validate.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Inline styles --}}
@section('styles')
@parent
<style type="text/css">
.attributes-inline hr, .attributes-inline .btn-primary,
.attributes-inline legend {
	display: none;
}
</style>
@stop

{{-- Page content --}}
@section('page')

<section class="panel panel-default panel-tabs">

	{{-- Form --}}
	<form id="shop-form" action="{{ request()->fullUrl() }}" role="form" method="post" data-parsley-validate>

		{{-- Form: CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<header class="panel-heading">

			<nav class="navbar navbar-default navbar-actions">

				<div class="container-fluid">

					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#actions">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>

						<a class="btn btn-navbar-cancel navbar-btn pull-left tip" href="{{ route('admin.sanatorium.categories.categories.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
							<i class="fa fa-reply"></i> <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
						</a>

						<span class="navbar-brand">{{{ trans("action.{$mode}") }}} <small>{{{ $category->exists ? $category->id : null }}}</small></span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							@if ($category->exists)
							<li>
								<a href="{{ route('admin.sanatorium.categories.categories.delete', $category->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
									<i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.delete') }}}</span>
								</a>
							</li>
							@endif

							<li>
								<button class="btn btn-primary navbar-btn" data-toggle="tooltip" data-original-title="{{{ trans('action.save') }}}">
									<i class="fa fa-save"></i> <span class="visible-xs-inline">{{{ trans('action.save') }}}</span>
								</button>
							</li>

						</ul>

					</div>

				</div>

			</nav>

		</header>

		<div class="panel-body">

			<div role="tabpanel">

				{{-- Form: Tabs --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="active" role="presentation"><a href="#general-tab" aria-controls="general-tab" role="tab" data-toggle="tab">{{{ trans('sanatorium/categories::categories/common.tabs.general') }}}</a></li>
				</ul>

				<div class="tab-content">

					{{-- Tab: General --}}
					<div role="tabpanel" class="tab-pane fade in active" id="general-tab">

						<div class="row">

							<div class="col-sm-2">

								<div class="attributes-inline">

									@attributes($category, ['category_icon'])

								</div>

							</div>

							<div class="col-sm-10">

								<fieldset>

									<div class="form-group">

										<label class="control-label">

											{{ trans('sanatorium/categories::categories/model.general.parent') }}

										</label>

										<select name="parent" class="form-control">

											<option value="0">
												{{ trans('sanatorium/categories::categories/model.root') }}
											</option>

											@foreach($categories as $parent)

												<option value="{{ $parent->id }}" {{ $category->parent == $parent->id ? 'selected' : '' }} >{{ $parent->category_title }}</option>

											@endforeach

										</select>

									</div>

									<div class="attributes-inline">

										@attributes($category, ['category_title'])

									</div>

									<div class="attributes-inline">

										@attributes($category, ['category_description'])

									</div>

									<div class="attributes-inline">

										@attributes($category, ['category_long_description'])

									</div>

								</fieldset>

							</div>
						</div>

					</div>

				</div>				

			</div>

		</div>

	</form>

</section>
@stop
