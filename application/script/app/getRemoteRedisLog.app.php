<?php


/**
 *  从远程服务器获取redis的日志队列存入本地
 * Created by Malcolm.
 * Date: 2018/5/18  18:15
 */
class GetRemoteRedisLogApp extends BaseScriptApp {

    function index() {
        //实例化redis
        $redis = new Redis();

        do {
            $redis->connect('r-uf6eb7d724e8d6e4.redis.rds.aliyuncs.com', 6379);

            $redis->auth('Jyb12341234');

            $link = $redis->select(1);

            if (!$link)
                echo "Content1 false \n";


            echo "Content1 True \n";

            $rs = $redis->rPop("njyb_log");

            if (!$rs) {
                echo "Not found \n";

                usleep(500000);
                continue;
            }

            echo "Fetch data \n";

            $redis->connect('127.0.0.1', 6379);

            $local = $redis->select(1);
            if (!$local)
                echo "Content2 false \n";


            echo "Content2  True \n";


            $rss = $redis->lPush('njyb_log' , $rs);

            echo "{$rss} \n";

        }while (true);


    }


}