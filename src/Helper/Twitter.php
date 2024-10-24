<?php

namespace App\Helper;

use App\Constants;

class Twitter
{

    public static function fetchTweet(string $id): ?array
    {
        if (!$guestId = self::getGuestId()) return null;
        if (!$guestToken = self::activateGuestToken()) return null;
        if (!$tweet = self::queryTweet($id, $guestId, $guestToken)) return null;

        return self::tidyUpHairball($tweet);
    }

    private static function formatMedia(array $media): array
    {
        return [
            'type' => $media['type'],
            'url' => $media['url'],
            'media_url_https' => $media['media_url_https'],
            'variants' => $media['video_info']['variants'] ?? []
        ];
    }

    private static function tidyUpHairball(array $data): array
    {
        $post = $data['data']['tweetResult']['result'];
        $postContext = $post['birdwatch_pivot']['subtitle'] ?? null;
        $postUser = $post['core']['user_results']['result'];
        $postMedia = $post['legacy']['entities']['media'] ?? [];

        $formattedPostMedia = array_map(function ($media) {
            return self::formatMedia($media);
        }, $postMedia);

        $quotedPost = $data['data']['tweetResult']['result']['quoted_status_result']['result'] ?? null;
        $quotedPostContext = $quotedPost['birdwatch_pivot']['subtitle'] ?? null;
        $quotedPostUser = $quotedPost['core']['user_results']['result'] ?? null;
        $quotedPostMedia = $quotedPost['legacy']['entities']['media'] ?? [];

        $formattedQuotedPostMedia = array_map(function ($media) {
            return self::formatMedia($media);
        }, $quotedPostMedia);

        return [
            'post' => [
                'user' => [
                    'screen_name' => $postUser['legacy']['screen_name'],
                    'name' => $postUser['legacy']['name'],
                    'profile_image' => $postUser['legacy']['profile_image_url_https'],
                    'profile_banner' => $postUser['legacy']['profile_banner_url'],
                    'verified' => $postUser['legacy']['verified']
                ],
                'full_text' => $post['legacy']['full_text'],
                'media' => $formattedPostMedia,
                'context' => $postContext ? [
                    'text' => $postContext['text']
                ] : null
            ],
            'quoted_post' => $quotedPost ? [
                'user' => [
                    'screen_name' => $quotedPostUser['legacy']['screen_name'],
                    'name' => $quotedPostUser['legacy']['name'],
                    'profile_image' => $quotedPostUser['legacy']['profile_image_url_https'],
                    'profile_banner' => $quotedPostUser['legacy']['profile_banner_url'],
                    'verified' => $quotedPostUser['legacy']['verified']
                ],
                'full_text' => $quotedPost['legacy']['full_text'],
                'media' => $formattedQuotedPostMedia,
                'context' => $quotedPostContext ? [
                    'text' => $quotedPostContext['text']
                ] : null
            ] : null,
        ];
    }

    private static function queryTweet(string $id, string $guestId, string $guestToken): ?array
    {
        $variables = [
            'tweetId' => $id,
            'withCommunity' => false,
            'includePromotedContent' => false,
            'withVoice' => false
        ];

        $features = [
            'creator_subscriptions_tweet_preview_api_enabled' => true,
            'communities_web_enable_tweet_community_results_fetch' => true,
            'c9s_tweet_anatomy_moderator_badge_enabled' => true,
            'articles_preview_enabled' => true,
            'responsive_web_edit_tweet_api_enabled' => true,
            'graphql_is_translatable_rweb_tweet_is_translatable_enabled' => true,
            'view_counts_everywhere_api_enabled' => true,
            'longform_notetweets_consumption_enabled' => true,
            'responsive_web_twitter_article_tweet_consumption_enabled' => true,
            'tweet_awards_web_tipping_enabled' => true,
            'creator_subscriptions_quote_tweet_preview_enabled' => false,
            'freedom_of_speech_not_reach_fetch_enabled' => true,
            'standardized_nudges_misinfo' => true,
            'tweet_with_visibility_results_prefer_gql_limited_actions_policy_enabled' => true,
            'rweb_video_timestamps_enabled' => true,
            'longform_notetweets_rich_text_read_enabled' => true,
            'longform_notetweets_inline_media_enabled' => true,
            'rweb_tipjar_consumption_enabled' => true,
            'responsive_web_graphql_exclude_directive_enabled' => true,
            'verified_phone_label_enabled' => false,
            'responsive_web_graphql_skip_user_profile_image_extensions_enabled' => false,
            'responsive_web_graphql_timeline_navigation_enabled' => true,
            'responsive_web_enhance_cards_enabled' => false
        ];

        $fieldToggles = [
            'withArticleRichContentState' => true,
            'withArticlePlainText' => false,
            'withGrokAnalyze' => false,
            'withDisallowedReplyControls' => false
        ];

        $apiEndpoint = '/graphql/OoJd6A50cv8GsifjoOHGfg/TweetResultByRestId';
        $params = http_build_query([
            'variables' => json_encode($variables),
            'features' => json_encode($features),
            'fieldToggles' => json_encode($fieldToggles),
        ]);

        $url = sprintf('%s%s?%s', Constants::TWITTER_API_ROOT, $apiEndpoint, $params);


        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_COOKIE => "guest_id=v1%253A172960828195652243",
            CURLOPT_HTTPHEADER => [
                "Authorization:" . Constants::GUEST_BEARER_TOKEN,
                sprintf('Cookie:: guest_id=%s; night_mode=2; gt=%s', $guestId, $guestToken),
                "x-guest-token: " . $guestToken
            ],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private static function getGuestId(): ?string
    {
        $headers = array_merge([
            'accept' => '*/*',
            'accept-language' => 'en-GB,en-US;q=0.7,en;q=0.3',
            'accept-encoding' => 'gzip, deflate, br',
            'te' => 'trailers',
        ], Constants::BASE_HEADERS);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Constants::TWITTER_ROOT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, UserAgent::generate());

        $response = curl_exec($ch);
        curl_close($ch);

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
        $cookies = [];
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }

        return $cookies['guest_id'] ?? null;
    }

    private static function activateGuestToken(): ?string
    {
        $url = strtr('{root}/1.1/guest/activate.json', [
            '{root}' => Constants::TWITTER_API_ROOT,
        ]);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "Authorization: " . Constants::GUEST_BEARER_TOKEN,
                "User-Agent: " . UserAgent::generate()
            ],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);

        return $json['guest_token'] ?? null;
    }

}