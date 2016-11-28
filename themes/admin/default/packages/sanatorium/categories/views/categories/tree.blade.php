@extends('layouts/default')

{{-- Page title --}}
@section('title')
    @parent
    {{ trans('sanatorium/categories::categories/common.title') }}
@stop

{{-- Queue assets --}}
{{ Asset::queue('sortable', 'platform/menus::js/jquery.sortable.js', 'jquery') }}
{{ Asset::queue('tree', 'sanatorium/categories::tree.scss') }}

{{-- Inline scripts --}}
@section('scripts')
    @parent
    <script type="text/javascript">
        var oldContainer;
        $(function(){
           $('#sortable > ol').sortable({
               handle: 'i.handle',
               //nested: false,
               group: 'nested',
               afterMove: function (placeholder, container) {
                   if(oldContainer != container){
                       if(oldContainer)
                           oldContainer.el.removeClass("active");
                       container.el.addClass("active");

                       oldContainer = container;
                   }
               },
               onDrop: function ($item, container, _super) {
                   container.el.removeClass("active");
                   _super($item, container);

                   console.log($item.parents('ol:first').length);
                   var items = $item.parents('ol:first').children('li').map(function(index, object) {
                       return $(object).data('id');
                   }).toArray();
                   console.log(items);

                   $.ajax({
                       url: "{{ route('admin.sanatorium.categories.categories.order') }}",
                       type: 'POST',
                       data: {
                           items: items
                       }
                   });
               }
           });

            $('#sortable ol li .collapse-toggle').click(function(event){
                event.preventDefault();
                $(this).parents('li:first').find('ol:first').collapse('toggle');
            });
        });
    </script>
@stop

{{-- Inline styles --}}
@section('styles')
    @parent
    <style type="text/css">
        #sortable ol {
            padding-left: 10px;
        }
        #sortable ol .handle {
            display: inline-block;
            padding-left: 0.5em;
            padding-right: 0.5em;
            margin-right: 0.5em;
            cursor: move;
        }
        #sortable ol li {
            margin-top: 5px;
            display: block;
        }
        #sortable ol li {
            cursor: pointer;
        }
        #sortable ol li .item-wrapper {
            padding: 5px 10px;
            background-color: #f2f2f2;
            border-radius: 2px;
            box-shadow: 0 0 2px rgba(0,0,0,0.3);
        }
        #sortable ol li a {
            padding-left: 0.5em;
            padding-right: 0.5em;
            margin-left: 0.5em;
            display: inline-block;
        }
        #sortable ol {
            list-style-type: none;
        }
    </style>
@stop

<?php

if ( !function_exists('printCategories') ) {

    function printCategories($categories, $echo = true, $root = true)
    {
        if ( empty($categories) )
            return null;

        $html = '<ol class="' . ($root ? 'items collapse in' : 'collapse'). '">';

        foreach( $categories as $category )
        {

            $html .= '<li data-id="' . $category->id . '">';
            $html .= '<div class="item-wrapper">';
            $html .= '<a href="' . route('admin.sanatorium.categories.categories.edit', $category->id) . '" class="pull-right">';
            $html .= '<i class="fa fa-pencil" aria-hidden="true"></i>';
            $html .= '</a>';
            $html .= !empty($category->children) ? '<a class="collapse-toggle pull-right" href="#"><i class="fa fa-chevron-down" aria-hidden="true"></i></a>' : '';
            $html .= '<i class="fa fa-arrows handle" aria-hidden="true"></i>';
            $html .= '<a href="' . route('admin.sanatorium.categories.categories.edit', $category->id) . '">';
            $html .= $category->category_title;
            $html .= '</a>';
            $html .= '</div>';
            $html .= printCategories($category->children, false, false);
            $html .= '</li>';

        }

        $html .= '</ol>';

        if ( $echo )
            echo $html;
        else
            return $html;

    }

}

?>

{{-- Page content --}}
@section('page')

    {{-- Grid --}}
    <section class="panel panel-default panel-grid">

        {{-- Grid: Header --}}
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

                        <span class="navbar-brand">{{{ trans('sanatorium/categories::categories/common.title') }}}</span>

                    </div>

                    {{-- Grid: Actions --}}
                    <div class="collapse navbar-collapse" id="actions">

                        <ul class="nav navbar-nav navbar-left">

                            <li class="primary">
                                <a href="{{ route('admin.sanatorium.categories.categories.create') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.create') }}}">
                                    <i class="fa fa-plus"></i> <span class="visible-xs-inline">{{{ trans('action.create') }}}</span>
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>

            </nav>

        </header>

        <div class="panel-body">

            <div id="sortable">
                <?php printCategories($categories) ?>
            </div>

        </div>

    </section>

@stop
