@extends('admin.layouts.mymodal')

{{-- Content --}} 
@section('content')
    <div class="form-group" align="center">
        <h3>
            年度适用规则
        </h3>
    </div>
    <table id="courses" class="table table-striped table-hover">
        <tr>
            <th class="col-md-14">{{{ Lang::get('admin/course/table.program') }}}</th>
            <th class="col-md-14">{{{ Lang::get('admin/course/title.year') }}}</th>
            <th class="col-md-14">{{{ Lang::get('admin/course/title.semester') }}}</th>
            <th class="col-md-14">{{{ Lang::get('admin/course/title.major') }}}</th>
            <th class="col-md-14">{{{ Lang::get('admin/course/table.is_activated') }}}</th>
            <th class="col-md-14">{{{ Lang::get('admin/course/title.classification') }}}</th>
            <th class="col-md-16">{{{ Lang::get('admin/course/table.student_classification') }}}</th>

        </tr>
        @foreach($rsts as $rst)
        <tr>
            <td>{{{ $rst->code }}}</td>
            <td>{{{ $rst->year }}}</td>
            @if ($rst->semester == 1)
                <td>春季</td>
            @elseif ($rst->semester == 2)
                <td>秋季</td>
            @else
                <td>{{{ $rst->semester }}}</td>
            @endif
            <td>{{{ $rst->major }}}</td>
            @if ($rst->is_activated == 1)
                <td>启</td>
            @elseif ($rst->is_activated == 0)
                <td>关</td>
            @else
                <td>{{{ $rst->is_activated }}}</td>
            @endif
            @if ($rst->major_classification == 12)
                <td>本科</td>
            @elseif ($rst->major_classification == 14)
                <td>专科</td>
            @else
                <td>{{{ $rst->major_classification }}}</td>
            @endif
            @if ($rst->student_classification == 12)
                <td>本科</td>
            @elseif ($rst->student_classification == 14)
                <td>专科</td>
            @else
                <td>{{{ $rst->student_classification }}}</td>
            @endif
        </tr>
        @endforeach

    </table>

    <div class="pull-right">
        <button class="btn btn-default btn-small btn-inverse close_popup"><span class="glyphicon glyphicon-circle-arrow-left"></span> {{{ Lang::get('general.back') }}}</button>
    </div>
@stop

@section('styles')

<style>


    .col-md-14 {
        width: 14%;
    }
    .col-md-16 {
            width: 16%;
        }
</style>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('.close_popup').click(function(){
            parent.jQuery.fn.colorbox.close();
            //return false;
        });
    });
</script>
@stop

