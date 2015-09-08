@extends('admin.layouts.mymodal')

{{-- Content --}} 
@section('content')
    <div class="form-group" align="center">
        <h3>
            {{{ $teachingPlan_code }}}专业模块表
        </h3>
    </div>
    <table id="courses" class="table table-striped table-hover">
        <tr>
            <th class="col-md-3">{{{ Lang::get('admin/course/table.module_name') }}}</th>
            <th class="col-md-3">{{{ Lang::get('admin/course/table.obligatory_credit') }}}</th>
            <th class="col-md-3">{{{ Lang::get('admin/course/table.min_credit_module') }}}</th>
        </tr>
        <?php $counter = 0; $msum = 0;?>
        @foreach($rsts as $rst)
        <tr>
            <td>{{{ $rst->mname }}}</td>
            <td>{{{ $rst->scredit == null ? 0 :  $rst->scredit}}}</td>
            <td>{{{ $rst->mcredit }}}</td>
            <td></td>
        </tr>
        <?php $counter += $rst->scredit;$msum += $rst->mcredit;?>
        @endforeach
        <tr>
            <td>合计</td>
            <td>{{{ $counter }}}</td>
            <td>{{{ $msum }}}</td>
            <td></td>
        </tr>
    </table>

    <div class="pull-right">
        <button class="btn btn-default btn-small btn-inverse close_popup"><span class="glyphicon glyphicon-circle-arrow-left"></span> {{{ Lang::get('general.back') }}}</button>
    </div>



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

