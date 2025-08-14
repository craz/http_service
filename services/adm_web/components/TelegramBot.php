<?php

namespace app\components;

use Yii;
use yii\helpers\Url;

use app\models\TgbotUser;
use app\models\TgbotHistory;
use app\models\User;
use app\models\Logs;
use app\models\Settings;
use app\models\Func;
use app\models\Mailer;

use app\models\UserQuiz;

use app\components\AIBot;
use app\components\TelegramBotQuiz1;

class TelegramBot
{
    private $token = "";

    public function __construct() {
   		$this->token = env('TELEGRAM_API_KEY');
    }

    protected function logInput($data) {
		file_put_contents(\Yii::$app->basePath.'/runtime/logs/tg.log', "=======================".PHP_EOL.date('Y-m-d H:i:s').PHP_EOL.$data, FILE_APPEND);
	}

    public function init()
    {
        $rawData = json_decode(file_get_contents('php://input'), true);
        if (!empty($rawData)) {

			$this->logInput(print_r($rawData, true));

	        $this->router($rawData);
		}

        return true;
    }

    private function router($data)
    {
        if (array_key_exists("message", $data)) {

        	$chat_id = $data['message']['chat']['id'];

        	if ($chat_id < 0) {
        		$this->answer($chat_id, 'Я могу разговаривать только в чате с отдельным участником', [
				]);
				die;
        	}




        	//$this->sendTypingAction($chat_id);
			//die;







        	/*if ($chat_id != 172248947) {
        		$this->answer($chat_id, 'Service temporarily unavailable', [
				]);
				die;
        	}*/



        	$user = TgbotUser::find()->where([
				'tg_id' => $chat_id,
			])->one();

			if (!$user) {
				$user = new TgbotUser();
				$user->tg_id = $chat_id;
				$user->bdate_iteration = 0;

				if (!empty($data['message']['chat']['username']))
					$user->tg_login = $data['message']['chat']['username'];

				$user->user_id = 0;

				$user->save();
			}

			$u = User::find()->where([
				'bot_user_id' => $user->id
			])->one();

			if (!$u) {
				$u = new User();
				$u->bot_user_id = $user->id;
				$u->is_dispatch_subscribed = 1;
				$u->birth_date = '1970-01-01 00:00:00';
				$u->save();

				$user->user_id = $u->id;
				$user->save();
			}


        	$lastMessageFromBot = TgbotHistory::find()->where("is_bot=1 and bot_user_id=".$user->id." and system_flag=0")->orderBy(['id' => SORT_DESC])->one();

        	if ($lastMessageFromBot) {
        		$lastMessageFromBot->action_url = trim($lastMessageFromBot->action_url);
        	}


        	$lastMessageGlobal = TgbotHistory::find()->where("bot_user_id=".$user->id." and system_flag=0")->orderBy(['id' => SORT_DESC])->one();


        	if (array_key_exists("text", $data['message'])) {

        		$text = @$data['message']['text'];


        		if (strstr($text, '/start ')) {
        			$umSource = trim(substr($text, 7));

        			if (!empty($umSource)) {

        				if ($umSource == 'quiz') {

        					$userQuiz = UserQuiz::find()->where(['user_id'=>$u->id])->one();
        					if ($userQuiz && !empty($userQuiz->result)) {

        						$this->answer($chat_id, "Вы уже приняли участие в этой активности. Спасибо!", []);
	        					die;

        					}

        					$u->quiz_1_started = 1;
        					$u->save();

        					$quizText = "Если бы твой стиль был образом, то каким?

1. 🎞 Классика с винтажными акцентами
2. 🌬 Воздушное платье и распущенные волосы
3. 🖤 Тёмный тотал-лук и стрелки
4. 👟 Oversize, кроссовки и кофе
5. 💫 Блёстки, неон и максимум всего";

        					$this->answer($chat_id, $quizText, [["1", "2", "3", "4", "5"]], 'quiz_1_question_1');
        					die;
        				}

        				$umSourcesArr = explode('___', $umSource);

        				$refId = (int)$umSourcesArr[0];

        				if ($u) {
							$u->referent_id = $refId;
							$u->save();
						}

						for ($i = 1; $i < count($umSourcesArr); $i++) {

							$ddt = $umSourcesArr[$i];

							if (strstr($ddt, '=') && strstr($ddt, 'utm_')) {

								$ddts = explode('=', $ddt);

								$kkey = $ddts[0];

								$kkey = strtolower($kkey);

								if ($kkey == 'utm_medium' || $kkey == 'utm_source' || $kkey == 'utm_campaign' || $kkey == 'utm_term' || $kkey == 'utm_content') {

									$u->{$kkey} = trim($ddts[1]);
									$u->save();
								}

							}

						}

        				//$this->answer($chat_id, print_r($umSourcesArr, true), []);
        				//die;
        			}
				}

				if (strstr($text, '/unsubscribe')) {

        			if ($u) {
						$u->is_dispatch_subscribed = 0;
						$u->save();

						$this->answer($chat_id, "Вы успешно отписались от рассылок!", []);
					}

					die;
				}

				if (strstr($text, '/restart')) {

        			if ($user) {

        				$this->answer($chat_id, "Бот успешно перезагружен", []);

        				$allUserMessages = TgbotHistory::find()->where("bot_user_id=".$user->id)->all();
        				foreach ($allUserMessages as $m) {
        					$m->delete();
        				}

        				$allUserLogs = Logs::find()->where("bot_user_id=".$user->id)->all();
        				foreach ($allUserLogs as $m) {
        					$m->delete();
        				}

        				if ($u)
	        				$u->delete();

						$user->delete();

						die;
					}
				}


        		if (empty($text) && isset($data['message']['caption'])) {
        			$text = $data['message']['caption'];
        		}

        		if (empty($text))
    				$text = ' ';

        		//$text = Func::encodeEmoji($text);

                $h = new TgbotHistory();
				$h->bot_user_id = $user->id;
				$h->is_bot = 0;
				$h->in_data = $text;

				$h->tg_api_id = $data['message']['message_id'];

				$h->save();

				$userSavedHistoryId = $h->id;


				if ($text == 'test2') {

					$this->answer($chat_id, "TEST TEST", [["Не зарегистрирована и не знаю о ней"], ["Не зарегистрирована", "Зарегистрирована"]], 'name');
					die;

					//$this->sendTypingAction($chat_id);
					//die;

					//$this->answerHTML($chat_id, "<b><u>Здравствуйте</u></b>, можете перейти <a href='https://ya.ru'><u>по ссылке</u></a>", [['Кнопка 1', 'Кнопка 2', 'Кнопка 3', 'Кнопка 4', 'Кнопка 5', 'Кнопка 6']], '', '', $h->tg_api_id);

					$this->answerMarkdown($chat_id, "Сегодня в Москве ожидается тёплая погода, температура около 20°C\. ☀️ Ветер южный, а относительная влажность около 33\%\. В целом, обещают ясное небо без осадков\. 🌤 Если хочешь узнать больше, ты можешь проверить [прогноз на Яндекс](https://yandex\.ru\/pogoda\/moscow) или [на Gismeteo](https://www\.gismeteo\.ru\/weather\-moscow\-4368\/19\-day\/)\.

Ты планируешь что\-то особенное на сегодня?", []);

		            die;

				}




				/*{

					$aiBot = new AIBot();

		 			$response = $aiBot->sendMessage([
	 				'id_user' => $u->id,
	 					'date' => time(),
						'bot_user_id' => $user->id,
	 					'bot_history_id' => $userSavedHistoryId,
						'is_keyboard' => false,
						'text' => $text
		 			]);

		            die;

				}*/




				$text_lower = mb_strtolower($text, 'UTF-8');


				$tt = new TelegramBotQuiz1();
               	$ret = $tt->processAnswer($chat_id, trim($text), $lastMessageFromBot, $user, $u);




				if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'name') {

					$name = trim($text);

               		if (empty($name)) {
               			$this->answer($chat_id, "Пожалуйста, введи своё имя", [], 'name');
			            die;
               		}

               		$u->name = $name;
               		$u->save();
               		
					$ans = "Ого, ".trim($u->name)."! С таким именем можно хоть в супергерои — коротко, звучно, надёжно. Ты уже спас кого-то сегодня? Нет? Ну тогда начнём с тебя — от скуки и серых будней. Я подготовилась 😉";

               		$this->answer($chat_id, $ans, []);

               		sleep(3);

               		$this->answer($chat_id, trim($u->name).", осталось совсем чуть-чуть. Подтверди, что тебе есть 18 лет.", [["Мне есть 18 лет"]], 'age18');
    		        die;

               		
				}


				if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'agree1') {

					if ($text == 'Да, согласен(на)') {
						$this->answer($chat_id, " Пожалуйста, ознакомься и подтверди согласие на рекламную рассылку 💌✨ - https://avon-storage.ru/email_marketing_agreement.pdf", [["Нет, не согласен(на)", "Да, согласен(на)"]], 'agree2');
			            die;
					}


					$this->answer($chat_id, "К сожалению, ты не можешь продолжить без предоставления согласия на обработку персональных данных ⚠️

Ознакомиться с согласием на обработку персональных данных можно по ссылке - https://avon-storage.ru/policies.pdf", [["Нет, не согласен(на)", "Да, согласен(на)"]], 'agree1');
    		        die;
				}

				if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'agree2') {

					if ($text == 'Да, согласен(на)') {

						$u->ads_dispatch = 1;
						$u->save();

					} else {

						$u->ads_dispatch = 0;
						$u->save();
					}


					$this->answer($chat_id, "Отлично! А как тебя зовут? 😊💬", [], 'name');
    		        die;
				}

				if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'age18') {

					$text = trim($text);

					if ($text != "Мне есть 18 лет") {

						$this->answer($chat_id, trim($u->name).", осталось совсем чуть-чуть. Подтверди, что тебе есть 18 лет.", [["Мне есть 18 лет"]], 'age18');
	    		        die;
					}


               		$this->answer($chat_id, "Спасибо за доверие! 🤗 Я расскажу тебе много интересного и полезного!📚🎉", []);

               		sleep(3);

               		$this->answer($chat_id, "А ты зарегистрирован в программе AVON, где можешь получать приятные бонусы и полезный бьюти-контент? 🎁😉📲", [["Зарегистрирован", "Не зарегистрирован"]], 'is_registered');
               		die;
				}








				if (empty(trim($u->name))) {

					$this->answer($chat_id, "Я — Косметичер 💄 Буду тебе рассказывать новости, лайфхаки,  делиться с тобой бьюти-новинками ✨

Вот что я умею:
💄 расскажу о продуктах Avon, программе привилегий, возможностях и бонусах;
✨ помогу подобрать образ для особенного дня или на каждый день;
📌 подскажу, куда обратиться в разных ситуациях;
💬 и просто поболтаю на любые темы.

Как со мной общаться?
Очень просто — пиши мне! Можно задавать вопросы или просто написать то, что хочешь найти — я всё пойму и отвечу 😉

С подробной информацией о политике конфиденциальности и правилах рекомендательных технологий можно по ссылкам 👀🔗

Политика конфиденциальность - https://avon-storage.ru/privacy_policy.pdf
Правила рекомендательных технологий - https://avon-storage.ru/recommendation_technology_policy.pdf

Чтобы продолжить, пожалуйста, ознакомься и подтверди согласие на обработку персональных данных - https://avon-storage.ru/policies.pdf", [["Нет, не согласен(на)", "Да, согласен(на)"]], 'agree1');
    		        die;
				}




				if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'is_registered') {

					$ans = "";

					if ($text == "Не зарегистрирована и не знаю о ней" || $text == "Не зарегистрирован и не знаю о ней") {
						
						$u->is_registered = 1;
						$u->save();


					} elseif ($text == "Не зарегистрирована" || $text == "Не зарегистрирован") {

						$u->is_registered = 2;
						$u->save();

						$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp";

		    			if ($u->referent_id > 0) {
    						$url = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp&referrer=".$u->referent_id;
		    			}

						$ans = "Я так рада, что ты с нами! 🌸
Хочешь, расскажу, как сделать твоё знакомство с нашей продукцией ещё интереснее и получить доступ ко всем привилегиям, бонусам и предложениям? 🎁

Ты уже успела получить доступ в личный кабинет?
👉 ".$url."

Если вдруг остались вопросы — пиши, я с радостью всё объясню 💬💖";

						$this->answer($chat_id, $ans, []);
						die;

					} elseif ($text == "Зарегистрирована" || $text == "Зарегистрирован") {

						$u->is_registered = 3;

						$u->register_tries_cnt = 10;

						$u->save();

						$ans = "🎉 Рада, что ты с нами!
Я помогу тебе быть в курсе новинок и подскажу всё самое интересное — от бьюти до йоги и мастер-классов. 🌟

Если тебе нужна помощь с выбором продукции AVON — всегда готова помочь! 💖

А ещё тебя ждут новые тренинги, персональные предложения и, конечно, любимые бьюти-бонусы! 🎁
Если появятся вопросы или просто захочется поболтать — я всегда рядом! 💬✨

✨ Хочешь узнать о новинках, зайти в личный кабинет или посмотреть обзоры и мастер-классы?
Жми кнопки под строкой ввода — или нажми ⌘, чтобы их показать. Я всё подскажу! 💖";

						$this->answer($chat_id, $ans, []);
						die;
					}
				}


				if ($u->is_registered == 0) {

					$this->answer($chat_id, "А ты зарегистрирован в программе AVON, где можешь получать приятные бонусы и полезный бьюти-контент? 🎁😉📲", [["Зарегистрирован", "Не зарегистрирован"]], 'is_registered');
    		        die;

				}



				if (!$u->is_intro_processed) {
					$u->is_intro_processed = 1;
					$u->save();
				}



				//$mainMenuText = "Привет, давай пообщаемся с ИИ ботом";

				//$mainMenuButtons = [["А", "Б"]];

	 			//$this->answer($chat_id, $mainMenuText, $mainMenuButtons);


	 			/////////////////////////





	 			if (stristr($text, 'зарегистр')) {

	 				//$this->answer($chat_id, "КУ КУ", []);
	   		        //die;

	   		        if ($u->register_tries_cnt < 3) {

	   		        	$u->register_tries_cnt = 5;
	   		        	$u->save();

	   		        	if ($u->referent_id > 0) {

	   		        		/*$text = trim($u->name).", если у тебя не осталось сомнений и вопросов - скорее регистрируйся! 📲✨

Ссылка 🔗 - https://avon.ru/pre-register-tp?referrer=".$u->referent_id."&utm_source=tg_cosmeteacher_bot_psp";*/


							$text = trim($u->name).", если у тебя не осталось сомнений и вопросов - скорее переходи в личный кабинет!🚀💻

Переходи в личный кабинет:

https://avon.ru/pre-register-tp?referrer=".$u->referent_id."&utm_source=tg_cosmeteacher_bot_psp

✨ Хочешь узнать о новинках, зайти в личный кабинет или посмотреть обзоры и мастер-классы?
Жми кнопки под строкой ввода — или нажми ⌘, чтобы их показать. Я всё подскажу! 💖";

							$this->answer($chat_id, $text, []);
	   				        die;

	   		        	} else {

	   		        		/*$text = trim($u->name).", если у тебя не осталось сомнений и вопросов - скорее регистрируйся!🚀

Ссылка 💻🔥 - https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp";*/

							$text = trim($u->name).", если у тебя не осталось сомнений и вопросов - скорее переходи в личный кабинет!🚀💻

Переходи в личный кабинет:

https://avon.ru/pre-register-tp?&utm_source=tg_cosmeteacher_bot_psp

✨ Хочешь узнать о новинках, зайти в личный кабинет или посмотреть обзоры и мастер-классы?
Жми кнопки под строкой ввода — или нажми ⌘, чтобы их показать. Я всё подскажу! 💖";

							$this->answer($chat_id, $text, []);
	   				        die;

	   		        	}
	   		        	
	   		        }

	 			} 

	 			if ($text == 'Новинки') {

	 				$s = Settings::find()->where(['alias' => 'news_text'])->one();

	 				$this->answerHTML($chat_id, $s->val, []);
			        die;
				}

				if ($text == 'Бьюти ТВ') {

	 				$s = Settings::find()->where(['alias' => 'video_text'])->one();

	 				$this->answerHTML($chat_id, $s->val, []);
			        die;
				}

				if ($text == 'Верификация') {

					$ans = '';

					if ($u->referent_id > 0) {
   		        		$ans = "https://avon.ru/pre-register-tp?referrer=".$u->referent_id."&utm_source=tg_cosmeteacher_bot_psp";
   		        	} else {
	   		        	$ans = "https://avon.ru/pre-register-tp?utm_source=tg_cosmeteacher_bot_psp";
	   		        }


	   		        $this->answer($chat_id, $ans, []);
			        die;
	 			}





	 			$userText = $text;

	 			$isKeyboard = false;


	 			if ($lastMessageFromBot && !empty(trim($lastMessageFromBot->buttons))) {

	 				$btns = explode(';', trim($lastMessageFromBot->buttons));

	 				//$this->logInput(print_r($btns, true).";;;; [".$text."]");

	 				if (in_array($text, $btns)) {
	 					$isKeyboard = true;
					} else {

						if (!empty($lastMessageFromBot->out_data)) {
							$nexText = "Воспользуйся одним из вариантов ответов, предложенных ниже. Если ты не видишь предложенные ответы, нажми на ⌘";

							$this->answer($chat_id, $nexText, []);

							$this->answer($chat_id, $lastMessageFromBot->in_data, [$btns], $lastMessageFromBot->action_url, $lastMessageFromBot->out_data, '', 0, 0);
		 					die;
						}

					}
	 			}

				//  { "id_user": 12345,  "date": 20.02.2025, "bot_user_id": 67890, "bot_history_id": 1234, "is_keyboard": False, "text": "Дезодорант } 



				if ($lastMessageGlobal && $lastMessageGlobal->is_bot == 0) {

					$answerVariants = [
"🌸 Минутку, подбираем самый красивый ответ!",
"🔍 Секундочку... сейчас всё узнаем 😉",
"☕️ Одну минуту — информация заваривается!",
"✨ Обрабатываем магию ответа...",
"📦 Пакуем ваш ответ, почти готово!",
"💄 Секундочку — подбираем бьюти-ответ для тебя...",
"✨ Чуть-чуть подождём — идёт магия красоты!",
"🌸 Скоро будет ответ — наводим глянец 😉",
"🛍 Уже в пути... как любимый заказ от Avon!",
"💬 Минутку! Собираем самое сияющее решение для тебя",
"💕 Ответ почти готов — как твой идеальный образ"
					];

					$ansIdx = random_int(0, count($answerVariants)-1);
    				$ans = $answerVariants[$ansIdx];

					$this->answer($chat_id, $ans, [], '', '', '', 0, 1);
	 				die;

				}




				//if ($lastMessageGlobal && $lastMessageGlobal->is_bot == 0) {
				 //
					// накапливаем юзерские сообщения

				//	$queued_messages_ids = trim($user->queued_messages_ids);
			//		$queued_messages_ids_arr = explode(';', $queued_messages_ids);
			//		$queued_messages_ids_arr_ = [];

			//		foreach ($queued_messages_ids_arr as $qa) {
			//			if (!empty($qa)) {
			//				$queued_messages_ids_arr_[] = $qa;
			//			}
			//		}
			  //
				//	$queued_messages_ids_arr_[] = $userSavedHistoryId;
				  //
			//		$user->queued_messages_ids = implode(';', $queued_messages_ids_arr_);
			//		$user->save();

			//	} else {

					$user->queued_messages_ids = '';
					$user->save();

					$aiBot = new AIBot();

		 			$response = $aiBot->sendMessage([
	 				'id_user' => $u->id,
	 					'date' => time(),
						'bot_user_id' => $user->id,
	 					'bot_history_id' => $userSavedHistoryId,
						'is_keyboard' => $isKeyboard,
						'text' => $userText
		 			]);

				//}

	 			$this->sendTypingAction($chat_id);


	 			//$this->answer($chat_id, $userText, [["А", "Б"]]);
	 			//die;
			}
  			
