<?php
namespace frontend\controllers;

use Yii;
use common\models\Unit;
use common\models\ZfaCorp;
use common\models\ZfaCountry;
use common\models\ZfaClassAttr;
use common\models\ZfaNicInventory;
use common\models\ZfaNicBuyDetail;
use common\models\ZfaClassFication;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\data\Pagination;
use phpCAS;
/**
 * Tech Controller
 */
class TechController extends Controller
{
    const SUBMIT_TYPE_TECH = 'tech'; // 发布科技成果
    const SUBMIT_TYPE_DEMAND = 'demand'; // 提交需求

    public $layout = "techIndexMain"; //设置使用的布局文件

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [ 'index', 'list' ,'submit', 'login'],
                        'allow' => true,
                    ],

                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * 科技成果首页
     *
     * @param int  $class_id 所属分类
     * @return mixed
     */
    public function actionIndex($class_id = null)
    {
        // 当前导航状态
        $view = Yii::$app->view;
        $view->params['current_menu'] = $class_id;
        $view->params['current_order'] = null;
        // 获取首页需求信息
        $view->params['demands'] =  $this->getDemandsOfIndex(4);
        return $this->render('index', [
            // 获取科技成果信息
            'techs' => $this->getTechsOfIndex($class_id),
        ]);

    }


    /**
     * 科技成果的需求列表.
     *
     * @return mixed
     */
    public function actionList()
    {
        $class_id = Yii::$app->request->get('class_id');
        // 当前导航状态
        $view = Yii::$app->view;
        $view->params['current_menu'] = $class_id;
        $this->layout = "techListMain";
        $view = Yii::$app->view;
        $demands = $this->getDemandsOfList($class_id,$_GET['page']);
        return $this->render('list', [
            // 获取需求信息
            'demands' => $demands,
            'pagination' => new Pagination([
                'defaultPageSize' => Yii::$app->params['listSize'],
                'totalCount' => yii::$app->session['DEMANDS_COUNT'],
            ]),
        ]);
    }

    /**
     * 系统登录处理
     *
     * @return mixed
     */
    public function actionLogin()
    {

        if (\Yii::$app->user->isGuest) {
            return Yii::$app->getResponse()->redirect( Yii::$app->params['caUrl'] . urlencode(rtrim(Yii::$app->params['frontendUrl'], '/') . Yii::$app->getRequest()->queryString));
        }
        return $this->render('index');
    }

    /**
     * 发布科技成果和需求处理
     *
     * @param string  $type 处理分类（发布方案或需求处理）
     * @return mixed
     */
    public function actionSubmit($type)
    {
        if (self::SUBMIT_TYPE_TECH == $type) {
            // 发布科技成果
            if (\Yii::$app->user->isGuest) {
                return Yii::$app->getResponse()->redirect( Yii::$app->params['caUrl'] . Yii::$app->params['submitTechUrl']);
            }
            return Yii::$app->getResponse()->redirect(Yii::$app->params['submitTechUrl']);
        } else {
            //发布需求
            if (\Yii::$app->user->isGuest) {
                return Yii::$app->getResponse()->redirect( Yii::$app->params['caUrl'] . Yii::$app->params['submitDemandUrl']);
            }
            return Yii::$app->getResponse()->redirect(Yii::$app->params['submitDemandUrl']);
        }

    }


    /**
     *  取得首页全部导航下科技成果信息
     *
     * @param int  $class_id 科技成果分类id
     * @return array 科技成果信息
     */
    static public function getTechsOfIndex($class_id = null)
    {
        // 获取科技成果数据
        $techs = ZfaNicInventory::getDataByClassid($class_id, Yii::$app->params['listSize'], 0);
        // 取得最新,最热ID
        $hotAndNewIds = ZfaNicInventory::getHotAndNewId($class_id);
        // 数据整形
        $data = Unit::formatNicInventory(ZfaNicInventory::TECH_CLASSID, $techs, Unit::INTRO_LENGTH, Unit::TITLE_LENGTH, $hotAndNewIds);

        return $data;
    }

    /**
     *  取得列表页下的需求信息
     *
     * @return array 需求信息null
     */
    private function getDemandsOfList($class_id, $current_page = 0)
    {

        // 获取科技成果需求数据
        $demands = ZfaNicBuyDetail::getDataByClassid($class_id, Yii::$app->params['listSize'], $current_page);
        // 取得最新,最热ID
        $hotAndNewIds = ZfaNicBuyDetail::getHotAndNewId($class_id);
        // 数据整形
        return Unit::formatDemands(ZfaNicInventory::TECH_CLASSID, $demands, 110, 70, $hotAndNewIds);
    }

    /**
     *  取得首页下解决方案的需求信息
     *
     * @param int $size 取得的件数
     * @return array 需求信息
     */
    private function getDemandsOfIndex($size)
    {
       // $ids = $this->getIdByCate(null);
        // 数据整形
        $demands = ZfaNicBuyDetail::getDataByClassid(null, $size, null);
        return  Unit::formatDemands(ZfaNicInventory::TECH_CLASSID, $demands, 30, Unit::TITLE_LENGTH);
    }

    /**
     *  取得首页下科技成果的分类信息
     *
     * @param int $size 取得的件数
     * @return array 需求信息
     */
    public static function getIdByCate($cate_name)
    {
        if (is_null($cate_name) || trim($cate_name) == '') {
            //取得科技成果的所有子分类的class id
            return ZfaClassFication::find()
                ->select(['id'])
                ->where(['=', 'parent_id', ZfaNicInventory::TECH_CLASSID])
                ->asArray()
                ->all();
        } else {
            //根据科技成果的子分类名称取得所对应的class id
            return ZfaClassFication::find()
                ->select(['id'])
                ->where(['=', 'name', $cate_name])
                ->asArray()
                ->all();
        }
    }
}
