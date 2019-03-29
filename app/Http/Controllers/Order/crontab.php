<?php
    header("content-type:text/html;charset=utf-8");
    $con=mysqli_connect('192.168.31.1','root','root','group_shop');
    $sql="select * from group_shop where order_status=2 && c_time+3600<time()";
    $res=mysqli_query($con,$sql);
    $arr=mysqli_fetch_all();
    print_r($arr);
//echo 2;
//$time=time()-3600;
//$res=DB::table('g_order')->where('c_time','<',$time)->where(['order_status'=>0])->get()->toArray();
//echo 1;
//foreach($res as $k=>$v){
//    $order_id=$v['order_id'];
//    OrderModel::where(['order_id'=>$order_id])->delete();
//}
//echo $time;

