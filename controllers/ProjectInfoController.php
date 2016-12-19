<?php
namespace frontend\controllers;

use app\models\Broker;
use Yii;
use yii\web\Controller;
use app\models\GoodsInfo;
use app\models\UserInfo;
use app\models\MemberInfo;
use app\models\ClassInfo;
use common\models\Unit;

/**
 *  方案、成果类
 */
class ProjectInfoController extends Controller
{
    /**
     * 解决方案
     * @return mixed
     */
    public function actionIndex($id)
    {
        if (!isset($id) || empty($id)){
            echo '参数错误，请重新输入';
            die;
        }

        //使用会展编辑的布局页面
        $this->layout='@app/views/layouts/detailInfoMain.php';
        //商品信息
        $result = GoodsInfo::get_info($id);
        $result['distance_time'] = array();
        $result['related_tech'] = array();

        //发布时间格式化
        if (!empty($result)){
            foreach ($result['ext_attr'] as $value) {
                if ($value[0] == '所属领域'){
                    $result['domains'] = $value[1];
                    break;
                }
            }

            $result['distance_time'] = Unit::timediff(floor($result['update_time']/1000),time());
            //相关科技成果
            $result['related_tech'] = GoodsInfo::getRelatedSolution($result['domains'],'5');
        }

        //发布者信息
        $result['user_info'] = UserInfo::get_user($result['corp_id']);

        return $this->render('index',["data"=>$result]);
    }

    public function actionLogin(){
        //如果访问的地址没有来源地址跳转到首页
        $return_url = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : Yii::$app->params['website'].Url::to(['solution/index']);
        return Yii::$app->getResponse()->redirect( Yii::$app->params['caUrl'] . urlencode($return_url));
    }

    /**
     * 科技成果
     * @return mixed
     */
    public function actionTech($id)
    {
        if (!isset($id) || empty($id)) {
            echo '参数错误，请重新输入';
            die;
        }

        //使用会展编辑的布局页面
        $this->layout = '@app/views/layouts/techInfoMain.php';
        //商品信息
        $result = GoodsInfo::get_info($id);

        $result['class_info'] = array();
        $result['related_tech'] = array();
        $result['user_info'] = array();
        $result['member_info'] = array();
        $result['distance_time'] = array();

        if (!empty($result)){
            $class_info = ClassInfo::get_class($result['classid']);
            $result['class_info'] = $class_info;
            //相关科技成果
            $result['related_tech'] = GoodsInfo::getRelatedByClass($result['classid'],'5');

            //发布者信息
            $result['user_info'] = UserInfo::get_user($result['corp_id']);
            //当前会员信息
            $result['member_info'] = array();
            if (!\Yii::$app->user->isGuest) {
                $member_id = $_SESSION['phpCAS']['attributes']['uid'];
                $result['member_info'] = MemberInfo::get_user($member_id);
            }
            //发布时间格式化
            $result['distance_time'] = Unit::timediff(floor($result['update_time']/1000),time());
        }

        return $this->render('tech', ["data" => $result]);
    }

    /**
     * CAS登录跳转
     */

    public function actionSubmit(){
        //如果访问的地址没有来源地址跳转到首页
        $callback_url = !empty($_SERVER['REQUEST_URI']) ? Yii::$app->params['website'].$_SERVER['REQUEST_URI'] : Yii::$app->params['website'].Url::to(['solution/index']);

        // 发布方案
        if (\Yii::$app->user->isGuest) {
            return Yii::$app->getResponse()->redirect( Yii::$app->params['caUrl'] . urlencode(Yii::$app->params['submitSolutionUrl']));
        } else {
            return Yii::$app->getResponse()->redirect(urlencode(Yii::$app->params['submitSolutionUrl']));
        }
    }

    /**
     * 页面错误信息
     * @return string
     */
    public function actionError(){
        //使用会展编辑的布局页面
        $this->layout = '@app/views/layouts/techInfoMain.php';
        //返回地址
        $return_url = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

        return $this->render('server_error', ['exception' => \Yii::$app->exception,'callback_url'=>$return_url]);
    }

    /**
     * 交易方式
     */
    public function actionDealWay(){
        //科技成果id
        $id = empty($_REQUEST['id']) ? '' : $_REQUEST['id'];

        //判断所传参数
        if (empty($id)){
            echo self::show_ajax('',500,'参数错误，请重新提交');
            die;
        }

        //方案信息
        $data = GoodsInfo::deal_way($id);
        echo self::show_ajax($data,200);
    }

    /**
     * 根据交易方式查询交易价格
     */
    public function actionDealPrice(){
        //科技成果id
        $id = empty($_REQUEST['id']) ? '' : $_REQUEST['id'];

        //判断所传参数
        if (empty($id)){
            echo self::show_ajax('',500,'参数错误，请重新提交');
            die;
        }

        $data = GoodsInfo::deal_price($id);
        echo self::show_ajax($data,200);
    }

    /**
     * 委托信息存储
     */
    public function actionConsignorHandle(){
        //科技成果id
        $data['description'] = empty($_REQUEST['description']) ? '' : $_REQUEST['description'];
        $data['user_name'] = empty($_REQUEST['user_name']) ? '' : $_REQUEST['user_name'];
        $data['user_phone'] = empty($_REQUEST['user_phone']) ? '' : $_REQUEST['user_phone'];
        $data['budget_price'] = empty($_REQUEST['budget_price']) ? '' : $_REQUEST['budget_price'];
        $data['corp_id'] = empty($_REQUEST['corp_id']) ? '' : $_REQUEST['corp_id'];
        $data['product_id'] = empty($_REQUEST['product_id']) ? '' : $_REQUEST['product_id'];

        $empty_notice_array = array(
            'description'=>'委托说明',
            'user_name'=>'委托人',
            'user_phone'=>'手机号',
            'budget_price'=>'预算资金',
        );

        foreach ($data as $key=>$val){
            if (empty($val)){
                $field = $empty_notice_array[$key];
                echo self::show_ajax($data,500,$field.'不能为空');
                die;
            }
        }

        $data = Broker::entrust_handle($data);
        echo self::show_ajax($data,200,'委托已成功提交，我们会尽快与您联系。');
    }

    /**
     * @param int $code 状态码，默认200
     * @param array $data 返回数据
     * @param string $error 信息
     * @return json 数组编码后的json
     */
    public static function show_ajax($data=array(),$code=200,$info=""){
        if ($code == 200){
            $status = true;
        } else {
            $status = false;
        }

        $array = array(
            'code'=>$code,
            'success'=>$status,
            'data'=>$data,
            'info'=>$info
        );

        return json_encode($array);
    }
}
