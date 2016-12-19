<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use app\models\DemandsInfo;
use app\models\UserInfo;
use app\models\UserPower;
use app\models\ClassInfo;

/**
 *  需求信息类
 */
class ProjectDemandsController extends Controller
{
    /**
     * 解决方案需求
     * @return mixed
     */
    public function actionIndex($id)
    {
        if (!isset($id) || empty($id)){
            echo '参数错误，请重新输入';
            die;
        }

        //科技成果分类id
        $class_id = yii::$app->params['project_id'];

        //商品信息
        $result = DemandsInfo::get_info($id);
        //最新需求列表
        $result['newest_demands'] = DemandsInfo::get_newest_demands($class_id,6,'solution');

        //发布者信息
        $result['user_info'] = UserInfo::get_user($result['corp_id']);

        //判断是否登录
        if (!\Yii::$app->user->isGuest) {
            if (!isset($_SESSION['phpCAS']['attributes']['user_type'])){
                $user_id = $_SESSION['phpCAS']['attributes']['uid'];
                $user_type = UserPower::get_user_type($user_id);
                $user_type = $_SESSION['phpCAS']['attributes']['user_type'] = $user_type;
            }
        }

        //使用会展编辑的布局页面
        $this->layout='@app/views/layouts/demandsMain.php';
        return $this->render('index',["data"=>$result]);
    }

    /**
     * 科技成果需求
     * @return mixed
     */
    public function actionTech($id)
    {
        if (!isset($id) || empty($id)){
            echo '参数错误，请重新输入';
            die;
        }

        //解决方案分类id
        $class_id = yii::$app->params['scientific_id'];

        //商品信息
        $result = DemandsInfo::get_info($id);
        if (!empty($result)){
            $class_info = ClassInfo::get_class($result['classid']);
            $result['class_info'] = $class_info;
        }

        //发布者信息
        $result['user_info'] = UserInfo::get_user($result['corp_id']);
        //最新需求列表
        $result['newest_demands'] = DemandsInfo::get_newest_demands($class_id,6);

        //判断是否登录
        if (!\Yii::$app->user->isGuest) {
            if (!isset($_SESSION['phpCAS']['attributes']['user_type'])){
                $user_id = $_SESSION['phpCAS']['attributes']['uid'];
                $user_type = UserPower::get_user_type($user_id);
                $user_type = $_SESSION['phpCAS']['attributes']['user_type'] = $user_type;
            }
        }

        //使用会展编辑的布局页面
        $this->layout='@app/views/layouts/techDemandsMain.php';
        return $this->render('tech',["data"=>$result]);
    }
}
