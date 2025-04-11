<?php return <<< JSON
{
	"AM_ALLOWED_FILE_TYPES": "dmg, iso, rar, tar, zip, aiff, m4a, mp3, ogg, wav, ai, dxf, eps, gif, ico, jpg, jpeg, png, psd, svg, tga, tiff, webp, avi, flv, mov, mp4, mpeg, webm, css, js, json, md, pdf",

	"AM_CACHE_ENABLED": true,
	"AM_CACHE_LIFETIME": 43200,
	"AM_CACHE_MONITOR_DELAY": 120,
	
	"AM_DEBUG_ENABLED": false,

	"AM_OPEN_BASEDIR_ENABLED": true,

	"AM_DISK_QUOTA": 2048,
	
	"AM_FEED_ENABLED": true,
	"AM_FEED_FIELDS": "+hero, +main",
	
	"AM_MAIL_OBFUSCATION_ENABLED": true,

	"AM_PACKAGE_FILTER_REGEX": ".",

	"AM_PASSWORD_MIN_LENGTH": 8,
	"AM_PASSWORD_REQUIRED_CHARS": "@#%^~+=*$&! A-Z a-z 0-9"
}
JSON;
