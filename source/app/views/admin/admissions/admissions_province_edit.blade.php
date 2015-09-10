@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop


{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}
        </h3>
    </div>

    <div id="show_info" align="center">
        <div align="left">备注：点击[修改该生信息]后，出现以下页面</div>
        <br>
        <div align="center">
            <h3>
                修改学生信息
            </h3>
        </div>
        <br>
        <form id="form" name="form" class="form-horizontal" method="post"  autocomplete="off" action="{{URL::to('admin/admissions/'.$admission->id.'/edit_admissions_province')}}">
            <!-- CSRF Token -->
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
            <!-- ./ csrf token -->
            <input type="hidden" name="id" id="id" value="{{$admission->id}}">
            <table id="edit_admissions" class="table table-striped table-hover table-bordered" style="width: 1000px">
                <thead>
                <tr>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.infoItem') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.sourceInfo') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.newInfo') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.infoItem') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.sourceInfo') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.newInfo') }}}</th>
                </tr>
                </thead>
                    <tr>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                        <td class="col-md-1">{{$admission->fullname}}</td>
                        <td class="col-md-1"><input type="text" id="student_name" name="student_name" class="width120"></td>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_id') }}}</th>
                        <td class="col-md-1">{{$admission->studentno}}</td>
                        <td class="col-md-1"></td>
                    </tr>
                    <tr>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.dateofbirth') }}}</th>
                        <td class="col-md-1">{{$admission->dateofbirth}}</td>
                        <td class="col-md-1"><input type="text" id="dateofbirth" name="dateofbirth" class="date width120">
                        </td>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.gender') }}}</th>
                        <td class="col-md-1">  @if ($admission->gender=='f')
                                女
                            @elseif ($admission->gender=='m')
                                男
                            @endif
                        </td>
                        <td class="col-md-1">
                            <select id="gender" name="gender" class="width120">
                                <option value="">请选择</option>
                                <option value="m">男</option>
                                <option value="f">女</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.certification_type') }}}</th>
                        <td class="col-md-1">
                            @if ($admission->idtype == 1)
                                身份证
                            @elseif ($admission->idtype == 2)
                                军官证
                            @elseif ($admission->idtype == 3)
                                护照
                            @elseif ($admission->idtype == 4)
                                港澳居民证件
                            @elseif ($admission->idtype == 5)
                                其他
                            @endif
                        </td>
                        <td class="col-md-1">
                            <select id="idtype" name="idtype" class="width120">
                                <option value="">请选择</option>
                                <option value="1">身份证</option>
                                <option value="2">军官证</option>
                                <option value="3">护照</option>
                                <option value="4">港澳居民证件</option>
                                <option value="5">其他</option>
                            </select>
                        </td>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.certification_code') }}}</th>
                        <td class="col-md-1">{{$admission->idnumber}}</td>
                        <td class="col-md-1"><input id="idnumber" name="idnumber" type="text" class="width120"></td>
                    </tr>
                    <tr>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.source_classification') }}}</th>
                        <td class="col-md-1">
                            <?php
                            switch ($admission->formerlevel) {
                                case 1 :
                                    echo '高中毕业';
                                    break;
                                case 2 :
                                    echo '职高毕业';
                                    break;
                                case 3 :
                                    echo '中专毕业';
                                    break;
                                case 4 :
                                    echo '技校毕业';
                                    break;
                                case 5 :
                                    echo '专科毕业';
                                    break;
                                case 6 :
                                    echo '本科毕业';
                                    break;
                                case 7 :
                                    echo '硕士研究生毕业';
                                    break;
                                case 8 :
                                    echo '博士研究生毕业';
                                    break;
                                case 9 :
                                    echo '其他毕业';
                                    break;
                            }
                            ?>
                        </td>
                        <td class="col-md-1">
                            <select id="formerlevel" name="formerlevel" class="width120">
                                <option value="">请选择</option>
                                <option value="1">高中毕业</option>
                                <option value="2">职高毕业</option>
                                <option value="3">中专毕业</option>
                                <option value="4">技校毕业</option>
                                <option value="5">专科毕业</option>
                                <option value="6">本科毕业</option>
                                <option value="7">硕士研究生毕业</option>
                                <option value="8">博士研究生毕业</option>
                                <option value="9">其他毕业</option>
                            </select>
                        </td>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.graduationDate') }}}</th>
                        <td class="col-md-1">{{$admission->dategraduated}}</td>
                        <td class="col-md-1"><input id="dategraduated" name="dategraduated" type="text" class="width120"></td>
                    </tr>
                    <tr>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.graduationSchool') }}}</th>
                        <td class="col-md-1">{{$admission->formerschool}}</td>
                        <td class="col-md-1"><input id="formerschool" name="formerschool" type="text"  class="width120"></td>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.source_major') }}}</th>
                        <td class="col-md-1">{{$admission->attainmentcert}}</td>
                        <td class="col-md-1"><input id="attainmentcert" name="attainmentcert" type="text" class="width120"></td>
                        <input type="hidden" id="id" name="id" value="{{$admission->id}}">
                    </tr>
                    <tr>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.nationgroup') }}}</th>
                        <td>
                            <?php
                            switch ($admission->nationgroup){
                                case '01' :
                                    echo '汉族';
                                    break;
                                case '02' :
                                    echo '蒙古族';
                                    break;
                                case '03' :
                                    echo '回族';
                                    break;
                                case '04' :
                                    echo '藏族';
                                    break;
                                case '05' :
                                    echo '维吾尔族';
                                    break;
                                case '06' :
                                    echo '苗族';
                                    break;
                                case '07' :
                                    echo '彝族';
                                    break;
                                case '08' :
                                    echo '壮族';
                                    break;
                                case '09' :
                                    echo '布依族';
                                    break;
                                case '10' :
                                    echo '朝鲜族';
                                    break;
                                case '11' :
                                    echo '满族';
                                    break;
                                case '12' :
                                    echo '侗族';
                                    break;
                                case '13' :
                                    echo '瑶族';
                                    break;
                                case '14' :
                                    echo '白族';
                                    break;
                                case '15' :
                                    echo '土家族';
                                    break;
                                case '16' :
                                    echo '哈尼族';
                                    break;
                                case '17' :
                                    echo '哈萨克族';
                                    break;
                                case '18' :
                                    echo '傣族';
                                    break;
                                case '19' :
                                    echo '黎族';
                                    break;
                                case '20' :
                                    echo '傈僳族';
                                    break;
                                case '21' :
                                    echo '佤族';
                                    break;
                                case '22' :
                                    echo '畲族';
                                    break;
                                case '23' :
                                    echo '高山族';
                                    break;
                                case '24' :
                                    echo '拉祜族';
                                    break;
                                case '25' :
                                    echo '水族';
                                    break;
                                case '26' :
                                    echo '东乡族';
                                    break;
                                case '27' :
                                    echo '纳西族';
                                    break;
                                case '28' :
                                    echo '景颇族';
                                    break;
                                case '29' :
                                    echo '柯尔克孜族';
                                    break;
                                case '30' :
                                    echo '土族';
                                    break;
                                case '31' :
                                    echo '达斡尔族';
                                    break;
                                case '32' :
                                    echo '仫佬族';
                                    break;
                                case '33' :
                                    echo '羌族';
                                    break;
                                case '34' :
                                    echo '布朗族';
                                    break;
                                case '35' :
                                    echo '撒拉族';
                                    break;
                                case '36' :
                                    echo '毛南族';
                                    break;
                                case '37' :
                                    echo '仡佬族';
                                    break;
                                case '38' :
                                    echo '锡伯族';
                                    break;
                                case '39' :
                                    echo '阿昌族';
                                    break;
                                case '40' :
                                    echo '普米族';
                                    break;
                                case '41' :
                                    echo '塔吉克族';
                                    break;
                                case '42' :
                                    echo '怒族';
                                    break;
                                case '43' :
                                    echo '乌孜别克族';
                                    break;
                                case '44' :
                                    echo '俄罗斯族';
                                    break;
                                case '45' :
                                    echo '鄂温克族';
                                    break;
                                case '46' :
                                    echo '崩龙族';
                                    break;
                                case '47' :
                                    echo '保安族';
                                    break;
                                case '48' :
                                    echo '裕固族';
                                    break;
                                case '49' :
                                    echo '京族';
                                    break;
                                case '50' :
                                    echo '塔塔尔族';
                                    break;
                                case '51' :
                                    echo '独龙族';
                                    break;
                                case '52' :
                                    echo '鄂伦春族';
                                    break;
                                case '53' :
                                    echo '赫哲族';
                                    break;
                                case '54' :
                                    echo '门巴族';
                                    break;
                                case '55' :
                                    echo '珞巴族';
                                    break;
                                case '56' :
                                    echo '基诺族';
                                    break;
                                case '97' :
                                    echo '其它';
                                    break;
                                case '98' :
                                    echo '外国血统中国籍人士';
                                    break;
                            }
                            ?>
                        </td>
                        <td>
                            <select id="nationgroup" name="nationgroup" class="width120">
                                <option value="">请选择</option>
                                @for($i=1;$i<=9;$i++)
                                    <option value="{{Lang::get('table.nationgroup_0'.$i)}}">{{Lang::get('table.nationgroup_0'.$i)}}</option>
                                @endfor
                                @for($i=10;$i<=56;$i++)
                                    <option value="{{Lang::get('table.nationgroup_'.$i)}}">{{Lang::get('table.nationgroup_'.$i)}}</option>
                                @endfor
                                <option value="{{Lang::get('table.nationgroup_97')}}">{{Lang::get('table.nationgroup_97')}}</option>
                                <option value="{{Lang::get('table.nationgroup_98')}}">{{Lang::get('table.nationgroup_98')}}</option>
                            </select>
                        </td>
                        <th class="col-md-1"></th>
                        <td></td>
                        <td></td>
                    </tr>
            </table>
            <div class="form-group" align="center">
                <button id="btnSave" value="1" type="submit" class="btn btn-small btn-info" >
                    {{{ Lang::get('admin/admissions/table.save') }}}</button>
                <button id="btnReset" value="2"  type="reset" class="btn btn-small btn-info" >
                    {{{ Lang::get('admin/admissions/table.reset') }}}</button>
                <button id="btnCancel" value="4"  type="button" class="btn btn-small btn-info" onclick="history.go(-1);">
                    {{{ Lang::get('admin/admissions/table.cancel') }}}</button>
            </div>
            <div id="show">
                <input type="hidden" id="btnValue" value="1" />
            </div>
        </form>

    </div>
