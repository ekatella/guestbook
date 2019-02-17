<?php

return [
		'POST' => [
			'messages' => [
				'controller' => '/guestbook/app/Messages/Messages',
				'action' => 'add',
				'args' => ''
			],
			'login' => [
				'controller' => '/guestbook/app/Users/Users',
				'action' => 'login',
				'args' => ''
			],
			'register' => [
				'controller' => '/guestbook/app/Users/Users',
				'action' => 'register',
				'args' => ''
			],
			'logout' => [
				'controller' => '/guestbook/app/Users/Users',
				'action' => 'logout',
				'args' => ''
			]
		],
		'GET' => [
			'messages' => [
				'controller' => '/guestbook/app/Messages/Messages',
				'action' => 'getAll',
				'args' => ''
			],
			'' => [
				'controller' => '/guestbook/app/Main/Main',
				'action' => 'index',
				'args' => ''
			]
		],
		'PUT' => [
			'messages/([0-9]+)' => [
				'controller' => '/guestbook/app/Messages/Messages',
				'action' => 'update',
				'args' => '$1'
			]
		],
		'DELETE' => [
			'messages/([0-9]+)' => [
				'controller' => '/guestbook/app/Messages/Messages',
				'action' => 'delete',
				'args' => '$1'
			]
		],
];
