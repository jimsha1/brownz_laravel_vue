<?php

function config_locale($key)
{
	$key_dotted = 'business.' . str_replace('_', '.', $key);
	$key_arabic = str_replace('.english', '.arabic', $key_dotted);

	if (app()->getLocale() === 'ar') {
		$value = config($key_arabic);

		if (!empty($value)) {
			return $value;
		}
	}

	return config($key_dotted);
}
