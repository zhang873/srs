@extends('admin.layouts.frame_modal')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
{{-- Web site Title --}}
    <br><br>
    <div id="show_info" align="center">
        <form method="post" id="form" action="">
            <!-- CSRF Token -->
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
            <!-- ./ csrf token -->
            <!-- Tabs Content -->
        @if (!empty($admission))
                <input type="hidden" name="student_id" value="{{$admission->id}}" />
                <table id="studentInfo" class="table table-striped table-hover table-bordered" style="width: 800px">
                    <thead><tr><td colspan="4" align="center"><h4>{{Lang::get('admin/admissions/table.student_basic_info')}}</h4></td></tr></thead>
                <tr>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                    <td class="col-md-1">{{$admission->fullname}}</td>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.gender') }}}</th>
                    <td class="col-md-1">@if($admission->gender == 'f')
                            女
                        @elseif($admission->gender == 'm')
                            男
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.dateofbirth') }}}</th>
                    <td>{{$admission->dateofbirth}}</td>
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
                </tr>
                <tr>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.politicalStatus') }}}</th>
                    <td>
                        <?php
                        switch ($admission->politicalstatus) {
                        case '01' :
                        echo '中共党员';
                        break;
                        case '02' :
                        echo '共青团员';
                        break;
                        case '03' :
                        echo '民革会员';
                        break;
                        case '04' :
                        echo '民盟盟员';
                        break;
                        case '05' :
                        echo '民进会员';
                        break;
                        case '06' :
                        echo '民建会员';
                        break;
                        case '07' :
                        echo '农工党党员 ';
                        break;
                        case '08' :
                        echo '致公党党员';
                        break;
                        case '09' :
                        echo '九三学社社员';
                        break;
                        case '10' :
                        echo '台盟盟员';
                        break;
                        case '11' :
                        echo '无党派民主人士';
                        break;
                        case '12' :
                        echo '群众';
                        break;
                        case '13' :
                        echo '其他';
                        break;
                        }
                            ?>
                    </td>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.source_classification') }}}</th>
                    <td>
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
                </tr>
                <tr>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.certification_type') }}}</th>
                    <td>
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
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.certification_code') }}}</th>
                    <td>{{$admission->idnumber}}</td>
                </tr>
                <tr>
                    <th class="col-md-1">户口</th>
                    <td>
                        @if ($admission->hukou == 0)
                            城镇户口
                        @elseif ($admission->hukou == 1)
                            农村户口
                        @elseif ($admission->hukou == 2)
                            外国公民
                        @elseif ($admission->hukou == 3)
                            其他
                        @endif
                    </td>
                    <th class="col-md-1"></th>
                    <td></td>
                </tr>
                <tr>
                    <th class="col-md-1">原专业</th>
                    <td>{{$admission->attainmentcert}}</td>
                    <th class="col-md-1">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                    <td>
                        @if ($admission->type == 12)
                            本科
                        @elseif ($admission->type == 14)
                            专科
                        @endif
                    </td>
                </tr>
                    <tr>
                        <th class="col-md-1">原教学点</th>
                        <td>{{$admission->campus_name}}</td>
                        <th class="col-md-1"></th>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="col-md-1">工作单位</th>
                        <td><input type="text" value="{{$admission->company_organization}}" id="company_organization" name="company_organization"></td>
                        <th class="col-md-1">单位地址</th>
                        <td><input type="text" value="{{$admission->company_address}}" id="company_address" name="company_address"></td>
                    </tr>
                    <tr>
                        <th class="col-md-1">单位邮政编码</th>
                        <td><input type="text" value="{{$admission->company_postcode}}" id="company_postcode" name="company_postcode"></td>
                        <th class="col-md-1">单位电话</th>
                        <td><input type="text" value="{{$admission->company_phone}}" id="company_phone" name="company_phone"></td>
                    </tr>
                    <tr>
                        <th class="col-md-1">本人通讯地址</th>
                        <td><input type="text" value="{{$admission->address}}" id="address" name="address"></td>
                        <th class="col-md-1">本人联系方式</th>
                        <td><input type="text" value="{{$admission->phone}}" id="phone" name="phone"></td>
                    </tr>
                    <tr>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.mobile') }}}</th>
                        <td><input type="text" value="{{$admission->mobile}}" id="mobile" name="mobile"></td>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.postcode') }}}</th>
                        <td><input type="text" value="{{$admission->postcode}}" id="postcode" name="postcode"></td>
                    </tr>
                    <tr>
                        <th class="col-md-1">{{{ Lang::get('admin/admissions/table.email') }}}</th>
                        <td><input type="text" value="{{$admission->email}}" id="email" name="email"></td>
                        <th class="col-md-1"></th>
                        <td></td>
                    </tr>
                    <input type="hidden" name="student_id" id="student_id" value="{{$admission->id}}">

            </table>
            <!-- ./ form actions -->
                <div class="form-group" align="center">
                    <button id="btnSave" name="state" type="submit" class="btn btn-small btn-info" >
                        {{{ Lang::get('admin/admissions/table.save') }}}</button>

                </div>

            @else
                <table id="studentInfo" class="table table-striped table-hover table-bordered" style="width: 800px">
                    <thead><tr><td colspan="4" align="center"><h4>{{Lang::get('admin/admissions/table.student_basic_info')}}</h4></td></tr></thead>
                <tr><td align="center">{{Lang::get('admin/admissions/messages.no_student_info')}}</td></tr>
                </table>
                <div align="center"><button type="button" onclick="history.go(-1)">返回</button></div>
            @endif
        </form>
    </div>
