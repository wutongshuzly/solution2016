<?php
namespace frontend\controllers;

use app\models\UserPower;
use Yii;
use common\models\Unit;
use common\models\ZfaCorp;
use common\models\ZfaCountry;
use common\models\ZfaClassAttr;
use common\models\ZfaNicInventory;
use common\models\ZfaNicBuyDetail;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use phpCAS;
use yii\data\Pagination;

/**
 * Solution controller
 */
class SolutionController extends Controller
{
    const SUBMIT_TYPE_SOLUTION = 'solution'; // 发布方案
    const SUBMIT_TYPE_DEMAND = 'demand'; // 提交需求

    public $layout = "solutionIndexMain"; //设置使用的布局文件

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
                        'actions' => ['logout', 'login', 'index', 'logout', 'list' ,'submit'],
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
     * 解决方案首页及列表页(智慧城市 智能家居 智能安防)
     *
     * @param string  $type 所属领域
     * @return mixed
     */
    public function actionIndex($type = null)
    {
        // 当前导航状态
        $view = Yii::$app->view;
        $view->params['current_menu'] = $type;
        // 获取首页需求信息
        $view->params['demands'] =  $this->getDemandsOfIndex(4);
        return $this->render('index', [
            // 获取解决方案信息
            'solutions' => $this->getSolutionsOfIndex(),
            'recommend_solutions' => $this->getRecommendSolutions(),
            'hot' =>  Yii::$app->session['HOT'],
            'new' =>  Yii::$app->session['NEW'],
        ]);

    }


    /**
     * 解决方案的需求列表.
     *
     * @return mixed
     */
    public function actionList()
    {
        $this->layout = "solutionListMain";
        $view = Yii::$app->view;
        $demands = $this->getDemandsOfList($_GET['page']);
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
     * 发布方案和需求处理
     *
     * @param string  $type 处理分类（发布方案或需求处理）
     * @return mixed
     */
    public function actionSubmit($type)
    {
        if (self::SUBMIT_TYPE_SOLUTION == $type) {
            // 发布方案
            if (\Yii::$app->user->isGuest) {
                return Yii::$app->getResponse()->redirect( Yii::$app->params['caUrl'] . Yii::$app->params['submitSolutionUrl']);
            }
            return Yii::$app->getResponse()->redirect(Yii::$app->params['submitSolutionUrl']);
        } else {
            //发布需求
            if (\Yii::$app->user->isGuest) {
                return Yii::$app->getResponse()->redirect( Yii::$app->params['caUrl'] . Yii::$app->params['submitDemandUrl']);
            }
            return Yii::$app->getResponse()->redirect(Yii::$app->params['submitDemandUrl']);
        }

    }

    /**
     * 系统登出处理
     *
     * @return mixed
     */
    public function actionLogout()
    {

        Yii::$app->user->logout();
        return Yii::$app->getResponse()->redirect(Yii::$app->params['caLogoutUrl'] . urlencode(Yii::$app->request->getReferrer()));
    }
    /**
     * 系统登录处理
     *
     * @return mixed
     */
    public function actionLogin()
    {

        if (\Yii::$app->user->isGuest) {
            return Yii::$app->getResponse()->redirect( Yii::$app->params['caUrl'] . urlencode(rtrim(Yii::$app->params['frontendUrl'], '/') . Url::to(['solution/index'])));
        }
        return $this->render('index');
    }

    /**
     *  取得首页全部导航下解决方案信息
     *
     * @return array 解决方案信息
     */
    function getSolutionsOfIndex()
    {
        // 获取解决方案数据
        $solutions = ZfaNicInventory::getDataByClassid(ZfaNicInventory::SOLUTION_CLASSID, Yii::$app->params['listSize']);
        // 取得最新,最热ID
        $hotAndNewIds = ZfaNicInventory::getHotAndNewId(ZfaNicInventory::SOLUTION_CLASSID);
        // 数据整形
        $data = Unit::formatNicInventory(ZfaNicInventory::SOLUTION_CLASSID, $solutions, Unit::INTRO_LENGTH, Unit::TITLE_LENGTH, $hotAndNewIds);
        //将首页全部解决方案的所属领域进行整理
        $data = $this->setDomain($data);
        return $data;
    }

    /**
     *  将首页全部解决方案的所属领域进行整理
     *
     * @return array 解决方案信息
     */
     function setDomain($data) {
        foreach ($data as &$one) {
            if(count($one['domains']) == 0) {
                continue;
            }
            $arr = explode(',', $one['domains']);
            if(count($arr) == 1) {
                $one['domains'] = $arr[0];
            } elseif(count($arr) == 2) {
                $one['domains'] = $arr [0] . '、' . $arr [1];
            } else {
                $one['domains'] = $arr [0]  . '、' . $arr [1]. '...';
            }
        }
        return $data;
    }


    /**
     *  取得首页优质方案秀
     *
     * @return array 优质方案
     */
    function getRecommendSolutions()
    {
        // 获取首页优质方案秀
        //test
        //$solutions = ZfaNicInventory::getRecommendSolutions([4130937,4130944,4130960,4130939]);
        //online
        $solutions = ZfaNicInventory::getRecommendSolutions([5534283,5534393,5534303,5534384]);
        // 数据整形
        $data = Unit::formatNicInventory(ZfaNicInventory::SOLUTION_CLASSID, $solutions, 35, 28);

        return $data;
    }
    /**
     *  取得列表页下的需求信息
     *
     * @return array 需求信息
     */
    private function getDemandsOfList($current_page = 0)
    {
        // 获取解决方案数据
        $demands = ZfaNicBuyDetail::getDataByClassid(ZfaNicInventory::SOLUTION_CLASSID, Yii::$app->params['listSize'] ,$current_page);
        // 取得最新,最热ID
        $hotAndNewIds = ZfaNicBuyDetail::getHotAndNewId(ZfaNicInventory::SOLUTION_CLASSID);
        // 数据整形
        $data = Unit::formatDemands(ZfaNicInventory::SOLUTION_CLASSID, $demands, 110, 70, $hotAndNewIds);

        return $data;
    }

    /**
     *  取得首页下解决方案的需求信息
     *
     * @param int $size 取得的件数
     * @return array 需求信息
     */
    private function getDemandsOfIndex($size)
    {
        $demands = ZfaNicBuyDetail::getDataByClassid(ZfaNicInventory::SOLUTION_CLASSID, $size);
        return  Unit::formatDemands(ZfaNicInventory::SOLUTION_CLASSID, $demands, 39, Unit::TITLE_LENGTH);
    }

}
