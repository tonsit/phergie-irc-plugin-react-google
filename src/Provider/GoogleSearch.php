<?php
/**
 * Web search provider for the Google plugin for Phergie (https://github.com/phergie/phergie-irc-bot-react)
 *
 * @link https://github.com/chrismou/phergie-irc-plugin-react-google for the canonical source repository
 * @copyright Copyright (c) 2014 Chris Chrisostomou (http://mou.me)
 * @license http://phergie.org/license New BSD License
 * @package Chrismou\Phergie\Plugin\Google
 */

namespace Chrismou\Phergie\Plugin\Google\Provider;

use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Plugin\React\Command\CommandEvent as Event;

/**
 * Provider class
 *
 * @category Phergie
 * @package Chrismou\Phergie\Plugin\Google\Provider
 */
class GoogleSearch implements GoogleProviderInterface
{

	protected $apiUrl = 'http://ajax.googleapis.com/ajax/services/search/web';

	/**
	 * Validate the provided parameters
	 *
	 * @param array $params
	 * @return true|false
	 */
	public function validateParams(array $params)
	{
		return (count($params)) ? true : false;
	}

	/**
	 * Get the url for the API request
	 *
	 * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
	 * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
	 * @return string
	 */
	public function getApiRequestUrl(Event $event)
	{
		$params = $event->getCustomParams();
		$query = trim(implode(" ", $params));

		$querystringParams = array(
			'v' => '1.0',
			'q' => $query
		);

		return sprintf("%s?%s", $this->apiUrl, http_build_query($querystringParams));
	}

	/**
	 * Process the response (when the request is successful) and return an array of lines to send back to IRC
	 *
	 * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
	 * @param string $apiResponse
	 *
	 * @return array
	 */
	public function getSuccessLines(Event $event, $apiResponse)
	{
		$json = json_decode($apiResponse);
		$json = $json->responseData;

		$messages = array();

		if ($json->cursor->estimatedResultCount > 0) {
			$messages[] = sprintf(
				"%s [ %s ]",
				$json->results[0]->titleNoFormatting,
				$json->results[0]->url
			);
			$messages[] = sprintf("More results: %s", $json->cursor->moreResultsUrl);

		} else {
			$messages[] = 'No results for this query';
		}

		return $messages;
	}

	/**
	 * Process the response (when the request fails)
	 *
	 * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
	 * @param string  $apiError
	 *
	 * @return array
	 */
	public function getFailureLines(Event $event, $apiError)
	{
		//$queue->ircPrivmsg($event->getSource(), "something went wrong... ಠ_ಠ");
		return array("something went wrong... ಠ_ಠ");
	}

	/**
	 * Returns an array of lines for the help response
	 *
	 * @return array
	 */
	public function getHelpLines()
	{
		return array(
			'Usage: google [search query]',
			'[search query] - the word or phrase you want to search for',
			'Instructs the bot to query Google and respond with the top result'
		);
	}

}