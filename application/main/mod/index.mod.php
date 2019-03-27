<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class IndexMod extends CBaseMod {

    public function __construct () {

    }

    /**
     * @todo    获取资讯列表
     * @author Malcolm  (2018年05月15日)
     */
    public function getIndexList(){
        $timestart = strtotime(date('Y-m-d'.'00:00:00',time()-3600*24*2)); //获取昨天00:00
        $timeend = strtotime(date('Y-m-d'.'00:00:00',time()-3600*24));//获取今天00:00

        $cond[]='role_id=2 AND mark=1';
        $ids =m('admin')->getIds($cond);
        $adminList = m('admin')->getRowByField('id', $ids);

        $array[]= $adminList['unick'];
        $data=[];
        $array=[];
        if ( is_array($ids) ) {
            foreach ( $ids as $key => $val ) {
                //查询发布用户的昵称
                $adminList = m('admin')->getRowByField('id', $val);
                $array[]= $adminList['unick'];
                //查询发布用户的记录数
                $cond = " add_user={$val} AND `add_time` BETWEEN '{$timestart}' AND '{$timeend}' AND mark=1";
                $newsData[] = m('news')->getCount($cond);
               /* $query=[
                   'fields'=>"add_user,count(id) as num",
                   'cond'=>"add_user={$val} AND `add_time` BETWEEN '{$timestart}' AND '{$timeend}' AND mark=1",
                   'group by'=>'add_user',
                   'order by'=>'add_time asc'
              ];
               $data[]=m('news')->getData($query);*/

                $data[$key] =[$array[$key],$newsData[$key]];

            }
        }

        return $data;
    }
    /**
     * @todo    获取资讯列表
     * @author Malcolm  (2018年05月15日)
     */
    public function getdataList(){
        $begin_lastmonth = strtotime(date('Y-m-1 00:00:00',strtotime('-1 month')));
        $end_lastmonth = strtotime(date("Y-m-d 23:59:59", strtotime(-date('d').'day')));
        $query = [
            'fields' => "add_time,FROM_UNIXTIME(add_time, '%Y-%m-%d') as day ",
            'cond' => "`add_time` BETWEEN {$begin_lastmonth}  AND {$end_lastmonth} AND mark=1",
            'GROUP BY' => 'FROM_UNIXTIME(add_time, \'%Y-%m-%d\') as day',
            'ORDER BY' => 'add_time asc'
        ];
         $data=[];
        $userData[] = m('user')->getData($query);

        if (is_array($userData)) {
            foreach ($userData[0] as $key => $val) {
                $data[] = $val['day'];
            }
        }

        $data=array_count_values($data);
        return $data;
    }


    /**
     * @todo    获取每个月的天数
     * @author dingj (2018年7月05日)
     */
   public function getDays( $date ,$rtype = '1')
    {
        $tem = explode('-' , $date);    //切割日期 得到年份和月份
        $year = $tem['0'];
        $month = $tem['1'];
        if( in_array($month , array( 1 , 3 , 5 , 7 , 8 ,01,03,05,07,10 , 12)))
        {
            // $text = $year.'年的'.$month.'月有31天';
            $text = '31';
        }
        elseif( $month == 2 )
        {
            if ( $year%400 == 0 || ($year%4 == 0 && $year%100 !== 0) )    //判断是否是闰年
            {
                // $text = $year.'年的'.$month.'月有29天';
                $text = '29';
            }
            else{
                // $text = $year.'年的'.$month.'月有28天';
                $text = '28';
            }
        }
        else{
            // $text = $year.'年的'.$month.'月有30天';
            $text = '30';
        }
        if ($rtype == '2') {
            for ($i = 1; $i <= $text ; $i ++ ) {
                if($i < 10){
                    $r[] = $year."-".$month."-".'0'.$i;
                }else{
                    $r[] = $year."-".$month."-".$i;
                }
            }
        } else {
            $r = $text;
        }
        return $r;

    }

}