@stop


{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        $(document).ready(function() {
            $("#form").submit(function () {
                var company_organization = $("#company_organization").val();
                var company_address = $("#company_address").val();
                var company_postcode = $("#company_postcode").val();
                var company_phone = $("#company_phone").val();
                var mobile = $("#mobile").val();
                var phone = $("#phone").val();
                var address = $("#address").val();
                var postcode = $("#postcode").val();
                var email = $("#email").val();

                if ((company_organization!="")  && (company_organization.search(/(^(?!_)(?!.*?_$)[0-9_\u4e00-\u9fa5]+$)/) == -1)) {
                    alert("工作单位只能输入文本信息！");
                    $("#company_organization").focus();
                    return false;
                }

                if ((company_address!="")  && (company_address.search(/(^(?!_)(?!.*?_$)[0-9_\u4e00-\u9fa5]+$)/) == -1)) {
                    alert("单位地址只能输入文本信息！");
                    $("#company_address").focus();
                    return false;
                }

                if (company_postcode!=""){
                    if (company_postcode.search(/(^(?!.*?_$)[0-9_]+$)/) == -1) {
                        alert("单位邮政编码只能输入数字！");
                        $("#postcode").focus();
                        return false;
                    }
                    if (company_postcode.length != 6){
                        alert("单位邮政编码只能是6位数字！");
                        $("#postcode").focus();
                        return false;
                    }
                }
                if ((company_phone!="")  && (company_phone.search(/^((0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$/) == -1)) {
                    alert("单位电话只能输入数字、下划线！");
                    $("#company_phone").focus();
                    return false;
                }

                if ((address!="")  && (address.search(/(^(?!_)(?!.*?_$)[0-9_\u4e00-\u9fa5]+$)/) == -1)) {
                    alert("本人通讯地址只能输入文字、数字、下划线！");
                    $("#address").focus();
                    return false;
                }

                if ((mobile!="")  && (mobile.search(/(^(?!.*?_$)[0-9_]+$)/) == -1)) {
                    alert("移动电话只能输入数字！");
                    $("#mobile").focus();
                    return false;
                }

                if ((phone!="")  && (phone.search(/(^(?!_)(?!.*?_$)[0-9_]+$)/) == -1)) {
                    alert("本人联系方式只能输入数字、下划线！");
                    $("#phone").focus();
                    return false;
                }

                if (email!="")  {//判断
                    var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
                    if (!reg.test(email)) {
                        alert('邮箱格式不正确，请重新填写!');
                        return false;
                    }
                }

                if (postcode!=""){
                    if (postcode.search(/(^(?!.*?_$)[0-9_]+$)/) == -1) {
                        alert("邮政编码只能输入数字！");
                        $("#postcode").focus();
                        return false;
                    }
                    if (postcode.length != 6){
                        alert("邮政编码只能是6位数字！");
                        $("#postcode").focus();
                        return false;
                    }
                }

                return true;
            });

        });

    </script>
@stop