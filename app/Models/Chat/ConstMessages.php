<?php
/**
 * Created by PhpStorm.
 * User: mmhaq
 * Date: 9/11/19
 * Time: 6:10 PM
 */

namespace App\Models\Chat;

/**
 * TODO A message should always have at least 2 things
 * 1- Code e.g. A B C D E X Y Z i.e. A-E are alphabets and X-Z are numbers
 * 2- Description/Detail/Message
 */
class ConstMessages {
    const ERR = 'Error';
    const SUCCESS = 'Success';
    const ERR_INTERNAL_MONGO_DB = 'Error: Operation could not be performed on DB.';

    const NO_CONTENT_SUCCESS_MSG = 'Success';
    const ACCEPTED_RESPONSE_MSG = 'No actionable data in request';

    const BAD_REQUEST_INCOMPLETE_DATA_MSG = 'Request data not complete. Some attribute missing'; // TODO needs to be more descriptive.
    const UNAUTHORIZED_TOKEN_MSG = 'Invalid or empty auth token';
    const UNAUTHORIZED_USER_MSG = 'Invalid or empty user id';
    const UNAUTHORIZED_REQUEST_MSG = 'Invalid or empty auth token or user id';
    const AUTHORIZED_ERROR_MSG = 'Authorization could not be confirmed';

    const NOT_FOUND_CHANNELS_MEMBERS = 'No channel members found for channel: ';
    const NOT_FOUND_CHANNEL_FOR_GROUP_ID = 'Channel does not exist with group id : ';
    const NOT_FOUND_CHANNEL_FOR_USER = 'Channels do not exist for user id : ';
    const USER_ADDED_TO_CHANNEL = 'User is added to the channel [userId:channelId]';
    const MEMBER_ALREADY_EXISTS = 'Member already exists [memberId:channelId]';
    const UNABLE_TO_REMOVE_USER = 'Could not remove user.';
    const NOT_FOUND_USER = 'User not found';
    const NOT_FOUND_MEMBERS = 'No members found';
    const COULD_NOT_UPDATE_MEMBER = 'Could not update member';

    const COULD_NOT_BE_CREATED = 'Channel could not be created.';

    const INVALID_URL_PARAMS = 'Invalid mandatory params in URL';
    const PREFLIGHT_CALL_RESPONSE_OK = 'Server is communicating with options provided in headers etc.';
    const BAD_REQUEST_MISSING_ID = 'HTTP method requires ID';
    const UNAUTHORIZED_RESOURCE_ID = 'Not valid resource id';

    const BAD_REQUEST_REQUEST_CONTENT_INVALID = 'Empty body or invalid Content-Type in HTTP request';
    const FUNCTION_NOT_IMPLEMENTED = 'unimplemented';

    const INTERNAL_SERVER_ERROR = 'Something went wrong and the details can be known by debugging.';
    const NOT_FOUND_REQUESTED_RESOURCE = 'Requested resource not found';
    const COULD_NOT_ADD_MEMBER = 'Could not add member with given request data';
    const COULD_NOT_CREATE_CHANNEL = 'Could not create channel with given data';
    const INCREMENT_FAILED = 'Increment operation failed';
    const NOT_FOUND_CHANNEL = 'No channel found';
    const CHANNEL_NOT_UPDATED = 'Channel not updated';
    const USER_NOT_ADDED = 'User not added';
    const USER_NOT_UPDATED = 'User could not be updated';
    const CHANNEL_DELETE_SUCCESS = 'Channel deleted successfully';

    const ERR_PUSH_NOTIFICATION = 'Error on sending push notification';
    const WARN_NO_FCM_TOKEN = 'No Firebase Token found, unable to send notification';
    const FCM_NOTIFICATION_FAILURE = ' push notifications were not sent successfully';
    const WARN_FCM_TOKEN_STORAGE_LIMIT = 'FCM token save limit exceeded. Max number of allowed devices is: ';

    const ROW_STATUS_ACTIVE = 'A';
    const DISABLED = 'N';
}
