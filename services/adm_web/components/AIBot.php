<?php

namespace app\components;

class AIBot
{
	protected $api_url = 'http://localhost:8011';


	public function sendMessage($data) {

		\Yii::info(print_r($data, true), 'aibot');

		$response = $this->makeRequest('/webhook', [
			'data' => $data,
//			'log' => 1,
		]);

		return $response;
	}

	protected function initRequest($url, $options = array()) {

		$durl = $this->api_url.$url;

		$url = $durl;

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // error with open_basedir or safe mode
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

		if (isset($options['referer'])) {
			curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
		}

		if (!isset($options['headers'])) {
			$options['headers'] = [];
		}
		$options['headers'] = [
            'Accept: application/json',
            'Content-type: application/json',
        ];

		if (isset($options['headers'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
		}

		if (isset($options['query'])) {
			$url_parts = parse_url($url);
			if (isset($url_parts['query'])) {
				$query = $url_parts['query'];
				if (strlen($query) > 0) {
					$query .= '&';
				}
				$query .= http_build_query($options['query']);
				$url = str_replace($url_parts['query'], $query, $url);
			}
			else {
				$url_parts['query'] = $options['query'];
				$new_query = http_build_query($url_parts['query']);
				$url .= '?' . $new_query;
			}
		}

		if (isset($options['data'])) {

			$postdata = json_encode($options['data']);

			if (!empty($options['log']) && $options['log']) {
				//Yii::log($postdata, CLogger::LEVEL_INFO, 'application.modules.user');

				\Yii::info($postdata, 'aibot');
			}

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		} else {

			if (!empty($options['log']) && $options['log']) {
				//Yii::log($url, CLogger::LEVEL_INFO, 'application.modules.user');
			}
		}

		if (@$options['put'] == 1)
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		else if (@$options['delete'] == 1)
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

		curl_setopt($ch, CURLOPT_URL, $url);
		return $ch; 
	}

	public function makeRequest($url, $options = array(), $parseJson = true) {

		\Yii::info('START', 'aibot');

		$ch = $this->initRequest($url, $options);

		$result = curl_exec($ch);
		$headers = curl_getinfo($ch);

		/*print_r($headers);
		die;
		echo $result;
		die;*/

		$orig_result = $result;

		if (curl_errno($ch) > 0) {
			//throw new CHttpException(404, curl_error($ch).' ('.curl_errno($ch).')');

			\Yii::info('ERROR: '.curl_error($ch).' ('.curl_errno($ch).')', 'aibot');

			print_r(curl_error($ch));
			die;
		}

		curl_close($ch);

		if ($parseJson) {
			$result = $this->parseJson($result);
		}

		if (!empty($options['log']) && $options['log']) {
			//Yii::log($url.PHP_EOL.$orig_result.PHP_EOL.print_r($result, true), CLogger::LEVEL_INFO, 'application.modules.user');

			\Yii::info($this->api_url.$url.PHP_EOL.$orig_result.PHP_EOL.print_r($result, true), 'aibot');
		}

		\Yii::info('END', 'aibot');

		return $result;
	}

	protected function parseJson($response) {
		$result = json_decode($response);
		return $result;
	}
}