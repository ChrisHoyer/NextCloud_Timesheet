<?php

namespace OCA\Timesheet\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\Timesheet\Service\NotFoundException;


trait Errors {

	// Function uses a callback, if everything is ok just proceed otherwise HTTP 404
    protected function handleNotFound (Closure $callback) {
        try {
            return new DataResponse($callback());
        } catch(NotFoundException $e) {
            $message = ['message' => $e->getMessage()];
            return new DataResponse($message, Http::STATUS_NOT_FOUND);
        }
    }
	
	// Function for casting input data
    protected function handleInvalidData (Closure $callback) {
        try {
            return $callback();
        } catch(Exception $e) {
            $message = ['message' => $e->getMessage()];
            return new DataResponse($message, Http::STATUS_NOT_FOUND);
        }
    }

}