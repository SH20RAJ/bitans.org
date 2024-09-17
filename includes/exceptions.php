<?php

// SQL Exception (500)
class SQLException extends Exception
{
  public function __construct($message, $code = 500, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}

// Privacy Exception (400)
class PrivacyException extends Exception
{
  public function __construct($message, $code = 400, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}

// Validation Exception (400)
class ValidationException extends Exception
{
  public function __construct($message, $code = 400, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}

// BadRequest Exception (400)
class BadRequestException extends Exception
{
  public function __construct($message, $code = 400, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}

// Authorization Exception (403)
class AuthorizationException extends Exception
{
  public function __construct($message, $code = 403, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}

// No Data Exception (404)
class NoDataException extends Exception
{
  public function __construct($message, $code = 404, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}
