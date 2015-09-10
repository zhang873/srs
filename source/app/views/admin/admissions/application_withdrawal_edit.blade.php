@extends('admin.layouts.default')
{{-- Content --}}
@section('content')
    <table id="admissions"  class="table table-striped table-hover table-bordered" align="center" width="800px">
        <tr>
            <td colspan="4" align="center"><h3>考生基本资料</h3></td>
        </tr>
        @foreach($admissions as $admission)
            <tr>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.student_name') }}}</th>
                <td style="width: 150px;">{{$admission->student_name}}</td>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.gender') }}}</th>
                <td style="width: 120px;"> @if ($admission->gender=='f')
                        女
                    @elseif ($admission->gender=='m')
                        男
                    @endif
                </td>
            </tr>
            <tr>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.dateofbirth') }}}</th>
                <td style="width: 150px;">{{$admission->dateofbirth}}</td>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.nationgroup') }}}</th>
                <td style="width: 150px;">
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
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.politicalStatus') }}}</th>
                <td style="width: 150px;"> <?php
                    switch ($admission->politicalstatus) {
                        case 1:
                            echo '中共党员';
                            break;
                        case 2:
                            echo '共青团员';
                            break;
                        case 3:
                            echo '民革会员';
                            break;
                        case 4:
                            echo '民盟盟员';
                            break;
                        case 5:
                            echo '民进会员';
                            break;
                        case 6:
                            echo '民建会员';
                            break;
                        case 7:
                            echo '农工党党员';
                            break;
                        case 8:
                            echo '致公党党员';
                            break;
                        case 9:
                            echo '九三学社社员';
                            break;
                        case 10:
                            echo '台盟盟员';
                            break;
                        case 11:
                            echo '无党派民主人士';
                            break;
                        case 12:
                            echo '群众';
                            break;
                        case 13:
                            echo '其他';
                            break;
                    }
                    ?>
                </td>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.source_classification') }}}</th>
                <td style="width: 150px;">
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
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.certification_type') }}}</th>
                <td style="width: 150px;">
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

                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.certification_code') }}}</th>
                <td style="width: 150px;">{{$admission->idnumber}}</td>

            </tr>
            <tr>
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.hukou') }}}</th>
                <td style="width: 150px;">
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
                <th class="col-md-1" style="width: 150px;">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
                <td style="width: 150px;">
                    @if ($admission->program == 12)
                        本科
                    @elseif ($admission->program == 14)
                        专科
                    @endif
                </td>
            </tr>
            <tr>
                <th class="col-md-1" style="width: 150px;">报考专业</th>
                <td style="width: 150px;">{{$admission->program}}</td>
                <th class="col-md-1" style="width: 150px;">报考学校</th>
                <td style="width: 150px;">{{$admission->campus_name}}</td>
            </tr>
            <tr>
                <td style="width: 150px;">申请年度</td>
                <td>
                    <select name="year" id="year" style="width:150px;">
                        <option value="">请选择</option>
                        @for ($i=2000;$i<2025;$i++)
                            <option value="{{{$i}}}">{{$i}}</option>
                        @endfor
                    </select>
                </td>
                <th class="col-md-1" style="width: 150px;">申请学期</th>
                <td style="width: 150px;">
                    <select name="semester" id="semester" style="width:150px;">
                        <option value="">请选择</option>
                        <option value="0" @if (Config::get('customsettings.admissionsemester')==0) selected="selected" @endif >秋季</option>
                        <option value="1" @if (Config::get('customsettings.admissionsemester')==1) selected="selected" @endif>春季</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th class="col-md-1" style="width: 150px;">退学原因（必填）</th>
                <td style="width: 150px;"><input id="withdrawal_cause" name="withdrawal_cause" type="text"></td>
                <th class="col-md-1" style="width: 150px;"></th>
                <td style="width: 150px;"></td>
            </tr>
        @endforeach
    </table>
    <div align="center">
        <!-- Form Actions -->
        <button type="submit" class="btn btn-default" value="2" id="btnSubmit">{{{ Lang::get('button.submit') }}}申请</button>
        <!-- ./ form actions -->
    </div>
    <div>
        <input type="hidden" id="btnValue" value="2">
    </div>
@stop

@section('styles')

    <style>
        .width120{
            text-align:right;
            width:120px;

        }
        .width150{
            text-align:left;
            width:100px;
        }
    </style>
@stop
