<?php
/*
 * Wrapper for the Titter oAuth.
 * The library file is included and keys are set when the object is loaded.
 */
defined('SYSPATH') or die('No direct script access.');

require_once(APPPATH.'vendor/autotweet/autotweet/twitteroauth.php');

class IbTwitterApi extends TwitterOauth
{
	public function __construct($consumerKey = NULL, $consumerSecret = NULL, $oAuthToken = NULL, $oAuthSecret = NULL)
	{
		// If the keys are not passed in, default to the ones in the engine settings
		if (is_null($consumerKey))
		{
			$settings       = Settings::instance()->get();
			$enabled        = trim($settings['twitter_api_access']);
			$consumerKey    = trim($settings['twitter_api_consumer_key']);
			$consumerSecret = trim($settings['twitter_api_secret_consumer_key']);
			if ($enabled)
			{
				$oAuthToken  = trim($settings['twitter_api_access_token']);
				$oAuthSecret = trim($settings['twitter_api_secret_access_token']);
			}
			else
			{
				$oAuthToken  = NULL;
				$oAuthSecret = NULL;
			}
		}

		parent::__construct($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);
		$this->decode_json = TRUE;
	}

	// Alter the post function to shorten messages longer than 140 characters.
	function post($url, $parameters = array())
	{
		if (isset($parameters['status']) AND strlen($parameters['status']) > 140)
		{
			$parameters['status'] = substr($parameters['status'], 0, 140);
		}

		return parent::post($url, $parameters);
	}

	// Get Tweets, link items within the tweets and embed images
	function get_tweets()
	{
		$tweets = $this->get('statuses/user_timeline', array('count' => 15, 'include_rts' => true, 'include_entities' => true));

		if (isset($tweets->errors))
		{
			$message = '';
			foreach ($tweets->errors as $error)
			{
				$message .= "\n".$error->code.': '.$error->message;
			}

			Log::instance()->add(Log::ERROR, __('Error rendering Twitter feed.').$message)->write();
			return $tweets;
		}
		if($tweets)
		{
			foreach ($tweets as $tweet)
			{
				$text = $tweet->text;

				// Link URLs
				foreach ($tweet->entities->urls as $url) {
					$text = str_replace($url->url, '<a href="'.$url->expanded_url.'">'.$url->url.'</a>', $text);
				}

				// Link hash tags
				foreach ($tweet->entities->hashtags as $hashtag) {
					$text = str_replace('#'.$hashtag->text, '<a href="https://twitter.com/hashtag/'.$hashtag->text.'?src=hash">#'.$hashtag->text.'</a>', $text);
				}

				// Link mentions
				foreach ($tweet->entities->user_mentions as $mention) {
					$text = str_replace('@'.$mention->screen_name, '<a href="https://twitter.com/'.$mention->screen_name.'">@'.$mention->screen_name.'</a>', $text);
				}

				// Embed images
				if ( ! empty($tweet->entities->media))
				{
					foreach ($tweet->entities->media as $media)
					{
						if ($media->type == 'photo')
						{
							// If the site is served over SSL, do not embed the image, unless it is also served over SSL
							if (empty($_SERVER['HTTPS']) OR strpos($media->media_url, 'https://') === 0)
							{
								$text .= '<img src="'.$media->media_url.'" />';
							}
						}
					}
				}
				$tweet->expanded_text = nl2br($text);
			}
		}

		return $tweets;

	}

	// Used with the short tag, {twitter_api_feed-}, to embed a feed
	public static function embed_feed()
	{
		$apc_cache_key = 'twitter_api_data:' . $_SERVER['HTTP_HOST'];
        /**
         * Cache results from the Twitter API. Re-check every hour.
         * We want to avoid calling the API on every page load, so we don't exceed the rate limit
         **/

        // If alternative PHP caching has been set up and the cached data exists
        if (function_exists('apc_exists') AND apc_exists($apc_cache_key))
        {
            // Get the cached data
            $twitter_api_data = apc_fetch($apc_cache_key);
        }
        else
        {
            // Get the data from the API
            $twitter_api      = new IbTwitterApi();
            $twitter_api_data = array(
                'account' => $twitter_api->get('account/settings'),
                'tweets'  => $twitter_api->get_tweets()
            );

            if (function_exists('apc_exists'))
            {
                // Cache the data for one hour
                apc_store($apc_cache_key, $twitter_api_data, 60 * 60);
            }
        }

        $account = $twitter_api_data['account'];
        $tweets  = $twitter_api_data['tweets'];
		$errors  = array();

		if (isset($account->errors)) $errors = array_merge($errors, $account->errors);
		if (isset($tweets->errors))  $errors = array_merge($errors, $tweets->errors);

		$view        = View::factory('twitter_feed')
			->set('errors', $errors)
			->set('tweets', $tweets)
			->set('account', $account);

		return $view;
	}

}
