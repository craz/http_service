<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Json;

use app\models\TgbotUser;
use app\models\TgbotHistory;
use app\models\User;
use app\models\Func;

use app\components\TelegramBot;
use app\components\AIBot;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Accept, Accept-CH, Accept-Charset, Accept-Datetime, Accept-Encoding, Accept-Ext, Accept-Features, Accept-Language, Accept-Params, Accept-Ranges, Access-Control-Allow-Credentials, Access-Control-Allow-Headers, Access-Control-Allow-Methods, Access-Control-Allow-Origin, Access-Control-Expose-Headers, Access-Control-Max-Age, Access-Control-Request-Headers, Access-Control-Request-Method, Age, Allow, Alternates, Authentication-Info, Authorization, C-Ext, C-Man, C-Opt, C-PEP, C-PEP-Info, CONNECT, Cache-Control, Compliance, Connection, Content-Base, Content-Disposition, Content-Encoding, Content-ID, Content-Language, Content-Length, Content-Location, Content-MD5, Content-Range, Content-Script-Type, Content-Security-Policy, Content-Style-Type, Content-Transfer-Encoding, Content-Type, Content-Version, Cookie, Cost, DAV, DELETE, DNT, DPR, Date, Default-Style, Delta-Base, Depth, Derived-From, Destination, Differential-ID, Digest, ETag, Expect, Expires, Ext, From, GET, GetProfile, HEAD, HTTP-date, Host, IM, If, If-Match, If-Modified-Since, If-None-Match, If-Range, If-Unmodified-Since, Keep-Alive, Label, Last-Event-ID, Last-Modified, Link, Location, Lock-Token, MIME-Version, Man, Max-Forwards, Media-Range, Message-ID, Meter, Negotiate, Non-Compliance, OPTION, OPTIONS, OWS, Opt, Optional, Ordering-Type, Origin, Overwrite, P3P, PEP, PICS-Label, POST, PUT, Pep-Info, Permanent, Position, Pragma, ProfileObject, Protocol, Protocol-Query, Protocol-Request, Proxy-Authenticate, Proxy-Authentication-Info, Proxy-Authorization, Proxy-Features, Proxy-Instruction, Public, RWS, Range, Referer, Refresh, Resolution-Hint, Resolver-Location, Retry-After, Safe, Sec-Websocket-Extensions, Sec-Websocket-Key, Sec-Websocket-Origin, Sec-Websocket-Protocol, Sec-Websocket-Version, Security-Scheme, Server, Set-Cookie, Set-Cookie2, SetProfile, SoapAction, Status, Status-URI, Strict-Transport-Security, SubOK, Subst, Surrogate-Capability, Surrogate-Control, TCN, TE, TRACE, Timeout, Title, Trailer, Transfer-Encoding, UA-Color, UA-Media, UA-Pixels, UA-Resolution, UA-Windowpixels, URI, Upgrade, User-Agent, Variant-Vary, Vary, Version, Via, Viewport-Width, WWW-Authenticate, Want-Digest, Warning, Width, X-Content-Duration, X-Content-Security-Policy, X-Content-Type-Options, X-CustomHeader, X-DNSPrefetch-Control, X-Forwarded-For, X-Forwarded-Port, X-Forwarded-Proto, X-Frame-Options, X-Modified, X-OTHER, X-PING, X-PINGOTHER, X-Powered-By, X-Requested-With, X-Auth-Token');

if (!function_exists('getallheaders')) {
    function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
    }
}

class ApiController extends Controller
{
	private $allowed_methods;

	public function actionIndex()
	{
		$this->redirect('/');
	}

	public function init()
	{
		$this->allowed_methods = [
			'message',
		];

		$this->enableCsrfValidation = false;

		return parent::init();
	}

	protected function json($data) {

		if (isset($data['result']) && $data['result'] != 1) {
			header("HTTP/1.1 400");
		}
		
    	echo Json::encode($data);
    	die;
    }

	public function actionQuery($method)
	{
		header('Content-Type: application/json');

		if (!in_array($method, $this->allowed_methods)) {
			$this->json([
				'result' => 0,
				'msg' => "method not allowed",
				'errorCode' => 403,
			]);
		}
		
		$action = $method.'Action';

		return $this->$action();
	}

	// =====================================================================


