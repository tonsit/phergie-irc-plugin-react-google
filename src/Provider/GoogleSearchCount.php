<?php
/**
 * Web search result count provider for the Google plugin for Phergie (https://github.com/phergie/phergie-irc-bot-react)
 *
 * @link https://github.com/chrismou/phergie-irc-plugin-react-google for the canonical source repository
 * @copyright Copyright (c) 2016 Chris Chrisostomou (https://mou.me)
 * @license http://phergie.org/license New BSD License
 * @package Chrismou\Phergie\Plugin\Google
 */

namespace Chrismou\Phergie\Plugin\Google\Provider;

use Phergie\Irc\Plugin\React\Command\CommandEvent as Event;

/**
 * Provider class
 *
 * @category Phergie
 * @package Chrismou\Phergie\Plugin\Google\Provider
 */
class GoogleSearchCount extends GoogleSearch implements GoogleProviderInterface
{
    /**
     * Process the response (when the request is successful)
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

        $messages = [];

        $messages[] = sprintf(
            "%s results [ %s ]",
            (isset($json->cursor->estimatedResultCount)) ? $json->cursor->estimatedResultCount : "0",
            $json->cursor->moreResultsUrl
        );

        return $messages;
    }

    /**
     * Returns an array of lines for the help response
     *
     * @return array
     */
    public function getHelpLines()
    {
        return [
            'Usage: googlecount [search query]',
            '[search query] - the word or phrase you want to search for',
            'Instructs the bot to query Google and respond with the estimated result count'
        ];
    }
}
