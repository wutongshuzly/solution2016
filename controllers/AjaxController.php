<?php
namespace frontend\controllers;


use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

use common\models\ZfaCorp;
use common\models\ZfaNicBuyDetail;
use common\models\ZfaNicInventory;
use common\models\ZfaUser;
use common\models\Unit;


/**
 *  Ajax控制器
 */
class AjaxController extends Controller
{

    /**
     * ajax action
     *
     * @return mixed
     */
    public function actionMore()
    {
            $data = Yii::$app->request->get();
            $ajax_type = $data['ajaxType'];
            $list = [];
            if ("more_solution" == $ajax_type || "more_demand" == $ajax_type) {
                $current_page = $data['currentPage'];
                if ("more_solution" == $ajax_type) {
                    $data = $this->moreSolutionsByType($data['domain'], $current_page, $data['order'], $data['sort']);
                } elseif ("more_demand" == $ajax_type) {
                    $list = ZfaNicBuyDetail::getDataByClassid(ZfaNicInventory::SOLUTION_CLASSID, Yii::$app->params['listSize'], $current_page);
                    // 数据整形
                    $data = Unit::formatDemands(ZfaNicInventory::SOLUTION_CLASSID, $list, 110, Unit::TITLE_LENGTH);
                }
            }

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'json' => $data,
                'code' => 200,
            ];
    }

    /**
     *  领域下获取更多解决方案信息
     *
     * @param string $type 领域导航分类
     * @param string $current_page 当前页
     * @return array
     */
     private function moreSolutionsByType($domain = null, $current_page = 0, $order, $sort)
    {
        //获取和整形所有的解决方案数据
        $data = $this->formatAllSolution($order, $sort);
        $list = $this->filter($data, $domain);
        return $this->pagination($list, $current_page);

    }

    /**
     *  获取和整形所有的解决方案数据
     *
     * @return array
     */
    public function formatAllSolution($order, $sort) {
        // 获取全部解决方案数据
        $solutions = ZfaNicInventory::getSolution($order, $sort);
        // 取得最新,最热ID
        $hotAndNewIds = ZfaNicInventory::getHotAndNewId(ZfaNicInventory::SOLUTION_CLASSID);
        // 数据整形
        $data = Unit::formatNicInventory(ZfaNicInventory::SOLUTION_CLASSID, $solutions, Unit::INTRO_LENGTH, Unit::TITLE_LENGTH, $hotAndNewIds);
        return $data;
    }

    /**
     *  领域数据筛选处理
     *
     * @param array   $data 解决方案信息
     * @param string  $domain 导航分类
     *
     * @return array
     */
    public function filter($data, $domain) {

        if (is_null($domain)|| $domain == '') {
            // 全部领域检索场合下，如果应用领域有2个以上，只显示前两个
           foreach ($data as &$one) {
                if ($one['domains'] == '') {
                    continue;
                }
                $arr = explode(',', $one['domains']);
                if (count($arr) == 1) {
                    $one['domains'] = $arr [0];
                } elseif(count($arr) == 2) {
                    $one['domains'] = $arr [0] . '、' . $arr [1];
                } else {
                    $one['domains'] = $arr [0]  . '、' . $arr [1]. '...';
                }
            }
            return $data;
        }


        $list = [];
        foreach ($data as &$one) {
            if ($one['domains'] == '') {
                continue;
            }
            $tmp = strpos($one['domains'], $domain);
            if (!is_bool($tmp)){
                $one['domains'] = $domain;
                $list[] = $one;
            }
        }
        return $list;
    }

    /**
     *  数组分页切割
     *
     * @param array   $info 解决方案信息
     * @param int 当前页
     * @return string
     */
    static public function pagination($data, $current_page = 0)
    {
        // 数组分页处理
        $info = [];
        $size = Yii::$app->params['listSize'];
        $index = $current_page * $size;
        for($i = 0; $i < $size; $i++) {
            if (!array_key_exists($index, $data)) {
                break;
            }
            $info[] =  $data[$index];
            $index++;
        }
        return $info;
    }
    /**
     *  取得解决方案首页领域变更时，取得解决方案信息
     *
     * @return string
     */
    public function actionDomainChange()
    {

        $domain = yii::$app->request->getQueryParam('type');
        $order = yii::$app->request->getQueryParam('order');
        $sort = yii::$app->request->getQueryParam('sort');
        $solutions = $this->formatAllSolution($order, $sort);

        // 所属领域筛选处理
        $info = $this->filter($solutions, $domain);
        // 只取第一页
        $data = $this->pagination($info, 0);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'json' => $data,
            'code' => 200,
        ];
    }

    /**
     *  取得科技成果首页领域变更时，取得科技成果信息
     *
     * @return string
     */
    public function actionTechDomainChange()
    {

        $id = yii::$app->request->getQueryParam('type');
        $order = yii::$app->request->getQueryParam('order');
        $sort = yii::$app->request->getQueryParam('sort');

        // 取得最新,最热ID
        $hotAndNewIds = ZfaNicInventory::getHotAndNewId($id);
        // 获取科技成果数据
        $techs = ZfaNicInventory::getDataByClassid($id, Yii::$app->params['listSize'], 0, $order, $sort);
        // 数据整形
        $data = Unit::formatNicInventory(ZfaNicInventory::TECH_CLASSID, $techs, Unit::INTRO_LENGTH, Unit::TITLE_LENGTH, $hotAndNewIds);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'json' => $data,
            'code' => 200,
        ];
    }

    /**
     *  取得科技成果需求列表页领域变更时，取得科技成果需求信息
     *
     * @return string
     */
    public function actionTechDemandDomainChange()
    {
        $id = yii::$app->request->getQueryParam('type');
        // 获取科技成果需求需求数据
        $techs = ZfaNicBuyDetail::getDataByClassid($id, Yii::$app->params['listSize'], 0);
        // 数据整形
        $data = Unit::formatDemands(ZfaNicInventory::TECH_CLASSID, $techs, 110, Unit::TITLE_LENGTH);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'json' => $data,
            'code' => 200,
        ];
    }
    /**
     * ajax action
     *
     * @return mixed
     */
    public function actionMoreTech()
    {
            $data = Yii::$app->request->get();
            $ajax_type = $data['ajaxType'];
            $current_page = $data['currentPage'];
            $list = [];
            if ("more_tech" == $ajax_type || "more_demand" == $ajax_type) {
                $id = $data['domain'];
                if ("more_tech" == $ajax_type) {
                      // 获取科技成果需求数据
                    $techs = ZfaNicInventory::getDataByClassid($id, Yii::$app->params['listSize'], $current_page);
                    $data = Unit::formatNicInventory(ZfaNicInventory::TECH_CLASSID, $techs, Unit::INTRO_LENGTH, Unit::TITLE_LENGTH);
                } elseif ("more_demand" == $ajax_type) {
                    $list = ZfaNicBuyDetail::getDataByClassid($id, Yii::$app->params['listSize'], $current_page);
                    // 数据整形
                    $data = Unit::formatDemands(ZfaNicInventory::TECH_CLASSID, $list, 110, Unit::TITLE_LENGTH);
                }
            }

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'json' => $data,
                'code' => 200,
            ];
    }

    /**
     * ajax action
     *
     * @return mixed
     */
    public function actionGetNewAndHot()
    {
        // 取得最新,最热ID
        $data = ZfaNicInventory::getHotAndNewId(ZfaNicInventory::SOLUTION_CLASSID);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'json' => $data,
            'code' => 200,
        ];
    }
}
