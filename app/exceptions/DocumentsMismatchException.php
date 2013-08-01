<?php
class DocumentsMismatchException extends AppException {
	public function __construct($message = NULL, $previous = NULL, $extra = NULL) {
		if (is_null($message)) $message = 'Documents Mismatch!';
		parent::__construct($message, AppException::ERROR, $previous, $extra);
	}
}
