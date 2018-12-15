<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PformSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户表单';
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .filters {
        display: none;
    }

</style>

<div class="pform-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建表单', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' =>['class' => 'table table-striped'],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'headerOptions' => array('style'=>'width:8%;'),
            ], 
            //'form_img_url',

            [
                'attribute' => 'form_img_url',
                'format' => 'html',
                'value' => function($model, $key, $index, $column){
                    if(!empty($model->form_img_url))
                        $form_img_url = '<img src=' . $model->form_img_url .' width=160px height=90px>';
                    else
                        $form_img_url = '<img src=http://usr.im/160x90>';

                    return $form_img_url;
                },
                'headerOptions' => array('style'=>'width:160px;'),
            ],

            //'uid',
            //'title',
            [
                'attribute' => 'title',
                'format' => 'html',
                'value' => function($model, $key, $index, $column){

                    $form_link = Yii::$app->request->hostInfo ."/customer-pform/create?pform_uid=".$model->uid;
                    return $model->title . "<br><br>填表链接：<br>" . $form_link;
                },
                'headerOptions' => array('style'=>'width:300px;'),
            ],

     
           //'description',

            // [
            //     'attribute' => 'detail',
            //     'format' => 'html',
            // ],

            [
                'label' => '表单预览',
                'format' => 'raw',
                'value'=>function ($model, $key, $index, $column) {
                    //$url = "/customer-pform/create?pform_uid=".$model->uid;

                    //return Html::a('预览', ['customer-pform/create', 'pform_uid' =>$model->uid]);
                    //http://pf.beesoft.com/customer-pform/create?pform_uid=594e763b8a4a4
                    //链接到前端视图， 暂时硬编码。

                    //return "<a href=". Yii::$app->request->hostInfo ."/customer-pform/create?pform_uid=".$model->uid.">预览</a>";

                    $form_link = Yii::$app->request->hostInfo ."/customer-pform/create?pform_uid=".$model->uid;
                    $form_title = $model->title;

                    return "<button class='btn btn-default btn-form-link' data-toggle='modal' data-target='#myModal1' form_title_attr='".$form_title."' form_link_attr='".$form_link."'><i class='glyphicon glyphicon-phone'></i> 预览</button>";
                },

                //'headerOptions' => array('style'=>'width:70px;'),
            ],

            [
                'label' => '包含字段',
                'value'=>function ($model, $key, $index, $column) {

                    $formfields = \backend\models\PformField::find()
                                    ->where(["pform_uid" => $model->uid])
                                    ->all();

                    $field_str = "";
                    if(empty($formfields))
                        return $field_str;

                    foreach ($formfields as $formfield) {
                        $field_str = $field_str."【".$formfield->title."】<br>";
                    }
                    return "<span>".$field_str."</span>";
                },
                'format' => 'html',
                //'filter' => \common\models\CampaignOrder::getGhOption(),
                'headerOptions' => array('style'=>'width:25%;'),
            ],

            // 'created_at:datetime',
            // 'updated_at:datetime',
            // 'user_id',
            // 'description',
            //http://127.0.0.1/adv/backend/web/index.php?PformFieldSearch%5Bpform_uid%5D=594cd9feac29c&r=pform-field
            
            // ['class' => 'yii\grid\ActionColumn'],
            // // [
            // //     'class' => 'yii\grid\ActionColumn', 
            // //     'template' => '{update}   {delete}',
            // //     'headerOptions' => array('style'=>'width:12%;'),
            // // ],

            // ],

                [
                    'label' => '填单人数',
                    'format' => 'html',
                    'value' => function ($model, $key, $index, $column) {
                        // $customerform_count = \backend\models\CustomerPform::find()
                        //     ->where(["pform_uid" => $model->uid])
                        //     ->count();
                        // $formfield_count = \backend\models\PformField::find()
                        //     ->where(["pform_uid" => $model->uid])
                        //     ->count();
                        // return $customerform_count/$formfield_count;
                        
                        $customerform_count = \backend\models\CustomerPform::find()
                            ->select(['customer_pform_uid'])
                            ->where(["pform_uid" => $model->uid])
                            ->distinct()
                            ->count();
                        return $customerform_count;
                    },
                    'headerOptions' => array('style'=>'width:14%;'),
                ],

            [
                'class' => 'yii\grid\ActionColumn', 
                 // 'label' => '填表数据',
                //'template' => '{addbackcover} {view} {update} {delete} {list}',
                'template' => '{myview} {addbackcover} {update} {list}',
                'headerOptions' => array('style'=>'width:12%;'),
                'buttons' => [
                    'myview' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-plus" style="color:#5CB85C"></span>&nbsp;', ['pform/view', 'id' => $model->id], ['title' => '添加表单字段']);
                    },

                    'list' => function ($url, $model, $key) {
                        return Html::a('&nbsp;&nbsp;<span class="glyphicon glyphicon-stats"></span>&nbsp;', ['customer-pform/statistics', 'uid' => $model->uid], ['title' => '查看填表数据']);
                    },

                    'addbackcover' => function ($url, $model, $key) {
                        return Html::a('&nbsp;<span class="glyphicon glyphicon-star-empty"></span>&nbsp;', ['pform-backcover/add', 'uid' => $model->uid], ['title' => '增加填表成功页面']);
                    },


                ],
            ],
        ],
    ]); ?>
</div>

<!-- 二维码模式窗口 for mobile -->
<div class="modal fade"  id="myModal1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">预览表单</h3>
            </div>
            <div class="modal-body">
                <h4 class="modal-title" id="form_title"></h4>

                <div class="alert alert-success" role="alert" id="preview"</div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-success" id="addMetadata">确定</button>
            </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function(){
        var host = "<?= Yii::$app->request->hostInfo ?>";
       
        $(".btn-form-link").click(function(){

            var form_link_attr = $(this).attr("form_link_attr");
            var form_title = $(this).attr("form_title_attr");
            
            var preview = "<img width=100% src='http://qr.liantu.com/api.php?text=" + form_link_attr +  "' /> <small class='center-block text-center'>手机扫一扫预览</small>";
            var preview1 = "<a href='" + host + "/customer-pform/preview?text=" + form_link_attr +  "'>PC上预览</a>";

            //var  preview1 = "<a href='http://m.baidu.com'>baidu</a>";

            $("#preview").html(preview);
       
            $("#form_title").html(form_title + '&nbsp;&nbsp; <small>' + preview1 + '</small>');
        });

    });//end of documnet ready

</script>
