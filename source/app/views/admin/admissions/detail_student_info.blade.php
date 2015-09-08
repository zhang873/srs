@extends('admin.layouts.frame_modal')

{{-- Web site Title --}}
@section('title')
	detail:: @parent
@stop

{{-- Content --}}
@section('content')
    <div style="display: none;">
    <table id="tbNone">
        <thead>
            <tr>
                <th></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    </div>
    <table id="admissions"  class="table table-striped table-hover table-bordered" align="center" width="800px">
        <tr>
            <td colspan="6" align="center"><h3>{{{ Lang::get('admin/admissions/title.admission_info') }}}</h3></td>
        </tr>
        <tr>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.gender') }}}</th>
            <td class="col-md-1">@if ($info->gender=='f')女@elseif ($info->gender=='m')男@endif</td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.nationgroup') }}}</th>
            <td class="col-md-03"><?php
            switch ($info->nationgroup){
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
            }?></td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.jiGuan') }}}</th>
            <td class="col-md-03">{{$info->jiguan}}</td>
        </tr>
        <tr>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.dateofbirth') }}}</th>
            <td class="col-md-1">{{$info->dateofbirth}}</td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.source_classification') }}}</th>
            <td class="col-md-03"><?php
            switch ($info->formerlevel) {
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
            }?></td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.politicalStatus') }}}</th>
            <td class="col-md-03"><?php
            switch ($info->politicalstatus) {
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
            }?></td>
        </tr>
        <tr>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.maritalStatus') }}}</th>
            <td class="col-md-01"><?php
            switch ($info->maritalstatus) {
                case 0 :
                    echo '未婚';
                    break;
                case 1 :
                    echo '已婚';
                    break;
                case 2 :
                    echo '其他';
                    break;
            }?></td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.mobile') }}}</th>
            <td class="col-md-03">{{$info->mobile}}</td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.email') }}}</th>
            <td class="col-md-03">{{$info->email}}</td>
        </tr>
        <tr>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.major') }}}</th>
            <td class="col-md-1">{{$info->rname}}</td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.major_classification') }}}</th>
            <td class="col-md-03"><?php
            switch ($info->program) {
                case 12 :
                    echo '本科';
                    break;
                case 14 :
                    echo '专科';
                    break;
            }?></td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.postcode') }}}</th>
            <td class="col-md-03">{{$info->postcode}}</td>
        </tr>
        <tr>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.class_id') }}}</th>
            <td class="col-md-1">{{$info->sysid}}</td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.group_name') }}}</th>
            <td class="col-md-03">{{$info->gname}}</td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.teaching_plan_ID') }}}</th>

            <td class="col-md-03"><a href="{{{ URL::to('admin/admissions/score_record?admission_id='.$info->aid.'&plan_code='.$info->code) }}}"
                id="popFrame">{{$info->code}}</a></td>
        </tr>
        <tr>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.admissionyear') }}}</th>
            <td class="col-md-1">{{$info->admissionyear}}</td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.admissionsemester') }}}</th>
            <td class="col-md-03"><?php
            switch ($info->admissionsemester) {
                case 1 :
                    echo '春季';
                    break;
                case 2 :
                    echo '秋季';
                    break;
            }?></td>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.admission_state') }}}</th>
            <td class="col-md-03"><?php
            switch ($info->status) {
                case 0 :
                    echo '已录入数据';
                    break;
                case 1 :
                    echo '已上报省校';
                    break;
                case 2 :
                    echo '省校已审批';
                    break;
                case 3 :
                    echo '未注册';
                    break;
                case 4 :
                    echo '在籍';
                    break;
                case 5 :
                    echo '异动中';
                    break;
                case 6 :
                    echo '毕业';
                    break;
                case 7 :
                    echo '退学';
                    break;
            }?></td>
        </tr>
        <tr>
            <th class="col-md-03">{{{ Lang::get('admin/admissions/table.address') }}}</th>
            <td colspan="6" class="col-md-1">{{$info->address}}</td>
        </tr>
    </table>
    <div class="form-group" align="center">
        <label id="lbl"></label>
    </div>

@stop

@section('styles')

<style>
    div.DTTT .btn {
        color:#ffffff !important;
        font-size: 14px;
        margin-left: 20px;
    }
    div.DTTT{
        margin-left: .3em;
    }
    .rlbl{
        text-align:right;
        width:100px;
    }
    .twidth{
        width:150px;
    }
    .col-md-01 {
        width: 10%;
    }
    .col-md-02 {
        width: 8%;
    }
    .col-md-03 {
        width: 6%;
    }
</style>
@stop



{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
        //$("#popFrame").colorbox({iframe:true, width:"80%", height:"100%"});
        var oTable = $('#tbNone').dataTable();
        var tableTools = new $.fn.dataTable.TableTools( oTable, {
            "sSwfPath": "{{asset('assets/swf/copy_csv_xls_pdf.swf')}}",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sButtonText": "{{{ Lang::get('admin/admissions/title.export_excel') }}}",
                    "sButtonClass":"btn btn-small btn-info",
                    "sFileName": "学籍情况.xls",
                    "mColumns": "visible"
                    
                }
            ]
        } );
        $( tableTools.fnContainer() ).insertAfter('#lbl');
        $(document).ready(function() {

        });

	</script>
@stop