@extends('admin.layouts.modal')

{{-- Content --}} 
@section('content')

{{-- Create Form --}}
<form  id="create_edit_Form"  method="post" action="" autocomplete="off">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <!-- ./ csrf token -->

    <!-- Tabs Content -->
    
        <!--id Name -->
        <div class="form-group {{{ $errors->has('name') || $errors->has('credit') ? 'error' : '' }}}">
            <label class="rlbl" for="name">{{{ Lang::get('admin/course/table.name') }}}</label>
            @if ($mode == 'create')
                <input type="text" class="twidth" name="name" id="name" value="{{{ Input::old('name') }}}" />
                  {{ $errors->first('name', '<span class="help-inline">:message</span>') }}
            @else
                <input type="text" class="twidth" name="name" id="name" value="{{{ $course->name }}}" />
                  {{ $errors->first('name', '<span class="help-inline">:message</span>') }}
            @endif
            <label class="rlbl" for="credit">{{{ Lang::get('admin/course/table.credit') }}}</label>
            @if ($mode == 'create')
                <input type="text" class="twidth" name="credit" id="credit" value="{{{ Input::old('credit') }}}" />
                  {{ $errors->first('credit', '<span class="help-inline">:message</span>') }}
            @else
                <input type="text" class="twidth" name="credit" id="credit" value="{{{ $course->credit }}}" />
                  {{ $errors->first('credit', '<span class="help-inline">:message</span>') }}
            @endif
        </div>
        <!--abbreviation credit -->
        <div class="form-group {{{ $errors->has('abbreviation') || $errors->has('is_practice') ? 'error' : '' }}}">
            <label class="rlbl" for="abbreviation">{{{ Lang::get('admin/course/table.abbreviation') }}}</label>
            @if ($mode == 'create')
                <input type="text" class="twidth" name="abbreviation" id="abbreviation" value="{{{ Input::old('abbreviation') }}}" />
                  {{ $errors->first('abbreviation', '<span class="help-inline">:message</span>') }}
            @else
                <input type="text" class="twidth" name="abbreviation" id="abbreviation" value="{{{ $course->abbreviation }}}" />
                  {{ $errors->first('abbreviation', '<span class="help-inline">:message</span>') }}
            @endif
            <label class="rlbl" for="is_practice">{{{ Lang::get('admin/course/table.is_practice') }}}</label>
            <select class="twidth" size="1" tabindex="5" name="is_practice" id="is_practice">
            @if ($mode == 'create')
                @if (Input::old('is_practice') == '1')
                    <option value="0">无</option>
                    <option value="1" selected="selected">有</option>
                @else
                    <option value="0" selected="selected">无</option>
                    <option value="1">有</option>
                @endif
            @else
                @if ($course->is_practice == '1')
                    <option value="0">无</option>
                        <option value="1" selected="selected">有</option>
                    @else
                        <option value="0" selected="selected">无</option>
                        <option value="1">有</option>
                    @endif
            @endif
            </select>

        </div>
        <!--res_teacher pra_tag -->
        <div class="form-group {{{ $errors->has('lecturer') || $errors->has('is_certification') ? 'error' : '' }}}">
            <label class="rlbl" for="lecturer">{{{ Lang::get('admin/course/table.lecturer') }}}</label>
            @if ($mode == 'create')
                <input type="text" class="twidth" name="lecturer" id="lecturer" value="{{{ Input::old('lecturer') }}}" />
                  {{ $errors->first('lecturer', '<span class="help-inline">:message</span>') }}
            @else
                <input type="text" class="twidth" name="lecturer" id="lecturer" value="{{{ $course->lecturer }}}" />
                  {{ $errors->first('lecturer', '<span class="help-inline">:message</span>') }}
            @endif
            <label class="rlbl" for="is_certification">{{{ Lang::get('admin/course/table.is_certification') }}}</label>
            <select class="twidth" size="1" tabindex="5" name="is_certification" id="is_certification">
            @if ($mode == 'create')
                @if (Input::old('is_certification') == '1')
                     <option value="0">无</option>
                     <option value="1" selected="selected">有</option>
                @else
                     <option value="0" selected="selected">无</option>
                     <option value="1">有</option>
                @endif
            @else
                @if ($course->is_certification == '1')
                     <option value="0">无</option>
                     <option value="1" selected="selected">有</option>
                @else
                     <option value="0" selected="selected">无</option>
                     <option value="1">有</option>
                @endif
            @endif
            </select>

        </div>
        <!--define_date is_certification -->
        <div class="form-group {{{ $errors->has('define_date') || $errors->has('remark') ? 'error' : '' }}}">
            <label class="rlbl" for="define_date">{{{ Lang::get('admin/course/table.define_date') }}}</label>
            @if ($mode == 'create')
                <input type="date" class="twidth" name="define_date" id="define_date" value="{{{ Input::old('define_date') }}}" />
                    {{ $errors->first('define_date', '<span class="help-inline">:message</span>') }}
            @else
                <input type="date" class="twidth" name="define_date" id="define_date" value="{{{ $course->define_date }}}" />
                    {{ $errors->first('define_date', '<span class="help-inline">:message</span>') }}
            @endif
            <label class="rlbl" for="remark">{{{ Lang::get('admin/course/table.remark') }}}</label>
            @if ($mode == 'create')
                <input type="text" class="twidth" name="remark" id="remark" value="{{{ Input::old('remark') }}}" />
                  {{ $errors->first('remark', '<span class="help-inline">:message</span>') }}
            @else
                <input type="text" class="twidth" name="remark" id="remark" value="{{{ $course->remark }}}" />
                  {{ $errors->first('remark', '<span class="help-inline">:message</span>') }}
            @endif

        </div>
       <!--department_id remark -->
        <div class="form-group {{{ $errors->has('department_id') || $errors->has('classification') ? 'error' : '' }}}">
            <label class="rlbl" for="department_id">{{{ Lang::get('admin/course/table.department_id') }}}</label>
            <select class="twidth" size="1" tabindex="5" name="department_id" id="department_id">
                <option value="" selected="selected">请选择</option>
                @if ($mode == 'create')
                    @foreach ($departs as $depart)
                        <option value="{{{ $depart->id }}}"{{{ $depart->id == Input::old('department_id') ? ' selected="selected"' : '' }}}>
                        {{{ $depart->name }}} </option>
                    @endforeach
                @else
                    @foreach ($departs as $depart)
                        <option value="{{{ $depart->id }}}"{{{ $depart->id == $course->department_id ? ' selected="selected"' : '' }}}>
                        {{{ $depart->name }}} </option>
                    @endforeach
                @endif
            </select>
            <label class="rlbl" for="classification">{{{ Lang::get('admin/course/table.classification') }}}</label>
             <select class="twidth" size="1" tabindex="5" name="classification" id="classification">
              @if ($mode == 'create')
                  @if (Input::old('classification') == '12')
                      <option value="14">专科</option>
                      <option value="12" selected="selected">本科</option>
                  @else
                      <option value="14" selected="selected">专科</option>
                      <option value="12">本科</option>
                  @endif
              @else
                  @if ($course->classification == '12')
                      <option value="14">专科</option>
                          <option value="12" selected="selected">本科</option>
                      @else
                          <option value="14" selected="selected">专科</option>
                          <option value="12">本科</option>
                      @endif
              @endif
              </select>
        </div>
        <!--state classification -->
        <div class="form-group {{{ $errors->has('state') ? 'error' : '' }}}">
             <label class="rlbl" for="state">{{{ Lang::get('admin/course/table.state') }}}</label>
             <select class="twidth" size="1" tabindex="5" name="state" id="state">
             @if ($mode == 'create')
                 @if (Input::old('state',1) == '1')
                     <option value="0">停用</option>
                     <option value="1" selected="selected">启用</option>
                 @else
                     <option value="0" selected="selected">停用</option>
                     <option value="1">启用</option>
                 @endif
             @else
                 @if ($course->state == '1')
                     <option value="0">停用</option>
                         <option value="1" selected="selected">启用</option>
                     @else
                         <option value="0" selected="selected">停用</option>
                         <option value="1">启用</option>
                     @endif
             @endif
             </select>
          </div>
    <!-- ./ tabs content -->

    <!-- Form Actions -->
    <div class="form-group">
        <div class="controls">
            <button class="btn-cancel close_popup">{{{ Lang::get('admin/depart/table.cancel') }}}</button>
            <button id="btnOK" type="submit" class="btn btn-success">{{{
				Lang::get('admin/depart/table.ok') }}}</button>
		</div>
    </div>
    <!-- ./ form actions -->
