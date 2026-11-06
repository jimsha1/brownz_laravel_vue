<?php

function url_safe_base64_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function url_safe_base64_decode($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function url_history($url, $state = null, $title = '')
{
	return [
		'state' => $state ?? ['id' => time()],
		'title' => $title,
		'url' => $url,
	];
}

function avatar_url($image, $width = null, $height = null, $zc = null)
{
    return route('user.avatar.url', [
        'image' => trim($image),
        'w' => (int) $width,
        'h' => (int) $height,
        'zc' => (int) $zc,
    ]);
}
