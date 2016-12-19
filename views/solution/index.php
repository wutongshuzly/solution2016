<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$notFoundFlg = false;
if (count($solutions)==0) {
    $notFoundFlg = true;
}

?>

<!-- 内容开始 -->
<div class="container">
    <!-- 优质方案秀 -->
    <div class="highshow-wrap">
        <div class="highshow-hd">
            <p>
                <span class="active">优质方案秀</span>
            </p>
        </div>
        <div class="highshow-bd">
            <ul>
                <?php foreach ($recommend_solutions as $one ) { ?>
                <a href="<?=$one['detail_url'] ?>" target="_blank">
                    <li>
                        <div class="img-div">
                            <img width="200" height="152" src="<?=$one['image']?>">
                            <i class="tj-top"></i>
                        </div>
                        <div class="show-info">
                            <h2><?=$one['product_name'] ?></h2>
                            <p><?=$one['description'] ?></p>
                            <p class="btn-p"><span><b>￥</b><?=$one['price'] ?></span><a href="<?=$one['detail_url'] ?>" class="ljgm" target="_blank">购买方案</a></p>
                        </div>
                    </li>
                </a>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="solution-wrap solution-overhidden" id="solutionTab">
        <div class="solution-hd border-color">
                <p>
                    <span class="tab-t <?php if(empty($this->params['current_menu']) || is_null($this->params['current_menu'])) echo 'active'; ?>" onclick="domainchange('')">全部</span>
                    <span class="tab-t <?php if(!empty($this->params['current_menu']) && $this->params['current_menu'] == '智慧城市') echo 'active'; ?>" onclick="domainchange('智慧城市')">智慧城市</span>
                    <span class="tab-t <?php if(!empty($this->params['current_menu']) && $this->params['current_menu'] == '智能家居') echo 'active'; ?>" onclick="domainchange('智能家居')">智能家居</span>
                    <span class="tab-t <?php if(!empty($this->params['current_menu']) && $this->params['current_menu'] == '智能安防') echo 'active'; ?>" onclick="domainchange('智能安防')">智能安防</span>
                </p>
        </div>
        <div class="solution-wrap-l mt20 solution-wrap-l-l" id="">
            <div class="top-banner">
                <p>
                    <span class="active" onclick="orderchange('default',this)">默认</span>
                    <span onclick="orderchange('hot',this)" class="">人气<i class="icon-down icon-down"></i></span>
                    <span onclick="orderchange('update_time',this)">更新时间<i class="icon-down"></i></span>
                    <span onclick="orderchange('price',this)">价格<i class="icon-down"></i></span>
                </p>
            </div>
            <div class="solutions-list" id="solutions-list">
                <div class="solution-bd">
                    <div class="silder-area active">
                        <?php if ($notFoundFlg) { ?>
                            <div class="">
                                <p><br>
                                <h1><?= "没有找到符合条件的解决方案信息"?></h1>
                                <br></p>
                            </div>
                        <?php } else { ?>
                            <ul class="list-thelist">
                                <?php foreach ($solutions as $one ) { ?>
                                    <div class="slider-list">
                                        <?php if ($one['hot_flg']) { ?>
                                            <div class="platform" style="">
                                                <div class="platform-list">平台认证</div>
                                                <div class="platform-list1" style=""></div>
                                            </div>
                                        <?php } ?>
                                        <div class="slider-list-img"> <img src="<?=$one['image']?>"></div>
                                        <div class="slider-list-con">
                                            <h2><a target="_blank" href="<?=$one['detail_url'] ?>"><?=$one['product_name'] ?></a>
                                                <?php if ($one['new_flg'] && $one['red_flg']) { ?>
                                                    <i class="icon-new"></i>
                                                <?php  } ?>
                                                <?php if ($one['hot_flg']) { ?>
                                                    <i class="icon-hottip"></i>
                                                <?php  } ?>
                                            </h2>
                                            <p>应用领域：<span class=""><?=$one['domains'] ?></span>&nbsp;&nbsp;&nbsp;&nbsp;更新时间：
                                                <?php if ($one['red_flg']) { ?>
                                                    <span class="colorred"><?=$one['update_time'] ?></span>
                                                <?php } else { ?>
                                                    <span><?=$one['update_time'] ?></span>
                                                <?php } ?>
                                            </p>
                                            <p class="text-con"><?=$one['description'] ?></p>
                                        </div>
                                        <div class="company-con">
                                            <h2><a style="text-decoration:none;cursor:default;" href='javascript:;'><?=$one['corp_name'] ?></a></h2>
                                            <p>
                                                <?php foreach($one['roles_imgs'] as $img) { ?>
                                                    <img src="<?=$img?>">
                                                <?php  } ?>
                                            </p>
                                            <div class="Online-talk"><a href="javascript:;" class="service" id="<?=$one['id'] ?>"><i class="icon-talk"></i>&nbsp;<span class="text-online">在线咨询</span></a></div>
                                            <script>
                                                BizQQWPA.addCustom([{aty: '1', a: '1001', nameAccount: 4006265285, selector: '<?=$one['id'] ?>'},
                                                ]);
                                                $(".platform-list").hover(function(event){
                                                    var target = $(event.currentTarget);
                                                    target.text('平台认证，提供对接服务')
                                                    target.css("background","url(http://images.cecb2b.com/images/common-service/bg2.png) no-repeat");
                                                    target.next().stop(true).animate({
                                                        width: 0,
                                                    }, 1000)
                                                },function(event){

                                                    var target = $(event.currentTarget);
                                                    target.next().stop(true).animate({
                                                        width: 87,
                                                    }, 1000,function() {
                                                        target.css("background","url(http://images.cecb2b.com/images/common-service/bg1.png) no-repeat");
                                                        target.text('平台认证')
                                                    })
                                                })
                                            </script>
                                        </div>
                                        <div class="but-now">
                                            <p><strong><b>￥</b><?=$one['price'] ?></strong></p><a class="ljgm" target="_blank" href="<?=$one['detail_url'] ?>">购买方案</a> </div>
                                    </div>
                                <?php } ?>
                            </ul>
                            <div class="mod-page">
                                <?= LinkPager::widget(['pagination' => $pagination]); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <input type="hidden" value="" name="currentOrder" id="currentOrder">
            <input type="hidden" value="" name="currentDomain" id="currentDomain">
            <input type="hidden" value="<?=$hot ?>" name="hot" id="hot">
            <input type="hidden" value="<?=$new ?>" name="new" id="new">
            <div class="load-more" id="addMore">
                <span><b class="loading"></b><div id='addMoreLabel'>加载更多&hellip;&hellip;</div></span>
            </div>
        </div>

        <script language="JavaScript">
            $(".solution-p span").click(function(event){
                $(".solution-p span").removeClass('active');
                var $this=$(event.currentTarget);
                $this.addClass("active");
            });
            //解决方案排序记录
            var solution_default_sort_count = 1;
            var solution_hot_sort_count = 0;
            var solution_update_time_sort_count = 0;
            var solution_price_sort_count = 0;

            function domainchange(domain) {
                solution_current_page = 1;
                zhcs_current_page = 1;//智慧城市
                znjj_current_page = 1;//智能家居
                znaf_current_page = 1;//智能安防
                document.getElementById("addMoreLabel").innerHTML="";
                document.getElementById("addMoreLabel").innerHTML="加载更多&hellip;&hellip;";

                $("#currentDomain").val(domain);
                var order = $("input[name='currentOrder']").val();
                ajax_submit(domain,order);
            }

            function ajax_submit(domain,order) {
                sort = 1;
                if(order == 'update_time') {
                    sort = solution_update_time_sort_count % 2;
                }else if(order == 'price') {
                    sort = solution_price_sort_count % 2;
                }else if(order == 'hot') {
                    order = 'last_audit_time'
                    sort = solution_hot_sort_count % 2;
                }else {
                    order = 'update_time'
                }
                $request_url = "<?=Url::to(['/ajax/domain-change'])?>";
                $.getJSON($request_url,
                    'type='+domain +'&order='+order +'&sort='+sort,
                    function(data){
                        jsonlist='<div class="solution-bd">';
                        jsonlist+='<div class="silder-area active">';
                        jsonlist+='<ul class="list-thelist">';
                        var i;
                        for(i= 0;i<data.json.length;i++) {
                            jsonlist+=' <div class="slider-list">';
                            if (data.json[i].hot_flg) {
                                jsonlist+=' <div class="platform" style="">';
                                jsonlist+=' <div class="platform-list">平台认证</div>';
                                jsonlist+=' <div class="platform-list1" style=""></div>';
                                jsonlist+=' </div>';
                            }
                            jsonlist+='<div class="slider-list-img">';
                            jsonlist+='<img src="'+ data.json[i].image +'">';
                            jsonlist+='</div>';
                            jsonlist+='<div class="slider-list-con">';

                            jsonlist += ' <h2><a target="_blank" href="' + data.json[i].detail_url + '">' + data.json[i].product_name + '</a>';
                            if (data.json[i].new_flg && data.json[i].red_flg) {
                                jsonlist += ' <i class="icon-new"></i>';
                            }
                            if (data.json[i].hot_flg) {
                                jsonlist += ' <i class="icon-hottip"></i>';
                            }
                            jsonlist += '</h2>';
                            jsonlist += '<p>应用领域：<span class="">' + data.json[i].domains + '</span>&nbsp;&nbsp;&nbsp;&nbsp;更新时间：' ;
                            if (data.json[i].red_flg) {
                                jsonlist += ' <span class="colorred">' + data.json[i].update_time + '</span></p>';
                            } else {
                                jsonlist += ' <span>' + data.json[i].update_time + '</span></p>';
                            }
                            jsonlist += '<p class="text-con">' + data.json[i].description + '</p>';
                            jsonlist += '</div>';
                            jsonlist += '<div class="company-con">';
                            jsonlist += '<h2><a style="text-decoration:none;cursor:default;" href="javascript:;">' + data.json[i].corp_name + '</a></h2>';
                            jsonlist += '<p>';
                            for(var index = 0;index<data.json[i].roles_imgs.length;index++) {
                                jsonlist += ' <img src="'+data.json[i].roles_imgs[index]+'">';
                            }
                            jsonlist += '</p>';
                            jsonlist += '<div class="Online-talk"><a href="javascript:;" class="service" id="'+data.json[i].id +'"><i class="icon-talk"></i>&nbsp;<span class="text-online">在线咨询</span></a></div>';
                            jsonlist += '</div>';
                            jsonlist += '<div class="but-now">';
                            jsonlist += '<p>￥<strong>' + data.json[i].price + '</strong></p>';
                            jsonlist+='<a class="ljgm" target="_blank" href="'+ data.json[i].detail_url +'">购买方案</a>';

                            jsonlist += '  </div>';
                            jsonlist += '  </div>';
                            jsonlist += '  <script>';
                            jsonlist += ' BizQQWPA.addCustom([{aty: "1", a: "1001", nameAccount: 4006265285, selector:"'+ data.json[i].id +'\"},]);';
                            jsonlist += '  <\/script>';
                        }
                        jsonlist+='</ul>';
                        jsonlist+='</div>';
                        jsonlist+='</div>';
                        $(".solutions-list").html(jsonlist);

                        $(".platform-list").hover(function(event){
                            var target = $(event.currentTarget);
                            target.text('平台认证，提供对接服务')
                            target.css("background","url(http://images.cecb2b.com/images/common-service/bg2.png) no-repeat");
                            target.next().stop(true).animate({
                                width: 0,
                            }, 1000)
                        },function(event){

                            var target = $(event.currentTarget);
                            target.next().stop(true).animate({
                                width: 87,
                            }, 1000,function() {
                                target.css("background","url(http://images.cecb2b.com/images/common-service/bg1.png) no-repeat");
                                target.text('平台认证')
                            })
                        })

                        if (i == 0) {
                            document.getElementById("addMoreLabel").innerHTML="暂无更多内容！"
                        }
                    }
                );
            }
            function orderchange(order,e) {
                $(e).addClass('active').siblings().removeClass('active');

                //解决方案首页排序切换
                document.getElementById("addMoreLabel").innerHTML="";
                document.getElementById("addMoreLabel").innerHTML="加载更多&hellip;&hellip;";
                $("#currentOrder").val(order);

                $(".top-banner span i").removeClass("icon-up1 icon-down1");

                if(order == 'update_time') {
                    solution_update_time_sort_count += 1;
                    if (solution_update_time_sort_count % 2 == 0){
                        $(e).children('i').addClass('icon-up1');
                    } else {
                        $(e).children('i').addClass('icon-down1');
                    }
                }
                if(order == 'price') {
                    solution_price_sort_count += 1;
                    if (solution_price_sort_count % 2 == 0){
                        $(e).children('i').addClass('icon-up1');
                    } else {
                        $(e).children('i').addClass('icon-down1');
                    }
                }
                if(order == 'hot') {
                    sort = solution_hot_sort_count += 1;
                    if (solution_hot_sort_count % 2 == 0){
                        $(e).children('i').addClass('icon-up1');
                    } else {
                        $(e).children('i').addClass('icon-down1');
                    }
                }
                if(order == 'default') {
                    solution_default_sort_count += 1;
                }
                var domain = $("input[name='currentDomain']").val();
                //排序变动后，当前页恢复为第一页
                solution_current_page = 1;//全部
                zhcs_current_page = 1;//智慧城市
                znjj_current_page = 1;//智能家居
                znaf_current_page = 1;//智能安防

                ajax_submit(domain,order);
            }

            $(function() {
                $(".platform-list").hover(function(event){
                    var target = $(event.currentTarget);
                    target.text('平台认证，提供对接服务')
                    target.css("background","url(http://images.cecb2b.com/images/common-service/bg2.png) no-repeat");
                    target.next().stop(true).animate({
                        width: 0,
                    }, 1000)
                },function(event){

                    var target = $(event.currentTarget);
                    target.next().stop(true).animate({
                        width: 87,
                    }, 1000,function() {
                        target.css("background","url(http://images.cecb2b.com/images/common-service/bg1.png) no-repeat");
                        target.text('平台认证')
                    })
                })
            });
            </script>