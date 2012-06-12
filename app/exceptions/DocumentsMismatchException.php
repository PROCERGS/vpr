<?php
class DocumentsMismatchException extends ErrorException {
	public function __construct($message = NULL, $code = NULL, $previous = NULL) {
		if (is_null($message)) $message = 'Documents Mismatch!';
		parent::__construct($message, $code, $previous);
	}
}
