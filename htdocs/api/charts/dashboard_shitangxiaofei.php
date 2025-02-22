<?php
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$optionsMenuItem = $_GET['optionsMenuItem'];
if($optionsMenuItem=="")  {
    $optionsMenuItem = "最近一月";
}

$学期       = getCurrentXueQi();

$USER_ID    = ForSqlInjection($GLOBAL_USER->USER_ID);

$sql        = "select * from data_deyu_geren_gradeone";
$rs         = $db->Execute($sql);
$rs_a       = $rs->GetArray();
$图标和颜色 = [];
$图标和颜色['收银']     = ['颜色'=> 'warning', '图标'=> 'trending-up'];
$图标和颜色['收银退款'] = ['颜色'=> 'success', '图标'=> 'account-star'];
$图标和颜色['在线充值'] = ['颜色'=> 'error', '图标'=> 'run-fast'];
//$图标和颜色['在线充值'] = ['颜色'=> 'info', '图标'=> 'drawing-box'];
//$图标和颜色['在线充值'] = ['颜色'=> 'primary', '图标'=> 'worker'];

switch($optionsMenuItem) {
    case '最近一周':
        $whereSql = " and 支付日期 >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
        break;
    case '最近一月':
        $whereSql = " and 支付日期 >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case '最近三月':
        $whereSql = " and 支付日期 >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
        break;
    case '最近半年':
        $whereSql = " and 支付日期 >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
        break;
    case '最近一年':
        $whereSql = " and 支付日期 >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        break;
}

//奖杯模块
$sql = "select SUM(订单金额) AS NUM from data_shitangxiaofei where 1=1 $whereSql";
$rs = $db->Execute($sql);
$AnalyticsTrophy['Welcome']     = "您好,".$GLOBAL_USER->USER_NAME."!🥳";
$AnalyticsTrophy['SubTitle']    = $班级." - 食堂消费总金额";
$AnalyticsTrophy['TotalScore']  = $rs->fields['NUM'];
$AnalyticsTrophy['ViewButton']['name']  = "查看明细";
$AnalyticsTrophy['ViewButton']['url']   = "/tab/apps_180";
$AnalyticsTrophy['TopRightOptions']     = [];
$AnalyticsTrophy['grid']        = 4;
$AnalyticsTrophy['type']        = "AnalyticsTrophy";
$AnalyticsTrophy['sql']         = $sql;

//按一级指标统计积分
$sql = "select 订单类型 AS title, SUM(订单金额) AS NUM from data_shitangxiaofei where 1=1 $whereSql group by 订单类型 order by 订单类型 asc";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
$Item = [];
$data = [];
$Index = 0;
foreach($rs_a as $Element)   {
    $data[] = ['title'=>$Element['title'],'stats'=>$Element['NUM'],'color'=>$图标和颜色[$Element['title']]['颜色'],'icon'=>"mdi:".$图标和颜色[$Element['title']]['图标']];
    $Index ++;
}
$AnalyticsTransactionsCard['Title']       = "食堂消费";
$AnalyticsTransactionsCard['SubTitle']    = "按类别统计总金额";
$AnalyticsTransactionsCard['data']        = $data;
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$AnalyticsTransactionsCard['grid']                 = 8;
$AnalyticsTransactionsCard['type']                 = "AnalyticsTransactionsCard";
$AnalyticsTransactionsCard['sql']                  = $sql;


//得到最新加分或是扣分的几条记录
$sql = "select 订单类型, 订单金额, 人员编号, 人员姓名, 部门名称 from data_shitangxiaofei where 1=1 $whereSql and 订单类型='收银' order by id desc limit 5";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
for($i=0;$i<sizeof($rs_a);$i++) {
    $rs_a[$i]['项目图标'] = "mdi:".$图标和颜色[$rs_a[$i]['订单类型']]['图标'];
    $rs_a[$i]['图标颜色'] = $图标和颜色[$rs_a[$i]['订单类型']]['颜色'];
}
$AnalyticsDepositWithdraw['加分']['Title']             = "加分";
$AnalyticsDepositWithdraw['加分']['TopRightButton']    = ['name'=>'查看所有','url'=>'/tab/apps_180'];
$AnalyticsDepositWithdraw['加分']['data']              = $rs_a;