@stop

@section('styles')

    <style>
        .width120{
            text-align:left;
            width:150px;
        }
    </style>
@stop


{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        $(document).ready(
                function() {
                    $("#form").submit(function () {
                        var student_name = $("#student_name").val();
                        var dateofbirth = $("#dateofbirth").val();
                        var gender = $("#gender").val();
                        var idtype = $("#idtype").val();
                        var idnumber = $("#idnumber").val();
                        var formerlevel = $("#formerlevel").val();
                        var dategraduated = $("#dategraduated").val();
                        var formerschool = $("#formerschool").val();
                        var attainmentcert = $("#attainmentcert").val();
                        var nationgroup = $("#nationgroup").val();


                        if ((student_name != "") && (student_name.search(/(^(?!.*?_$)[\u4e00-\u9fa5]+$)/) == -1))  {
                            alert("姓名只接受中文信息！");
                            $("#student_name").focus();
                            return false;
                        }

                        if ((idnumber != "") && (idnumber.search(/(^(?!.*?_$)[0-9_a-zA-Z]+$)/) == -1) ) {
                            alert("证件号只能是数字、字母！");
                            $("#idnumber").focus();
                            return false;
                        }

                        if ((dategraduated != "") && (dategraduated.search(/^[0-9]{4}-[0-1]?[0-9]{1}-[0-3]?[0-9]{1}$/) == -1)) {
                            alert("您输入的日期格式有误，正确格式应为'yyyy-mm-dd'");
                            $("#dategraduated").focus();
                            return false;
                        }

                        if ((formerschool != "") && (formerschool.search(/(^(?!.*?_$)[\u4e00-\u9fa5]+$)/) == -1))  {
                            alert("毕业学校只接受中文信息！");
                            $("#formerschool").focus();
                            return false;
                        }

                        if ((attainmentcert != "") && (attainmentcert.search(/(^(?!.*?_$)[\u4e00-\u9fa5]+$)/) == -1))  {
                            alert("专业只接受中文信息！");
                            $("#attainmentcert").focus();
                            return false;
                        }

                        return true;
                    });
                }
        );
    </script>

@stop