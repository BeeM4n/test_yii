<?php

namespace app\controllers;

use app\models\enum\PrizeType;
use app\models\enum\RewardStatus;
use app\models\enum\RewardType;
use app\models\User;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Reward;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'new', 'convert', 'process'],
                'rules' => [
                    [
                        'actions' => ['logout', 'new', 'convert', 'reject', 'process'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'newReward' => ['get'],
                    'convert' => ['get'],
                    'reject' => ['get'],
                    'process' => ['get'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $params = [];
        if (!Yii::$app->user->isGuest) {
            $params['currentUser'] =  User::findIdentity(Yii::$app->user->getId());

            $query = Reward::find();
            $query->where('user_id = :user_id', ['user_id' => Yii::$app->user->id]);

            $pagination = new Pagination([
                'defaultPageSize' => 5,
                'totalCount' => $query->count(),
            ]);

            $params['rewards'] = $query
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
            $params['pagination'] = $pagination;
        }
        return $this->render('index', $params);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Create new reward
     *
     * @return Response
     */
    public function actionNew()
    {
        $currentUser =  User::findIdentity(Yii::$app->user->getId());

        $reward = new Reward();
        $reward->user_id = $currentUser->id;
        $reward->status = RewardStatus::NEW;
        $reward->type = RewardType::getRandom();

        switch ($reward->type) {
            case RewardType::MONEY:
                $reward->amount = rand(Yii::$app->params['minMoneyReward'], Yii::$app->params['maxMoneyReward']);
                break;
            case RewardType::BONUS:
                $reward->amount = rand(Yii::$app->params['minBonusReward'], Yii::$app->params['maxBonusReward']);
                break;
            case RewardType::PRIZE:
                $reward->prize_type = PrizeType::getRandom();
                break;
        }

        $reward->save();

        return $this->goHome();
    }

    /**
     * Convert money/bonus reward
     *
     * @param $rewardId
     * @return Response
     */
    public function actionConvert($rewardId)
    {
        $currentUser =  User::findIdentity(Yii::$app->user->getId());

        $reward = Reward::findOne($rewardId);

        if (!$reward || $reward->user_id != $currentUser->id) {
            return $this->goHome();
        }

        $reward->convert();
        $reward->save();

        return $this->goHome();
    }

    /**
     * Reject reward
     *
     * @param $rewardId
     * @return Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionReject($rewardId)
    {
        $currentUser =  User::findIdentity(Yii::$app->user->getId());

        $reward = Reward::findOne($rewardId);

        if (!$reward || $reward->user_id != $currentUser->id) {
            return $this->goHome();
        }

        $reward->delete();

        return $this->goHome();
    }
}