$sql = "select 订单类型, 订单金额, 人员编号, 人员姓名, 部门名称 from data_shitangxiaofei where 1=1 $whereSql and 订单类型='在线充值' order by id desc limit 5";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
for($i=0;$i<sizeof($rs_a);$i++) {
    $rs_a[$i]['项目图标'] = "mdi:".$图标和颜色[$rs_a[$i]['订单类型']]['图标'];
    $rs_a[$i]['图标颜色'] = $图标和颜色[$rs_a[$i]['订单类型']]['颜色'];
}
$AnalyticsDepositWithdraw['扣分']['Title']              = "扣分";
$AnalyticsDepositWithdraw['扣分']['TopRightButton']     = ['name'=>'查看所有','url'=>'/tab/apps_180'];
$AnalyticsDepositWithdraw['扣分']['data']               = $rs_a;
$AnalyticsDepositWithdraw['grid']                       = 8;
$AnalyticsDepositWithdraw['type']                       = "AnalyticsDepositWithdraw";
$AnalyticsDepositWithdraw['sql']                        = $sql;



//本班积分排行
$colorArray = ['primary','success','warning','info','info'];
$iconArray  = ['mdi:trending-up','mdi:account-outline','mdi:cellphone-link','mdi:currency-usd','mdi:currency-usd','mdi:currency-usd'];
$sql    = "select 设备名称, SUM(订单金额) AS 订单金额 from data_shitangxiaofei where 1=1 $whereSql group by 设备名称 order by 设备名称 desc";
$rs     = $db->Execute($sql);
$rs_a   = $rs->GetArray();
$Item   = [];
$Index  = 0;
for($i=0;$i<sizeof($rs_a);$i++) {
    $rs_a[$i]['图标颜色']   = $colorArray[$i];
    $rs_a[$i]['头像']       = '/images/avatars/'.(($i)+1).'.png';
}
$AnalyticsSalesByCountries['Title']       = "设备终端";
$AnalyticsSalesByCountries['SubTitle']    = "按设备终端统计消费总金额";
$AnalyticsSalesByCountries['data']        = $rs_a;
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$AnalyticsSalesByCountries['grid']                 = 4;
$AnalyticsSalesByCountries['type']                 = "AnalyticsSalesByCountries";
$AnalyticsSalesByCountries['sql']                  = $sql;


print_R($AnalyticsSalesByCountries);exit;

/*
//ApexAreaChart
$sql = "select 订单类型,支付日期,sum(订单金额) AS NUM from data_shitangxiaofei where 1=1 $whereSql group by 订单类型,支付日期 order by 支付日期 asc";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
$一级指标Array = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['支付日期']][$rs_a[$i]['订单类型']] = $rs_a[$i]['NUM'];
    $一级指标Array[$rs_a[$i]['订单类型']] = $rs_a[$i]['订单类型'];
}
$dataY = [];
$dataX = array_keys($输出数据);
$一级指标Array = array_keys($一级指标Array);
foreach($一级指标Array as $订单类型)  {
    $ItemY = [];
    $ItemYDate = [];
    foreach($dataX as $Date) {
        $ItemYDate[] = intval($输出数据[$Date][$订单类型]);
    }
    $dataY[] = ["name"=>$订单类型,"data"=>$ItemYDate];
}

$ApexAreaChart['Title']       = "班级学生积分之和";
$ApexAreaChart['SubTitle']    = "按天统计班级学生积分之和";
$ApexAreaChart['dataX']       = $dataX;
$ApexAreaChart['dataY']       = $dataY;
$ApexAreaChart['sql']       = $sql;
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
*/

//ApexAreaChart
$sql = "select 支付日期,sum(订单金额) AS NUM from data_shitangxiaofei where 1=1 $whereSql group by 支付日期 order by 支付日期 asc";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['支付日期']] = $rs_a[$i]['NUM'];
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"班级总积分","data"=>array_values($输出数据)];

