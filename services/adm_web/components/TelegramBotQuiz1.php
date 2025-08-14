<?php

namespace app\components;

use Yii;
use yii\helpers\Url;

use app\models\TgbotUser;
use app\models\TgbotHistory;
use app\models\User;
use app\models\Settings;
use app\models\Func;
use app\models\UserQuiz;

use app\components\TelegramBot;

class TelegramBotQuiz1
{
	function processAnswer($chat_id, $text, $lastMessageFromBot, $user, $u)
    {
		$text_lower = mb_strtolower($text, 'UTF-8');
    	
    	$tb = new TelegramBot();

    	if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'quiz_1_question_1') {

			if (empty($text)) {
				$tb->answer($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$answer = (int)$text;

			if ($answer < 1 || $answer > 5) {

				$tb->answer($chat_id, "Неверный вариант ответа. Попробуй снова!", []);

				$tb->answer($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$userQuiz = UserQuiz::find()->where(['user_id'=>$u->id])->one();
			if (!$userQuiz) {
				$userQuiz = new UserQuiz();
				$userQuiz->user_id = $u->id;
			}

			$userQuiz->answer1 = $text;
			$userQuiz->save();


			$quizText = "Выходной мечты — это…

1. 🏛 Музей и кофе с видом
2. 🌿 Пикник и кино под открытым небом
3. 🕯 Дом, плед, свечи и тишина
4. 🚶‍♀️ Прогулка по городу без маршрута
5. 🪩 Танцы до утра и фотосессия";

			$tb->answer($chat_id, $quizText, [["1", "2", "3", "4", "5"]], 'quiz_1_question_2');
			die;
		}

		if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'quiz_1_question_2') {

			if (empty($text)) {
				$tb->answer($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$answer = (int)$text;

			if ($answer < 1 || $answer > 5) {

				$tb->answer($chat_id, "Неверный вариант ответа. Попробуй снова!", []);

				$tb->answer($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$userQuiz = UserQuiz::find()->where(['user_id'=>$u->id])->one();
			if (!$userQuiz) {
				$userQuiz = new UserQuiz();
				$userQuiz->user_id = $u->id;
			}

			$userQuiz->answer2 = $text;
			$userQuiz->save();


			$quizText = "Что для тебя значит помада?

1. 💄 Способ выразить себя — даже без слов
2. 💋 Деталь, без которой «что-то не то»
3. 👑 Средство защиты — и уверенности
4. 🎨 Маленький штрих, который завершает образ
5. 💥 Настроение: захотела — и примерила новое «я»";

			$tb->answer($chat_id, $quizText, [["1", "2", "3", "4", "5"]], 'quiz_1_question_3');
			die;
		}

		if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'quiz_1_question_3') {

			if (empty($text)) {
				$tb->answer($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$answer = (int)$text;

			if ($answer < 1 || $answer > 5) {

				$tb->answer($chat_id, "Неверный вариант ответа. Попробуй снова!", []);

				$tb->answer($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$userQuiz = UserQuiz::find()->where(['user_id'=>$u->id])->one();
			if (!$userQuiz) {
				$userQuiz = new UserQuiz();
				$userQuiz->user_id = $u->id;
			}

			$userQuiz->answer3 = $text;
			$userQuiz->save();


			$quizText = "Какой фразой тебя бы описали друзья?

1. 👑 У неё всегда безупречный вкус
2. 🌸 С ней рядом спокойно и тепло
3. 🧲 В ней есть что-то притягивающее и необъяснимое
4. 🌿 Она умеет не спешить и видеть красоту в простом
5. 💥 Ого, она опять придумала что-то безумное!";

			$tb->answer($chat_id, $quizText, [["1", "2", "3", "4", "5"]], 'quiz_1_question_4');
			die;
		}

		if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'quiz_1_question_4') {

			if (empty($text)) {
				$tb->answer($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$answer = (int)$text;

			if ($answer < 1 || $answer > 5) {

				$tb->answer($chat_id, "Неверный вариант ответа. Попробуй снова!", []);

				$tb->answer($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$userQuiz = UserQuiz::find()->where(['user_id'=>$u->id])->one();
			if (!$userQuiz) {
				$userQuiz = new UserQuiz();
				$userQuiz->user_id = $u->id;
			}

			$userQuiz->answer4 = $text;
			$userQuiz->save();


			$quizText = "Если бы ты была ароматом, то это был бы…

1. 💗 <b>Пудровая классика — Today.</b> Цветочный, светлый и дорогой. 
2. 🌸 <b>Цветочный полдень — Incandessence.</b> Солнечный, яркий, с лёгким шлейфом жасмина. 
3. 🌙 <b>Тёплая древесная ночь — Far Away Beyond.</b> Глубокий, тёплый и стойкий.
4. 🔥 <b>Искра страсти — Attraction.</b> Смелый, чувственный и приятгательный.
5. ✨ <b>Внутренний стержень — Eve Confidence.</b> Нежный, фруктово-цветочный, с шлейфом силы.";

			$tb->answerHTML($chat_id, $quizText, [["1", "2", "3", "4", "5"]], 'quiz_1_question_5');
			die;
		}

		if ($lastMessageFromBot && $lastMessageFromBot->action_url == 'quiz_1_question_5') {

			if (empty($text)) {
				$tb->answerHTML($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$answer = (int)$text;

			if ($answer < 1 || $answer > 5) {

				$tb->answer($chat_id, "Неверный вариант ответа. Попробуй снова!", []);

				$tb->answerHTML($chat_id, $lastMessageFromBot->in_data, [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
				die;
			}

			$userQuiz = UserQuiz::find()->where(['user_id'=>$u->id])->one();
			if (!$userQuiz) {
				$userQuiz = new UserQuiz();
				$userQuiz->user_id = $u->id;
			}

			$userQuiz->answer5 = $text;
			$userQuiz->save();

			$answers = [
				'1' => 0,
				'2' => 0,
				'3' => 0,
				'4' => 0,
				'5' => 0
			];


			for ($i = 1; $i <= 5; $i++) {

				$k = $userQuiz->{'answer'.$i};

				$answers[(string)$k]++;
			}

			asort($answers);

			$k = (int)array_key_last($answers);

			$userQuiz->result1 = (string)$k;
			$userQuiz->save();

			//$tb->answerHTML($chat_id, print_r($userQuiz->getErrors(), true), [["1", "2", "3", "4", "5"]], $lastMessageFromBot->action_url);
			//die;

			$quizText = "";

			if ($k == 1) {

				$quizText = "💄 <b>Ultra Classic</b>

<b>Ты — про вкус, баланс и уверенность.</b>

Сдержанная снаружи, яркая внутри. Знаешь, когда сиять, а когда просто быть — и этого достаточно.

💄 <b>Твой оттенок:</b> Розовый виноград и Красное превосходство

👉 Смотреть на сайте: <a href='https://avon.ru/products/625470?utm_source=quiz'>https://avon.ru/products/625470?utm_source=quiz</a>

🎁 Получи свою помаду в <b>брендзоне Avon</b> — она находится <b>слева от главного входа в парк ВДНХ</b>.

Покажи результат теста промоутеру — и наслаждайся своим оттенком! 💄";

			} elseif ($k == 2) {

				$quizText = "💗 <b>Ultra Tender</b>

<b>Ты создаёшь пространство, где хочется остаться.</b>

С тобой уютно, вкусно и по-настоящему. Все подруги мечтают быть хотя бы чуть-чуть похожими на тебя.

💄 <b>Твой оттенок:</b> Нежно-розовый и Калифорнийские грёзы

👉 Смотреть на сайте: <a href='https://avon.ru/products/625506?utm_source=quiz'>https://avon.ru/products/625506?utm_source=quiz</a> и <a href='https://avon.ru/products/625515?utm_source=quiz'>https://avon.ru/products/625515?utm_source=quiz</a>

🎁 <b>Где получить свою помаду?</b>

В <b>брендзоне Avon</b> на ВДНХ — она находится <b>слева от главного входа в парк</b>.

Просто <b>покажи результат теста промоутеру</b> — и забирай свой бьюти-оттенок 💄";

			} elseif ($k == 3) {

				$quizText = "🖤 <b>Ultra Drama</b>

<b>Ты яркая. Эффектная. С тобой не бывает «нейтрально».</b>

Ты либо вдохновляешь, либо сжигаешь мосты. И оба варианта — потрясающе красивые.

💄 <b>Твой оттенок:</b> Черешневый шик и Лиловый

👉 Смотреть на сайте: <a href='https://avon.ru/products/625469?utm_source=quiz'>https://avon.ru/products/625469?utm_source=quiz</a>

🎁 <b>Где получить свою помаду?</b>

В <b>брендзоне Avon</b> на ВДНХ — она находится <b>слева от главного входа в парк</b>.

Просто <b>покажи результат теста промоутеру</b> — и забирай свой бьюти-оттенок 💄";

			} elseif ($k == 4) {

				$quizText = "🌿 <b>Ultra Nude</b>

<b>Ты про лёгкость и вкус к простому.</b>

Ни капли показной яркости — только внутренняя сила. Помада у тебя всегда «как будто без неё», но все спрашивают, какая именно.

💄 <b>Твой оттенок:</b> Кофе глясе

👉 Смотреть на сайте: <a href='https://avon.ru/catalog/makiyazh/guby/pomada?brands=%5B%221849ab35-70df-4523-a1aa-332dd6fc3e23%22%5D&utm_source=quiz'>https://avon.ru/catalog/makiyazh/guby/pomada?brands=%5B%221849ab35-70df-4523-a1aa-332dd6fc3e23%22%5D&utm_source=quiz</a>

🎁 <b>Где получить свою помаду?</b>

В <b>брендзоне Avon</b> на ВДНХ — она находится <b>слева от главного входа в парк</b>.

Просто <b>покажи результат теста промоутеру</b> — и забирай свой бьюти-оттенок 💄";

			} elseif ($k == 5) {

				$quizText = "✨ <b>Ultra Energy</b>

<b>Ты заряжаешь, врываешься и не боишься быть первой.</b>

И помада у тебя такая же: вау, яркая, и с характером. С тобой точно не будет скучно.

💄 <b>Твой оттенок:</b> Ультрарозовый

👉 Смотреть на сайте: <a href='https://avon.ru/catalog/makiyazh/guby/pomada?brands=%5B%221849ab35-70df-4523-a1aa-332dd6fc3e23%22%5D&utm_source=quiz'>https://avon.ru/catalog/makiyazh/guby/pomada?brands=%5B%221849ab35-70df-4523-a1aa-332dd6fc3e23%22%5D&utm_source=quiz</a>

🎁 <b>Где получить свою помаду?</b>

В <b>брендзоне Avon</b> на ВДНХ — она находится <b>слева от главного входа в парк</b>.

Просто <b>покажи результат теста промоутеру</b> — и забирай свой бьюти-оттенок 💄";

			}

			$tb->answerHTML($chat_id, $quizText, []);
			die;
		}
    }
}
?>