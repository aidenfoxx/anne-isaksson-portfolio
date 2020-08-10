<?php

return [
  /**
   * Portfolio.
   */
  'GET' => [
    '' => ['Phoxx\Controllers\PortfolioController' => 'index'],

    '_404_' => ['Phoxx\Controllers\PortfolioController' => 'pageNotFound'],

    '_500_' => ['Phoxx\Controllers\PortfolioController' => 'internalServerError'],

    '/auth' => ['Phoxx\Controllers\AuthController' => 'index'],

    '/account' => ['Phoxx\Controllers\AccountController' => 'index'],
  ],

  'POST' => [
    '/auth' => ['Phoxx\Controllers\AuthController' => 'auth'],

    '/account' => ['Phoxx\Controllers\AccountController' => 'actions'],
  ]
];
