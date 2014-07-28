<?php

$app->get('/hello2', function() use ($app) {
    echo '{"test_thing": "go now"}';
});

$authenticate = function ($app) {
    return function () use ($app) {
        
    	// Check there is a user id and email set
        if (isset($_SESSION['userId']) && isset($_SESSION['userEmail'])) {
        	$user = R::load('user', $_SESSION['userId']);

            if($user->id == 0 || $user->email !== $_SESSION['userEmail']) {
                $app->halt(401, 'Login Required.');
            }
        } else {
            $app->halt(401, 'Login Required.');
        }
    };
};

$app->group('/users', function () use ($app) {
    /**
     * Logs in
     */
    $app->post("/login", function () use ($app) {

        $loginData = json_decode($app->request->getBody());

        if($loginData) {
            // CHeck we have an email and password
            if(!isset($loginData->email)) {
                $app->halt('400', 'Email required.');
            }
            if(!isset($loginData->password)) {
                // $app->halt('400', 'Password required.');
            }
            $email = $loginData->email;
            // $password = $loginData->password;
        } else {
            if(!$app->request->post('email')) {
                $app->halt('400', 'Email required.');
            }
            // if(!$app->request->post('password')) {
                // $app->halt('400', 'Password required.');
            // }
            $email = $app->request->post('email');
            $password = $app->request->post('password');
        }

        $user = R::findOne( 'user', ' email = :email ', array(':email' => $email));

        // if($user->id != 0 && $user->password == hash('md5', $loginData->password)) {
        if($user && $user->id != 0) {
            $_SESSION['userId'] = $user->id;
            $_SESSION['userEmail'] = $user->email;
        } else {
            $app->halt('400', 'Incorrect email or password.');
        }
        echo json_encode($user->export(), JSON_NUMERIC_CHECK);
    });

    $app->post('/logout', function() use ($app) {
        unset($_SESSION['userId']);
    });
    $app->get('/logout', function() use ($app) {
        unset($_SESSION['userId']);
    });

    /**
     * Creates a new user
     */
    $app->post('/register', function() use ($app) {

        $sampleUserData = array(
            'firstName'     => 'Craig',
            'lastName'      => 'McNamara',
            'email'         => 'cmcnamara87@gmail.com',
        );

        $userData = json_decode($app->request->getBody());
        // $userData = $sampleUserData;

        $user = R::dispense('user');
        $user->import($userData);
        $user->password = md5($user->password);
        unset($user['password2']);
        R::store($user);

        $_SESSION['userId'] = $user->id;
        $_SESSION['userEmail'] = $user->email;

        echo json_encode($user->export(), JSON_NUMERIC_CHECK);
    });
});