  			//$this->answer($chat_id, $mainMenuText, $mainMenuButtons);
            //die;
        }

        return true;
    }

    public function answer($tg_id, $text, $buttons, $action_url = '', $ai_bot_out_data = '', $markdownType = '', $reply_to_message_id = 0, $system_flag = 0, $replyMessageHistoryId = 0) {

    	$user = TgbotUser::find()->where(['tg_id' => $tg_id])->one();
    	if (!$user)
    		die;

    	$u = User::findOne($user->user_id);

    	$keyboard = [];

    	$btnsTexts = [];

    	if (!empty($buttons)) {

    		if (count($buttons) > 1) {

    			foreach ($buttons as $buttonsRow) {

    				$buttonsArr = [];

    				foreach ($buttonsRow as $button) {

	    				$buttonsArr[] = ['text' => $button];
	    				$btnsTexts[] = $button;

					}

					$keyboard[] = $buttonsArr;
    			}

    		} else {

	    		$buttons = $buttons[0];

    			if (count($buttons) > 3) {

    				$kk = ceil(count($buttons)/3);

	    			$buttonsArr = [];

	    			$idx = 0;
		    		$i = 0;

		    		foreach ($buttons as $button) {

	    				$buttonsArr[$idx][] = ['text' => $button];

	    				$btnsTexts[] = $button;

	    				$i++;

		    			if ($i >= 3) {
		    				$idx++;
	    					$i = 0;
	    				}
		    		}

		    		//$this->logInput(print_r($buttonsArr, true));

	    			$keyboard = $buttonsArr;

		    	} else {

		    		$keyboard1 = [];

		    		foreach ($buttons as $button) {
						$keyboard1[] = [
							'text' => $button,
						];

						$btnsTexts[] = $button;
		    		}

    				$keyboard = [ 0 => $keyboard1 ];
				}
			}

			$keyboard = [
				'keyboard' => $keyboard,
				'resize_keyboard' => true,
				'one_time_keyboard' => true,
			];

			$keyboard = json_encode($keyboard);
    	}

    	if ($u && count($buttons) < 1 && $u->is_intro_processed) {
    		/*$keyboard = [
    			'remove_keyboard' => true,
			];*/

			$keyboard = [
				'resize_keyboard' => true,
				'one_time_keyboard' => true,
				'keyboard' => [
					[
						['text'=>'Новинки'],
						['text'=>'Верификация'],
						['text'=>'Бьюти ТВ']
					]
				]
			];

			$keyboard = json_encode($keyboard);
    	}

    	//$this->logInput(print_r($keyboard, true));

    	$payload = [
			'chat_id' => $tg_id,
			'text' => $text,
			'reply_markup' => $keyboard,
			'disable_web_page_preview' => true,
		];

    	if ($markdownType == 'HTML') {
    		$payload['parse_mode'] = 'html';
    	} elseif ($markdownType == 'MARKDOWN') {
    		$payload['parse_mode'] = 'MarkdownV2';
    	}

    	if ($reply_to_message_id > 0) {
    		$payload['reply_to_message_id'] = $reply_to_message_id;
    	}

    	//$this->logInput(print_r($payload, true));

		$result = $this->botApiQuery("sendMessage", $payload);
		$resultJson = json_decode($result);

		//$this->logInput(print_r($resultJson, true));

		//$text = Func::encodeEmoji($text);

		if ($user) {
			$h = new TgbotHistory();
			$h->bot_user_id = $user->id;
			$h->is_bot = 1;
			$h->in_data = $text;
			$h->out_data = $result;

			$h->system_flag = $system_flag;

			$h->tg_out_data = $result;

			if (!empty($ai_bot_out_data))
				$h->out_data = $ai_bot_out_data;

			$h->action_url = $action_url;

			if (count($btnsTexts) > 0) {
				$btnsText = implode(';', $btnsTexts);

				$h->buttons = $btnsText;
			}


			//\Yii::info($replyMessageHistoryId, 'aibot');

			if ($replyMessageHistoryId > 0) {
				$h->reply_to_history_id = $replyMessageHistoryId;
			}

			if ($resultJson && isset($resultJson->result) && isset($resultJson->result->message_id)) {
				$h->tg_api_id = $resultJson->result->message_id;
			}

			$h->save();

			return $h->id;
		}

		return 0;
    }

    public function answerImage($imgUrl, $tg_id, $text, $buttons, $action_url = '', $ai_bot_out_data = '', $markdownType = '', $reply_to_message_id = 0, $system_flag = 0, $replyMessageHistoryId = 0) {

    	$user = TgbotUser::find()->where(['tg_id' => $tg_id])->one();
    	if (!$user)
    		die;

    	$u = User::findOne($user->user_id);

    	$keyboard = [];

    	$btnsTexts = [];

    	if (!empty($buttons)) {

    		if (count($buttons) > 1) {

    			foreach ($buttons as $buttonsRow) {

    				$buttonsArr = [];

    				foreach ($buttonsRow as $button) {

	    				$buttonsArr[] = ['text' => $button];
	    				$btnsTexts[] = $button;

					}

					$keyboard[] = $buttonsArr;
    			}

    		} else {

	    		$buttons = $buttons[0];

    			if (count($buttons) > 3) {

    				$kk = ceil(count($buttons)/3);

	    			$buttonsArr = [];

	    			$idx = 0;
		    		$i = 0;

		    		foreach ($buttons as $button) {

	    				$buttonsArr[$idx][] = ['text' => $button];

	    				$btnsTexts[] = $button;

	    				$i++;

		    			if ($i >= 3) {
		    				$idx++;
	    					$i = 0;
	    				}
		    		}

		    		//$this->logInput(print_r($buttonsArr, true));

	    			$keyboard = $buttonsArr;

		    	} else {

		    		$keyboard1 = [];

		    		foreach ($buttons as $button) {
						$keyboard1[] = [
							'text' => $button,
						];

						$btnsTexts[] = $button;
		    		}

    				$keyboard = [ 0 => $keyboard1 ];
				}
			}

			$keyboard = [
				'keyboard' => $keyboard,
				'resize_keyboard' => true,
				'one_time_keyboard' => true,
			];

			$keyboard = json_encode($keyboard);
    	}

    	if ($u && count($buttons) < 1 && $u->is_intro_processed) {
    		/*$keyboard = [
    			'remove_keyboard' => true,
			];*/

			$keyboard = [
				'resize_keyboard' => true,
				'one_time_keyboard' => true,
				'keyboard' => [
					[
						['text'=>'Новинки'],
						['text'=>'Верификация'],
						['text'=>'Бьюти ТВ']
					]
				]
			];

			$keyboard = json_encode($keyboard);
    	}

    	//$this->logInput(print_r($keyboard, true));

    	$payload = [
			'chat_id' => $tg_id,
			'caption' => $text,
			'reply_markup' => $keyboard,
			'disable_web_page_preview' => true,
			'photo' => $imgUrl,
		];


    	if ($markdownType == 'HTML') {
    		$payload['parse_mode'] = 'html';
    	} elseif ($markdownType == 'MARKDOWN') {
    		$payload['parse_mode'] = 'MarkdownV2';
    	}

    	if ($reply_to_message_id > 0) {
    		$payload['reply_to_message_id'] = $reply_to_message_id;
    	}

    	//$this->logInput(print_r($payload, true));

		$result = $this->botApiQuery("sendPhoto", $payload);
		$resultJson = json_decode($result);

		//$this->logInput(print_r($resultJson, true));

		//$text = Func::encodeEmoji($text);

		if ($user) {
			$h = new TgbotHistory();
			$h->bot_user_id = $user->id;
			$h->is_bot = 1;
			$h->in_data = $text;
			$h->out_data = $result;

			$h->system_flag = $system_flag;

			$h->tg_out_data = $result;

			if (!empty($ai_bot_out_data))
				$h->out_data = $ai_bot_out_data;

			$h->action_url = $action_url;

			if (count($btnsTexts) > 0) {
				$btnsText = implode(';', $btnsTexts);

				$h->buttons = $btnsText;
			}

			if ($replyMessageHistoryId > 0) {
				$h->reply_to_history_id = $replyMessageHistoryId;
			}

			if ($resultJson && isset($resultJson->result) && isset($resultJson->result->message_id)) {
				$h->tg_api_id = $resultJson->result->message_id;
			}

			$h->save();

			return $h->id;
		}

		return 0;
    }

    public function answerHTML($tg_id, $text, $buttons, $action_url = '', $ai_bot_out_data = '', $reply_to_message_id = 0, $replyMessageHistoryId = 0) {
    	return $this->answer($tg_id, $text, $buttons, $action_url, $ai_bot_out_data, 'HTML', $reply_to_message_id, 0, $replyMessageHistoryId);
    }    

    public function answerMARKDOWN($tg_id, $text, $buttons, $action_url = '', $ai_bot_out_data = '', $reply_to_message_id = 0, $replyMessageHistoryId = 0) {
    	return $this->answer($tg_id, $text, $buttons, $action_url, $ai_bot_out_data, 'MARKDOWN', $reply_to_message_id, 0, $replyMessageHistoryId);
    }

    public function sendTypingAction($tg_id) {

    	$user = TgbotUser::find()->where(['tg_id' => $tg_id])->one();

		$result = $this->botApiQuery("sendChatAction", [
			'chat_id' => $tg_id,
			'action' => 'typing',
		]);

    }


    public function deleteMessage($tg_id, $message_id)
	{
		$result = $this->botApiQuery("deleteMessage", [
			'chat_id' => $tg_id,
			'message_id' => $message_id,
		]);
	}

    public function answerToUser($tg_id, $text)
	{
		$user = TgbotUser::find()->where(['tg_id' => $tg_id])->one();

		$keyboard = [];

		$result = $this->botApiQuery("sendMessage", [
			'chat_id' => $tg_id,
			'text' => $text,
			'reply_markup' => $keyboard,
		]);

        //$text = Func::encodeEmoji($text);

        if ($user) {
			$h = new TgbotHistory();
			$h->bot_user_id = $user->id;
			$h->is_bot = 1;
			$h->in_data = $text;
			$h->out_data = $result;
			$h->action_url = '';
			$h->save();
		}
	}

    private function botApiQuery($method, $fields = array())
    {
        $ch = curl_init('https://api.telegram.org/bot' . $this->token . '/' . $method);
        curl_setopt_array($ch, array(
            CURLOPT_POST => count($fields),
            CURLOPT_POSTFIELDS => http_build_query($fields),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 10
        ));
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
}
?>