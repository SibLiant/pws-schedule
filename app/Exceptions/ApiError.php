<?php namespace App\Exceptions;


use Illuminate\Http\JsonResponse;
use Exception;

class ApiError extends JsonResponse

{
    /**
     * Constructor.
     *
     * @param int    $httpStatusCode HTTP status code
     * @param mixed  $errorCode      Internal error code
     * @param string $errorTitle     Error description
     * @param array  $additionalAttrs
     */
    public function __construct($httpStatusCode, $errorCode, $errorTitle, array $additionalAttrs = array())
    {
        $data = [
            'errors' => [ array_merge(
                [
                    'status' => (string) $httpStatusCode,
                    'code'   => (string) $errorCode,
                    'title'  => (string) $errorTitle
                ],
                $additionalAttrs
            ) ]
        ];
        parent::__construct($data, $httpStatusCode);
    }
}

?>