</form>
@stop

@section('styles')
<style>
    .rlbl{
        text-align:right;
        width:120px;
            }

    .lalign{
        text-align:left;
        width:120px;
    }

    .twidth{
        width:150px;
    }
    .width1{
        width:80px;
    }
    .width2{
        width:100px;
    }
</style>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        Date.prototype.format = function(partten)
        {
            if(partten ==null||partten=='')
            {
                partten = 'yyyy-mm-dd';
            }
            var y = this.getFullYear();
            var m = this.getMonth()+1;
            var d = this.getDate();
            var r = partten.replace(/y+/gi,y);
            r = r.replace(/m+/gi,(m<10?"0":"")+m);
            r = r.replace(/d+/gi,(d<10?"0":"")+d);
            return r;
        }
        var d = new Date().format('yyyy-mm-dd');
        $("#define_date").val(d);
        $("#btnOK").click(function(){
            var ex = /^[\u4e00-\u9fa5\w()]+$/;
            var str = $("#name").val();
            if (!ex.test(str)) {
                alert("课程名字只能包括文字、数字、括号、下划线");
                $("#name").focus();
                return false;
            }

            str = $("#abbreviation").val();
            if (!ex.test(str)) {
                alert("课程简称只能包括文字、数字、括号、下划线");
                $("#abbreviation").focus();
                return false;
            }

            ex = /^[\u4e00-\u9fa5]+$/;
            str = $("#lecturer").val();
            if (!ex.test(str)) {
                alert("责任教师只能包括文字");
                $("#lecturer").focus();
                return false;
            }
            str = $("#define_date").val();
            if (str=='') {
                alert("请输入定义日期");
                $("#define_date").focus();
                return false;
            }
            str = $("#department_id").val();
            if (str=='') {
                alert("请选择教学部门");
                $("#department_id").focus();
                return false;
            }
            ex = /^[1-9]$/;
            str = $("#credit").val();

            if (!ex.test(str)) {
                alert("请输入学分，范围（1-9）");
                $("#credit").focus();
                return false;
            }
            ex = /^[\u4e00-\u9fa5]+$/;
            str = $("#remark").val();
            if (str!='' && !ex.test(str)) {
                alert("备注只能包括文字");
                $("#remark").focus();
                return false;
            }
        });
    });
</script>
@stop

