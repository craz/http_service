<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

use app\models\DispatchPushes;
use app\models\User;
use app\models\TgbotUser;

use app\components\TelegramBot;

class CronController extends Controller
{
    public function actionDispatchpushes1()
    {
    	$users = User::find()->where(['is_dispatch_subscribed' => 1, 'ads_dispatch' => 1, 'is_intro_processed' => 1])->all();

    	//$users = User::find()->where(['is_dispatch_subscribed' => 1, 'ads_dispatch' => 1, 'is_intro_processed' => 1, 'id'=>244])->all();

    	//$users = User::find()->where(['is_dispatch_subscribed' => 1, 'ads_dispatch' => 1, 'is_intro_processed' => 1, 'phone'=>'79262650030'])->all();

    	//echo count($users);
    	//die;

    	$tgBot = new TelegramBot();

    	foreach ($users as $u) {

    		$botUser = TgbotUser::find()->where(['user_id'=>$u->id])->one();

    		if ($botUser) {

    			$push = DispatchPushes::find()->where(['daytype'=>1])->orderBy(['random()'=>SORT_DESC])->one();

    			$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp";

    			if ($u->referent_id > 0) {
    				$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp&referrer=".$u->referent_id;
    			}

    			$tgBot->answer($botUser->tg_id, $push->body, []);
    			//$tgBot->answer($botUser->tg_id, $push->body.PHP_EOL.PHP_EOL.$url, []);
    		}

    	}

    	return true;

    	//print_r($pushes);
    	//die;
    }

    public function actionDispatchpushes4()
    {
    	$users = User::find()->where(['is_dispatch_subscribed' => 1, 'ads_dispatch' => 1, 'is_intro_processed' => 1])->all();

    	//$users = User::find()->where(['is_dispatch_subscribed' => 1, 'ads_dispatch' => 1, 'is_intro_processed' => 1, 'id'=>244])->all();

    	//echo count($users);
    	//die;

    	$tgBot = new TelegramBot();

    	foreach ($users as $u) {

    		$botUser = TgbotUser::find()->where(['user_id'=>$u->id])->one();

    		if ($botUser) {

    			$push = DispatchPushes::find()->where(['daytype'=>4])->orderBy(['random()'=>SORT_DESC])->one();

    			$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp";

    			if ($u->referent_id > 0) {
    				$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp&referrer=".$u->referent_id;
    			}

    			$tgBot->answer($botUser->tg_id, $push->body, []);
    			//$tgBot->answer($botUser->tg_id, $push->body.PHP_EOL.PHP_EOL.$url, []);
    		}

    	}

    	return true;

    	//print_r($pushes);
    	//die;
    }

    public function actionDispatchpushes5()
    {
    	$users = User::find()->where(['is_dispatch_subscribed' => 1, 'ads_dispatch' => 1, 'is_intro_processed' => 1])->all();

    	//$users = User::find()->where(['is_dispatch_subscribed' => 1, 'ads_dispatch' => 1, 'is_intro_processed' => 1, 'id'=>244])->all();

    	//echo count($users);
    	//die;

    	$tgBot = new TelegramBot();

    	foreach ($users as $u) {

    		$botUser = TgbotUser::find()->where(['user_id'=>$u->id])->one();

    		if ($botUser) {

    			$push = DispatchPushes::find()->where(['daytype'=>5])->orderBy(['random()'=>SORT_DESC])->one();

    			$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp";

    			if ($u->referent_id > 0) {
    				$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp&referrer=".$u->referent_id;
    			}

    			$tgBot->answer($botUser->tg_id, $push->body.PHP_EOL.PHP_EOL.$url, []);
    		}

    	}

    	return true;

    	//print_r($pushes);
    	//die;
    }

    public function actionRegisterpush()
    {
    	$users = User::find()->where("created_at < current_timestamp - interval '24 hours' and is_registered!=3 and is_intro_processed=1 and is_register_push_sended_1=0")->all();

    	//$users = User::find()->where("created_at < current_timestamp - interval '24 hours' and is_registered!=3 and is_intro_processed=1 and is_register_push_sended_1=0 and id=244")->all();

    	//echo count($users);
    	//die;

    	$tgBot = new TelegramBot();

    	foreach ($users as $u) {

    		$botUser = TgbotUser::find()->where(['user_id'=>$u->id])->one();

    		if ($botUser) {

    			$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp";

    			if ($u->referent_id > 0) {
    				$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp&referrer=".$u->referent_id;
    			}

    			$text = trim($u->name).", если у тебя не осталось сомнений и вопросов - скорее переходи в личный кабинет!🚀💻

<a href='".$url."'>Переходи в личный кабинет</a>";

    			$tgBot->answerHTML($botUser->tg_id, $text, []);

    			$u->is_register_push_sended_1 = 1;
    			$u->save();

    			sleep(1);
    		}

    	}

    	return true;
    }
}