	private function messageAction()
	{
		$str = file_get_contents("php://input");

		if (empty($str)) {
			$this->json([
				'result' => 0,
				'message' => 'Empty body',
			]);
		}

		$errors = false;


		// { "id_user": 12345, "bot_user_id": 67890,"bot_history_id": 1234, "inbox": 0, "final_comment": 0, “Model_type”: 1,  "out_data": "{ "question_to_user ":“Что ты хочешь выбрать?”,  "buttons ":[“Духи", "Туалетная вода",  “Дезодорант”], "category ":“Парфюмерия”}

		$data = json_decode($str, true);

		// \Yii::info(print_r($data, true), 'aibot');

		if (!empty($data['out_data'])) {

			$dataJson = json_decode($data['out_data'], true);
			// \Yii::info(print_r($dataJson, true), 'aibot');

			$bot = new TelegramBot();

			$buttons = [];

			if (isset($dataJson['buttons']) && !empty($dataJson['buttons'])) {
				$buttons = [$dataJson['buttons']];
			}

			$botUser = TgbotUser::findOne($data['bot_user_id']);

			if (!$botUser) {
				$this->json([
					'result' => 0,
					'message' => 'user not found',
				]);
			}


			$u = User::find()->where([
				'bot_user_id' => $botUser->id
			])->one();


			if (!$u) {
				$this->json([
					'result' => 0,
					'message' => 'API user not found',
				]);
			}


			$historyReplyingMessage = null;

			$replyMessageId = 0;
			$aggregatedMessageText = '';
			$firstAggregationMessageId = 0;

			$replyMessageHistoryId = 0;

			$answer_time = 0;

			if (@$data['bot_history_id'] > 0) {
				$historyReplyingMessage = TgbotHistory::findOne($data['bot_history_id']);

				if ($historyReplyingMessage) {
					$replyMessageId = $historyReplyingMessage->tg_api_id;

					$replyMessageHistoryId = $data['bot_history_id'];

					$now = time();
					$t1 = $historyReplyingMessage->created_at;

					if (strstr($t1, '.')) {
						$tts = explode('.', $t1);

						$t1 = $tts[0];
					}

					$t1 = strtotime($t1);

					// \Yii::info($historyReplyingMessage->created_at.' '.$t1.' '.$now.' '.date('Y-m-d H:i:s', $t1).' '.date('Y-m-d H:i:s', $now), 'aibot');

					$diff = $now - $t1;

					//$diff /= 1000;

					$diff = round($diff, 2);

					$answer_time = $diff;
				}
			}
		


		    // ======================================================================

			if (!empty(trim($botUser->queued_messages_ids))) {
				$queued_messages_ids_arr = explode(';', trim($botUser->queued_messages_ids));
				$queued_messages_ids_arr_ = [];

				foreach ($queued_messages_ids_arr as $qa) {
					if (!empty($qa)) {
						$queued_messages_ids_arr_[] = $qa;
					}
				}


				//$bot->answer($botUser->tg_id, print_r($queued_messages_ids_arr_, true), []);


				if (count($queued_messages_ids_arr_) > 0) {

					$queuedMessages = [];

					foreach ($queued_messages_ids_arr_ as $mid) {
						$msgObj = TgbotHistory::findOne($mid);

						$queuedMessages[] = $msgObj;
					}

					if (count($queuedMessages) > 0) {
						
						foreach ($queuedMessages as $qm) {
							$aggregatedMessageText .= trim($qm->in_data).'. ';
						}

						$firstAggregationMessageId = $queuedMessages[0]->id;
					}
				}

				$botUser->queued_messages_ids = '';
				$botUser->save();
			} else {
				$replyMessageId = 0;
			}

			// ======================================================================


			$historyWaitingMessages = TgbotHistory::find()->where(['bot_user_id' => $botUser->id, 'system_flag' => 1])->all();

			if (count($historyWaitingMessages) > 0) {

				if ($historyReplyingMessage)
					$replyMessageId = $historyReplyingMessage->tg_api_id;

				foreach ($historyWaitingMessages as $hm) {
					$hm->delete();

					$bot->deleteMessage($botUser->tg_id, $hm->tg_api_id);
				}

			}



			//\Yii::info($replyMessageHistoryId, 'aibot');



			//$bot->answer($botUser->tg_id, $dataJson['communication_to_user'], $buttons, '', $data['out_data']);

			$resId = 0;

			if (isset($data['is_markdown']) && (int)$data['is_markdown']) {
				$resId = $bot->answerMARKDOWN($botUser->tg_id, $dataJson['communication_to_user'], $buttons, '', $str, $replyMessageId, 0, $replyMessageHistoryId);
			} else {
				$resId = $bot->answer($botUser->tg_id, $dataJson['communication_to_user'], $buttons, '', $str, '', $replyMessageId, 0, $replyMessageHistoryId);
			}

			if ($resId > 0) {

				$historyInsertedMessage = TgbotHistory::findOne($resId);
				if ($historyInsertedMessage) {
					$historyInsertedMessage->answer_time = $answer_time;
					$historyInsertedMessage->model_type = (int)$data['model_type'];

					if ($replyMessageHistoryId > 0) {
						$historyInsertedMessage->reply_to_history_id = $replyMessageHistoryId;
					}

					$historyInsertedMessage->save();
				}
			}


			if ($u->register_tries_cnt < 1) {

				$u->register_tries_cnt++;
				$u->save();

				sleep(2);

				if ($u->referent_id > 0) {

	        		/*$text = "Ну что, остались ещё какие-то сомнения? Или уже готова стать покупателем с привилегиями и сиять как VIP-звезда? ✨🛍️
Если да — 🔥! Переходи по ссылке

Ссылка - https://avon.ru/pre-register-tp?referrer=".$u->referent_id."&utm_source=tg_cosmeteacher_bot_psp";*/

					$text = "Ну что, остались ещё какие-то сомнения? Или уже готова стать покупателем с привилегиями и сиять как VIP-звезда? ✨🛍
Если да — 🔥! 👉Переходи в личный кабинет:

https://avon.ru/pre-register-tp?referrer=".$u->referent_id."&utm_source=tg_cosmeteacher_bot_psp

✨ Хочешь узнать о новинках, зайти в личный кабинет или посмотреть обзоры и мастер-классы?
Жми кнопки под строкой ввода — или нажми ⌘, чтобы их показать. Я всё подскажу! 💖";

					if ($u->gender == 1) {
						/*$text = "Ну что, остались ещё какие-то сомнения? Или уже готов стать покупателем с привилегиями и сиять как VIP-звезда? ✨🛍️
Если да — 🔥! Переходи по ссылке

Ссылка - https://avon.ru/pre-register-tp?referrer=".$u->referent_id."&utm_source=tg_cosmeteacher_bot_psp";*/

						$text = "Ну что, остались ещё какие-то сомнения? Или уже готов стать покупателем с привилегиями и сиять как VIP-звезда? ✨🛍
Если да — 🔥! 👉Переходи в личный кабинет:

https://avon.ru/pre-register-tp?referrer=".$u->referent_id."&utm_source=tg_cosmeteacher_bot_psp

✨ Хочешь узнать о новинках, зайти в личный кабинет или посмотреть обзоры и мастер-классы?
Жми кнопки под строкой ввода — или нажми ⌘, чтобы их показать. Я всё подскажу! 💖";
					}

					$bot->answer($botUser->tg_id, $text, []);
			        die;

	        	} else {

	        		$text = "Ну что, остались ещё какие-то сомнения? Или уже готова стать покупателем с привилегиями и сиять как VIP-звезда? ✨🛍
Если да — 🔥! 👉Переходи в личный кабинет:

https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp

✨ Хочешь узнать о новинках, зайти в личный кабинет или посмотреть обзоры и мастер-классы?
Жми кнопки под строкой ввода — или нажми ⌘, чтобы их показать. Я всё подскажу! 💖";

					if ($u->gender == 1) {
						$text = "Ну что, остались ещё какие-то сомнения? Или уже готов стать покупателем с привилегиями и сиять как VIP-звезда? ✨🛍
Если да — 🔥! 👉Переходи в личный кабинет:

https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp

✨ Хочешь узнать о новинках, зайти в личный кабинет или посмотреть обзоры и мастер-классы?
Жми кнопки под строкой ввода — или нажми ⌘, чтобы их показать. Я всё подскажу! 💖";
					}

					$bot->answer($botUser->tg_id, $text, []);
			        die;
    	       	}
			}


			/*if (!empty($aggregatedMessageText)) {


				\Yii::info("aggregated text: ".$aggregatedMessageText, 'aibot');

				$aiBot = new AIBot();

	 			$response = $aiBot->sendMessage([
	 				'id_user' => $u->id,
 					'date' => time(),
					'bot_user_id' => $botUser->id,
 					'bot_history_id' => $firstAggregationMessageId,
					'is_keyboard' => false,
					'text' => $aggregatedMessageText
	 			]);
			}*/

			$this->json([
				'result' => 1,
			]);
		} else {

			$this->json([
				'result' => 0,
				'message' => 'out_data not found',
			]);
		}
	}
	
}