$ApexAreaChart['Title']       = "班级学生积分之和";
$ApexAreaChart['SubTitle']    = "按天统计班级学生积分之和";
$ApexAreaChart['dataX']       = $dataX;
$ApexAreaChart['dataY']       = $dataY;
$ApexAreaChart['sql']       = $sql;
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexAreaChart['grid']                  = 8;
$ApexAreaChart['type']                  = "ApexAreaChart";
$ApexAreaChart['sql']                   = $sql;


$ApexLineChart['Title']         = "班级学生积分之和";
$ApexLineChart['SubTitle']      = "按天统计班级学生积分之和";
$ApexLineChart['dataX']         = $dataX;
$ApexLineChart['dataY']         = $dataY;
$ApexLineChart['sql']           = $sql;
$ApexLineChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexLineChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexLineChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexLineChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexLineChart['grid']                  = 8;
$ApexLineChart['type']                  = "ApexLineChart";

//输出GoView结构
$ApexLineChart['GoView']['dimensions']      = ["支付日期",$ApexLineChart['Title']];
$GoViewSource = [];
foreach($输出数据 as $输出数据X=>$输出数据Y)  {
    $GoViewSource[] = [$ApexLineChart['Title']=>$输出数据Y,'支付日期'=>$输出数据X];
}
$ApexLineChart['GoView']['source']    = $GoViewSource;

//额外一个班级的统计数据 -- 开始
$额外一个班级的统计数据 = $班级名称Array[1];
$sql = "select 支付日期,sum(订单金额) AS NUM from data_shitangxiaofei where 班级='$额外一个班级的统计数据' $whereSql group by 支付日期 order by 支付日期 asc";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
$输出数据T = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据T[$rs_a[$i]['支付日期']] = $rs_a[$i]['NUM'];
}
$dataY = [];
$dataX = array_keys($输出数据T);
$dataY[] = ["name"=>"班级总积分","data"=>array_values($输出数据T)];
//输出GoView结构
$ApexLineChart['GoView2']['dimensions']      = ["支付日期",$班级,$额外一个班级的统计数据];
$GoViewSource = [];
foreach($输出数据T as $输出数据X=>$输出数据Y)  {
    $GoViewSource[] = [$班级=>$输出数据Y, '支付日期'=>$输出数据X, $额外一个班级的统计数据=>rand(1,20)];
}
$ApexLineChart['GoView2']['source']    = $GoViewSource;
//额外一个班级的统计数据 -- 结束


//AnalyticsWeeklyOverview
$sql = "select 支付日期,sum(订单金额) AS NUM from data_shitangxiaofei where 1=1 $whereSql group by 支付日期 order by 支付日期 desc limit 7";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['支付日期']] = $rs_a[$i]['NUM'];
}
ksort($输出数据);
$dataY = [];
$dataX = array_keys($输出数据);
$dataYItem = array_values($输出数据);
$dataY[] = ["name"=>"班级总积分","data"=>$dataYItem];

$AnalyticsWeeklyOverview['Title']         = "班级学生积分周报";
$AnalyticsWeeklyOverview['SubTitle']      = "最近一周班级学生积分之和";
$AnalyticsWeeklyOverview['dataX']         = $dataX;
$AnalyticsWeeklyOverview['dataY']         = $dataY;
$AnalyticsWeeklyOverview['sql']           = $sql;
$AnalyticsWeeklyOverview['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$AnalyticsWeeklyOverview['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$AnalyticsWeeklyOverview['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$AnalyticsWeeklyOverview['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];

$AnalyticsWeeklyOverview['BottomText']['Left']      = array_sum($dataYItem);
$AnalyticsWeeklyOverview['BottomText']['Right']     = "最近一周总积分为".array_sum($dataYItem).", 比上周增加13%";

$AnalyticsWeeklyOverview['ViewButton']['name']  = "明细";
$AnalyticsWeeklyOverview['ViewButton']['url']   = "/tab/apps_180";
$AnalyticsWeeklyOverview['grid']                = 4;
$AnalyticsWeeklyOverview['type']                = "AnalyticsWeeklyOverview";
$AnalyticsWeeklyOverview['sql']                 = $sql;



//AnalyticsPerformance
$sql = "select 订单类型,sum(订单金额) AS NUM from data_shitangxiaofei where 1=1 $whereSql group by 订单类型 order by 订单类型 asc";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['订单类型']] = $rs_a[$i]['NUM'];
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"班级总积分","data"=>array_values($输出数据)];

