<?php

function respondOK($message = null) {
    return response()->json(['message' => $message ?? 'OK']);
}

function respondError($code, $httpCode, $message = null) {
    $message = $message ?? \App\Constants\ErrorCodes::Message($code);
    return response()->json(['error' => ['code' => $code, 'message' => $message]], $httpCode);
}
