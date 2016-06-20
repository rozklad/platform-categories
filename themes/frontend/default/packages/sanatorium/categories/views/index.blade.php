@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
{{{ $category->title or $category->name }}}
@stop

{{-- Meta description --}}
@section('meta-description')
{{{ $category->description }}}
@stop

{{-- Page content --}}
@section('page')

  @if ( class_exists('Product') )

    @include('sanatorium/shop::catalog/order')

    <div class="clearfix"></div>

    @include('sanatorium/shop::catalog/row')

    <div class="clearfix"></div>

    @include('sanatorium/shop::catalog/navigation')

  @else
    <p class="alert alert-warning">
      {{ trans('sanatorium/shop::messages.errors.uninstalled') }}
    </p>
  @endif

@stop