$AnalyticsPerformance['Title']       = "按一级指标统计积分之和";
$AnalyticsPerformance['SubTitle']    = "按一级指标统计班级学生积分之和";
$AnalyticsPerformance['dataX']       = $dataX;
$AnalyticsPerformance['dataY']       = $dataY;
$AnalyticsPerformance['sql']         = $sql;
$AnalyticsPerformance['colors']      = ['#fdd835','#32baff','#00d4bd','#7367f0','#FFA1A1'];
$AnalyticsPerformance['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$AnalyticsPerformance['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$AnalyticsPerformance['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$AnalyticsPerformance['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$AnalyticsPerformance['grid']                 = 4;
$AnalyticsPerformance['type']                 = "AnalyticsPerformance";
$AnalyticsPerformance['sql']                  = $sql;



//ApexDonutChart
$sql = "select 订单类型,sum(订单金额) AS NUM from data_shitangxiaofei where 1=1 $whereSql group by 订单类型 order by 订单类型 asc";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['订单类型']] = intval($rs_a[$i]['NUM']);
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"班级总积分百分比","data"=>array_values($输出数据)];

$ApexDonutChart['Title']       = "按一级指标统计百分比";
$ApexDonutChart['SubTitle']    = "按一级指标统计加分之和的百分比";
$ApexDonutChart['dataX']       = $dataX;
$ApexDonutChart['dataY']       = $dataY;
$ApexDonutChart['sql']         = $sql;
$ApexDonutChart['colors']      = ['#fdd835','#32baff','#00d4bd','#7367f0','#FFA1A1'];
$ApexDonutChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexDonutChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexDonutChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexDonutChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexDonutChart['grid']                 = 4;
$ApexDonutChart['type']                 = "ApexDonutChart";
$ApexDonutChart['sql']                  = $sql;



//ApexRadialBarChart
$sql = "select 订单类型,sum(订单金额) AS NUM from data_shitangxiaofei where 1=1 $whereSql group by 订单类型 order by 订单类型 asc limit 5";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['订单类型']] = intval($rs_a[$i]['NUM']);
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"班级总积分百分比","data"=>array_values($输出数据)];

$ApexRadialBarChart['Title']       = "按一级指标统计百分比";
$ApexRadialBarChart['SubTitle']    = "按一级指标统计加分之和的百分比";
$ApexRadialBarChart['dataX']       = $dataX;
$ApexRadialBarChart['dataY']       = $dataY;
$ApexRadialBarChart['sql']         = $sql;
$ApexRadialBarChart['colors']      = ['#fdd835','#32baff','#00d4bd','#7367f0','#FFA1A1'];
$ApexRadialBarChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexRadialBarChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexRadialBarChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexRadialBarChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexRadialBarChart['grid']                 = 4;
$ApexRadialBarChart['type']                 = "ApexRadialBarChart";
$ApexRadialBarChart['sql']                = $sql;



$RS                             = [];
$RS['defaultValue']             = $班级;
$RS['optionsMenuItem']          = $optionsMenuItem;

$RS['charts'][]       = $AnalyticsTrophy;
$RS['charts'][]       = $AnalyticsTransactionsCard;
$RS['charts'][]       = $AnalyticsSalesByCountries;
$RS['charts'][]       = $AnalyticsDepositWithdraw;
$RS['charts'][]       = $AnalyticsWeeklyOverview;
//$RS['charts'][]       = $ApexAreaChart;
$RS['charts'][]       = $ApexLineChart;
$RS['charts'][]       = $AnalyticsPerformance;
$RS['charts'][]       = $ApexDonutChart;
$RS['charts'][]       = $ApexRadialBarChart;


print_R(json_encode($RS));



?>
